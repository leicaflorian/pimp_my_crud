<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Traits\WithStubHandling;
use App\Traits\WithTableAnalyzer;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;

class MakeCrudViewsCommand extends Command {
  use WithStubHandling, WithTableAnalyzer;
  
  /**
   * Filesystem instance
   *
   * @var Filesystem
   */
  protected Filesystem $files;
  
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'pmc:views {resource}';
  
  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create all crud views for the specified resource';
  
  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle() {// For each configured file
    foreach ($this->getFilesToCreate() as $fileEntry) {
      $file = $fileEntry["src"];
      // get destination folder path
      $path = $this->getSourceFilePath($fileEntry["dest"], ["{resource}" => $this->argument("resource")]);
    
      // Create destination folder
      $this->makeDirectory(dirname($path));
    
      // Create the file with its content
      $contents = $this->getCompiledFile($file, $fileEntry["variables"]);
    
      // If file not exists, saves it, otherwise inform that file already exists
      $this->storeFile($path, $contents);
    }
  
    return Command::SUCCESS;
    
  }
  
  /**
   * Get the files to create
   *
   * @return array
   */
  private function getFilesToCreate(): array {
    $resource         = $this->argument('resource');
    $resourceSingular = Pluralizer::singular($resource);
    $modelName        = $this->getSingularClassName($resource);
    $model            = "App\\Models\\{$modelName}";
    
    if ( !class_exists($model)) {
      $this->error("Model {$model} does not exists");
      
      return [];
    }
    
    $columns = $this->getTabelColumns((new $model));
    
    return [
      $this->getIndexView($resource, $resourceSingular, $columns),
      $this->getEditView($resource, $resourceSingular, $columns),
      $this->getCreateView($resource, $resourceSingular, $columns),
      $this->getShowView($resource, $resourceSingular, $columns),
    ];
  }
  
  public function getIndexView($resource, $resourceSingular, $columns) {
    return [
      "src"       => "resources/views/index.blade.php",
      "dest"      => "resources/views/{resource}/index.blade.php",
      "variables" => [
        "resource"         => $resource,
        "resourceSingular" => $resourceSingular,
        "pageTitle"        => "List of {$this->argument('resource')}",
        "columns"          => $columns->map(function ($column) {
          $colName = ucfirst(Str::replace('_', ' ', $column['name']));
          
          return "<th>{$colName}</th>";
        })->implode("\n              "),
        "rows"             => $columns->map(fn($column) => "<td>{{ \${$resourceSingular}->{$column["name"]} }}</td>")
          ->implode("\n                ")
      ],
    ];
  }
  
  public function getEditView($resource, $resourceSingular, $columns) {
    return [
      "src"       => "resources/views/edit.blade.php",
      "dest"      => "resources/views/{resource}/edit.blade.php",
      "variables" => [
        "resource"         => $resource,
        "resourceSingular" => $resourceSingular,
        "pageTitle"        => ucfirst($resourceSingular) . " #\${$resourceSingular}->id | Edit",
        "title"        => ucfirst($resourceSingular) . " #{{ \${$resourceSingular}->id }} | Edit",
        "formInputs"       => $this->getUpsertForm($resourceSingular, $columns, true),
      ]
    ];
  }
  
  public function getCreateView($resource, $resourceSingular, $columns) {
    return [
      "src"       => "resources/views/create.blade.php",
      "dest"      => "resources/views/{resource}/create.blade.php",
      "variables" => [
        "resource"         => $resource,
        "resourceSingular" => $resourceSingular,
        "pageTitle"        => ucfirst($resource) . " | Create",
        "formInputs"       => $this->getUpsertForm($resourceSingular, $columns),
      ]
    ];
  }
  
  public function getShowView($resource, $resourceSingular, $columns) {
    return [
      "src"       => "resources/views/show.blade.php",
      "dest"      => "resources/views/{resource}/show.blade.php",
      "variables" => [
        "resource"         => $resource,
        "resourceSingular" => $resourceSingular,
        "pageTitle"        => ucfirst($resourceSingular) . " #\${$resourceSingular}->id",
        "title"        => ucfirst($resourceSingular) . " #{{ \${$resourceSingular}->id }}",
        "formInputs"       => $columns->reduce(function ($acc, $column) use ($resourceSingular) {
          $colName = ucfirst(Str::replace('_', ' ', $column['name']));
  
          $acc->push("<div><strong>{$colName}:</strong> {{ \${$resourceSingular}->{$column['name']} }}</div>");
  
          return $acc;
        }, collect())->implode("\n\n          "),
      ]
    ];
  }
  
  public function getUpsertForm($resourceSingular, $columns, $isUpdate = false) {
    return $columns->reduce(function ($acc, $column) use ($resourceSingular, $isUpdate) {
      $colName = ucfirst(Str::replace('_', ' ', $column['name']));
      
      if ( !$column["fillable"]) {
        return $acc;
      }
      
      $value = $isUpdate ? "{{ old('{$column['name']}', \${$resourceSingular}->{$column['name']}) }}" : "{{ old('{$column['name']}') }}";
      
      $acc->push("<div class=\"mb-3\">
            <label class=\"form-label\" for=\"input_{$column['name']}\">$colName</label>
            <input type=\"text\"
                   class=\"form-control @error('{$column['name']}') is-invalid @enderror\"
                   name=\"{$column['name']}\"
                   id=\"input_{$column['name']}\"
                   value=\"$value\">
            @error('{$column['name']}')
            <div class=\"invalid-feedback\">{{ \$message }}</div>
            @enderror
          </div>");
      
      return $acc;
    }, collect())->implode("\n\n          ");
  }
}

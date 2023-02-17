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
  protected $signature = 'pmc:views {resource : Name of the resource, in lowercase, plural}
                          {--force : Overwrite existing files}
                          {--only= : Only create the specified views, separated by comma. Available values are "index", "edit", "create" and "show"}
                          {--wysiwyg : Add wysiwyg editor to the form}';
  
  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create all CRUD views for the specified resource.
  Example:
    php artisan pmc:views posts
    php artisan pmc:views posts --only=index,edit
    php artisan pmc:views posts --only=index,edit --force
    php artisan pmc:views posts --wysiwyg --force
    ';
  
  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle() {
    $force = $this->option("force");
    $only  = $this->option("only");
    
    if ($only) {
      $this->info("Only \"{$only}\" views will be created");
    }
    
    // For each configured file
    foreach ($this->getFilesToCreate($only) as $fileEntry) {
      $file = $fileEntry["src"];
      // get destination folder path
      $path = $this->getSourceFilePath($fileEntry["dest"], ["{resource}" => $this->argument("resource")]);
      
      // Create destination folder
      $this->makeDirectory(dirname($path));
      
      // Create the file with its content
      $contents = $this->getCompiledFile($file, $fileEntry["variables"]);
      
      // If file not exists, saves it, otherwise inform that file already exists
      $this->storeFile($path, $contents, $force);
    }
    
    return Command::SUCCESS;
    
  }
  
  /**
   * Get the files to create
   *
   * @return array
   */
  private function getFilesToCreate(string $only = null): array {
    if ($only) {
      $only = explode(",", $only);
    }
    
    $resource         = $this->argument('resource');
    $resourceSingular = Pluralizer::singular($resource);
    $modelName        = $this->getSingularClassName($resource);
    $model            = "App\\Models\\{$modelName}";
    
    if ( !class_exists($model)) {
      $this->error("Model {$model} does not exists");
      
      return [];
    }
    
    $columns = $this->getTableColumns((new $model));
    
    return collect(["index", "edit", "create", "show"])
      ->filter(fn($view) => !$only || in_array($view, $only))
      ->map(fn($view) => $this->{"get" . ucfirst($view) . "View"}($resource, $resourceSingular, $columns))
      ->toArray();
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
        "title"            => ucfirst($resourceSingular) . " #{{ \${$resourceSingular}->id }} | Edit",
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
        "title"            => ucfirst($resourceSingular) . " #{{ \${$resourceSingular}->id }}",
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
      
      $acc->push($this->generateFormField($column['name'], $colName, $value, $column["type"], $column["foreign"], $column["required"]));
      
      return $acc;
    }, collect())->implode("\n\n          ");
  }
  
  public function generateFormField($name, $labelText, $value, $columnType, $columnForeign, $columnRequired) {
    $template = '<div class="mb-3">
            $label
            $input
            @error(\'$name\')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>';
    
    $label = $this->generateInputLabel($name, $labelText);
    
    // dump($columnType);
    
    switch (strtolower($columnType)) {
      case "boolean":
        $input = $this->generateSwitchField($name, $value, $labelText);
        $label = "";
        break;
      case "longtext":
      case "text":
        $input = $this->generateTextAreaField($name, $value);
        break;
      default:
        $input = $this->generateInputField($name, $value);
    }
    
    if ($columnForeign) {
      $input = $this->generateSelectField($name, $value, $labelText, $columnForeign, $columnRequired);
    }
    
    return str_replace(
      ['$name', '$input', '$label'],
      [$name, $input, $label],
      $template);
  }
  
  public function generateInputLabel($name, $label) {
    return str_replace(['$name', '$label'], [$name, $label], '<label class="form-label" for="input_$name">$label</label>');
  }
  
  public function generateInputField($name, $value): string {
    $template = '<input type="text"
                   class="form-control @error(\'$name\') is-invalid @enderror"
                   name="$name"
                   id="input_$name"
                   value="$value">';
    
    return str_replace(['$name', '$value'], [$name, $value], $template);
  }
  
  public function generateTextareaField($name, $value): string {
    $wysiwyg = $this->option("wysiwyg");
    $class = $wysiwyg ? " tinymce" : "";
    
    $template = '<textarea type="text"
                   class="form-control$class @error(\'$name\') is-invalid @enderror"
                   name="$name"
                   id="input_$name"
                   cols="30" rows="5"
                   >$value</textarea>';
    
    return str_replace(['$name', '$value', '$class'], [$name, $value, $class], $template);
  }
  
  public function generateSwitchField($name, $value, $label) {
    $checked = str_replace(['{', '}'], "", $value);
    $checked = "{{ $checked ? 'checked' : '' }}";
    
    $template = '<div class="form-check form-switch">
              <input type="hidden" name="$name" value="0">
              <input class="form-check-input" type="checkbox" role="switch" id="input_$name" name="$name"
                    value="1" $checked>
              <label class="form-check-label" for="input_$name">$label</label>
            </div>';
    
    return str_replace(['$name', '$label', '$checked'], [$name, $label, $checked], $template);
  }
  
  public function generateSelectField($name, $value, $label, $foreign, $required) {
    $value            = str_replace(['{', '}'], "", $value);
    $checked          = str_replace(['{', '}'], "", $value);
    $arrayName        = $foreign["table"];
    $resourceSingular = Str::singular($arrayName);
    $emptyOption      = $required ? "" : '<option></option>';
    
    $template = '<select class="form-select" aria-label="$label" id="input_$name" name="$name">
              $emptyOption
              @foreach ($$array as $$resource)
                <option value="{{ $$resource->id }}" {{ $$resource->id === $value ? \'selected\' : \'\' }}>{{ $$resource->name }}</option>
              @endforeach
            </select>';
    
    return str_replace(
      ['$name', '$label', '$value', '$checked', '$array', '$resource', '$emptyOption'],
      [$name, $label, $value, $checked, $arrayName, $resourceSingular, $emptyOption],
      $template);
  }
}

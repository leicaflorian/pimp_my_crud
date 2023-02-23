<?php

namespace LeicaFlorian\PimpMyCrud\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;
use LeicaFlorian\PimpMyCrud\Traits\WithStubHandling;
use LeicaFlorian\PimpMyCrud\Traits\WithTableAnalyzer;

class MakeCrudControllerCommand extends Command {
  use WithStubHandling, WithTableAnalyzer;
  
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'pmc:controller {resource : Name of the resource, in lowercase, plural}
                          {--force : Overwrite existing files}';
  
  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a resource Controller for for the specified resource and also fill it with the basic code.
  Example:
    php artisan pmc:controller posts
    php artisan pmc:controller posts --force
    ';
  
  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle(): int {
    $force = $this->option("force");
  
    $resourceData = $this->handleResourceArgument($this->argument("resource"));
    $resourceName = $resourceData["name"];
    $resourcePath = $resourceData["path"];
    $routePrefix  = $resourceData["prefix"];
  
    // For each configured file
    foreach ($this->getFilesToCreate($resourceName, $routePrefix) as $fileEntry) {
      $file = $fileEntry["src"];
      // get destination folder path
      $path = $this->getSourceFilePath($fileEntry["dest"], ["{resource}" => implode("/", [$resourcePath, $resourceName])]);
    
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
  private function getFilesToCreate($resource, $routePrefix): array {
    $resourceSingular = Pluralizer::singular($resource);
    $modelName        = $this->getSingularClassName($resource);
    $model            = "App\\Models\\{$modelName}";
    $destPath         = $routePrefix ? collect(explode(".", $routePrefix))
      ->reduce(function ($acc, $str): Collection {
        if ($str) {
          $acc->push(ucfirst($str));
        }
        
        return $acc;
      }, collect([]))
      ->join("/") : "";
    
    if ( !class_exists($model)) {
      $this->error("Model {$model} does not exists");
      
      return [];
    }
    
    $columns        = $this->getTableColumns((new $model));
    $foreignColumns = $columns->filter(fn($column) => $column["foreign"] && $column["fillable"]);
    
    $extraImport   = [];
    $extraQuery    = [];
    $extraViewData = [];
    
    if ($destPath) {
      $extraImport[] = "use App\\Http\\Controllers\\Controller;";
    }
    
    $extraImport[] = "use $model;";
    
    // If there are foreign columns, add them to the imports, queries and view data
    if ($foreignColumns->count() > 0) {
      $foreignColumns->each(function ($column) use (&$extraImport, &$extraQuery, &$extraViewData) {
        $table = $column["foreign"]["table"];
        $model = ucfirst(Pluralizer::singular($table));
        
        $extraImport[]   = "use App\\Models\\{$model};";
        $extraQuery[]    = "\$$table = $model::all();\n";
        $extraViewData[] = "'$table' => \$$table";
      });
    }
    
    return [
      [
        "src"       => "resources/controllers/Controller.php",
        "dest"      => "app/Http/Controllers/" . ($destPath ? $destPath . "/" : "") . "{$modelName}Controller.php",
        "variables" => [
          "resource"            => $resource,
          "resourceSingular"    => $resourceSingular,
          "routePrefix"         => $routePrefix,
          "controllerNamespace" => $destPath ? Str::replace("/", "\\", $destPath) : "",
          "modelName"           => $modelName,
          "modelNamespace"      => $model,
          "columns"             => $columns,
          "extraImport"         => implode("\n", $extraImport),
          "extraQuery"          => implode("\n", $extraQuery),
          "extraViewData"       => implode(", ", $extraViewData),
        ],
      ]
    ];
  }
}

<?php

namespace App\Console\Commands;

use App\Traits\WithStubHandling;
use App\Traits\WithTableAnalyzer;
use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;

class MakeCrudControllerCommand extends Command {
  use WithStubHandling, WithTableAnalyzer;
  
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'pmc:controller {resource}';
  
  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Command description';
  
  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle() {
    // For each configured file
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
      [
        "src"       => "resources/controllers/Controller.php",
        "dest"      => "app/Http/Controllers/{$modelName}Controller.php",
        "variables" => [
          "resource"         => $resource,
          "resourceSingular" => $resourceSingular,
          "modelName"        => $modelName,
          "modelNamespace"   => $model,
          "columns"          => $columns,
        ],
      ]
    ];
  }
}

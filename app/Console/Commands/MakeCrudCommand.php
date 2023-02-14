<?php

namespace App\Console\Commands;

use App\Traits\WithStubHandling;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;

class MakeCrudCommand extends Command {
  use WithStubHandling;
  
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'pmc:crud {resource}';
  
  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create the full crud for the specified resource';
  
  /**
   * Execute the console command.
   *
   * @return void
   */
  public function handle(): void {
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
  }
  
  /**
   * Get the files to create
   *
   * @return array
   */
  private function getFilesToCreate(): array {
    return [
      [
        "src"       => "resources/views/index.blade.php",
        "dest"      => "resources/views/{resource}/index.blade.php",
        "variables" => [
          "pageTitle" => "List of {$this->argument('resource')}",
        ]
      ]
    ];
  }
  
}

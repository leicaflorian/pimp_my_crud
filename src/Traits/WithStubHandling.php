<?php

namespace LeicaFlorian\PimpMyCrud\Traits;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;

trait WithStubHandling {
  /**
   * Return the Singular Capitalize Name
   *
   * @param $name
   *
   * @return string
   */
  public function getSingularClassName($name) {
    return ucwords(Pluralizer::singular($name));
  }
  
  /**
   * Return the stub file path
   *
   * @return string
   *
   */
  public function getStubPath($file) {
    return __DIR__ . "/../stubs/$file";
  }
  
  /**
   * Get the stub path and the stub variables and return the compiled file
   *
   * @return bool|mixed|string
   *
   */
  public function getCompiledFile($file, $variables) {
    return $this->getStubContents($this->getStubPath($file), $variables);
  }
  
  
  /**
   * Replace the stub variables(key) with the desire value
   *
   * @param         $stub
   * @param  array  $stubVariables
   *
   * @return string
   */
  public function getStubContents($stub, array $stubVariables = []): string {
    $contents = file_get_contents($stub);
    
    foreach ($stubVariables as $search => $replace) {
      $contents = str_replace('{{ ' . $search . ' }}', $replace, $contents);
    }
    
    return $contents;
  }
  
  /**
   * Get the full path of generate class
   *
   * @return string
   */
  public function getSourceFilePath($filePath, $toReplace) {
    return str_replace(array_keys($toReplace), array_values($toReplace), $filePath);
    
    // return base_path('App/Interfaces') . '/' . $this->getSingularClassName($this->argument('resource')) . $fileName;
  }
  
  /**
   * Build the directory for the class if necessary.
   *
   * @param  string  $path
   *
   * @return string
   */
  protected function makeDirectory($path) {
    $files = new Filesystem();
    
    if ( !$files->isDirectory($path)) {
      $files->makeDirectory($path, 0777, true, true);
    }
    
    return $path;
  }
  
  
  protected function storeFile($path, $contents, $force) {
    $files = new Filesystem();
    
    // If file not exists, saves it, otherwise inform that file already exists
    if ( !$files->exists($path) || $force) {
      $this->info("File : {$path} created");
      $files->put($path, $contents);
    } else {
      $this->info("File : {$path} already exits");
    }
  }
}

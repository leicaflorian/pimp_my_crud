<?php

namespace LeicaFlorian\PimpMyCrud;

use Illuminate\Support\ServiceProvider;
use LeicaFlorian\PimpMyCrud\Commands\MakeCrudControllerCommand;
use LeicaFlorian\PimpMyCrud\Commands\MakeCrudViewsCommand;

class PimpMyCrudServiceProvider extends ServiceProvider {
  /**
   * Register services.
   *
   * @return void
   */
  public function register() {
    //
  }
  
  /**
   * Bootstrap services.
   *
   * @return void
   */
  public function boot() {
    // Register the command if we are using the application via the CLI
    if ($this->app->runningInConsole()) {
      $this->commands([
        MakeCrudViewsCommand::class,
        MakeCrudControllerCommand::class
      ]);
    }
  }
}

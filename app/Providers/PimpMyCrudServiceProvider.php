<?php

namespace App\Providers;

use App\Console\Commands\MakeCrudCommand;
use App\Console\Commands\MakeCrudControllerCommand;
use App\Console\Commands\MakeCrudViewsCommand;
use Illuminate\Support\ServiceProvider;

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

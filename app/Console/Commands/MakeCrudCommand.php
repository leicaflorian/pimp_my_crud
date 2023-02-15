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
  
  }
  
  
}

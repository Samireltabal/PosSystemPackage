<?php 
  namespace Syncit\PosSystem;

  use Syncit\PosSystem\Commands\PosSystemCommand;
  use Illuminate\Support\ServiceProvider;

  class PosSystemServiceProvider extends ServiceProvider {
    
    public function boot() {
      // $this->package('Sycnit/PosSystem');
      if ($this->app->runningInConsole()) {
        $this->commands([
          PosSystemCommand::class,
      ]);
      }
    }

    public function register() {
      $this->app->singleton('possystem', function () {
        return new PosSystem;
    });
    }

  }
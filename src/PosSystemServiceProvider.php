<?php 
  namespace Synciteg\PosSystem;

  use Illuminate\Support\Facades\Route;
  use Synciteg\PosSystem\Commands\PosSystemCommand;
  use Illuminate\Support\ServiceProvider;

  class PosSystemServiceProvider extends ServiceProvider {
    
    public function boot() {
      // $this->package('Sycnit/PosSystem');
      
      // include __DIR__.'/Routes/Routes.php';
      $this->registerRoutes();
      // $this->loadRoutesFrom(__DIR__.'/./Routes/routes.php');
      if ($this->app->runningInConsole()) {
        $this->commands([
          PosSystemCommand::class,
        ]);
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
      }
    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/./Routes/Routes.php');
        });
    }

    protected function routeConfiguration()
    {
        return [
            'prefix' => config('pos.prefix'),
            'middleware' => config('pos.middleware'),
        ];
    }
    public function register() {
      $this->app->singleton('possystem', function () {
          return new PosSystem;
      });
      
      $this->app->make('Synciteg\PosSystem\Controllers\CategoriesController');
      $this->app->make('Synciteg\PosSystem\Controllers\InvoicesController');
      $this->app->make('Synciteg\PosSystem\Controllers\ProductsController');
      $this->app->make('Synciteg\PosSystem\Controllers\InvoiceItemsController');

      $this->mergeConfigFrom(__DIR__.'/config/config.php', 'pos');
    }

  }
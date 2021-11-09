<?php 
    use Synciteg\PosSystem\Controllers\ProductsController;

    Route::get('/', function() {
        return response()
        ->json(
            ['message' => 'syncit pos is responding', 'version' => config('pos.version')],
         201);
    });

    Route::prefix('products')->group( function () {
        Route::get('/', [ProductsController::class, 'list']);
    });
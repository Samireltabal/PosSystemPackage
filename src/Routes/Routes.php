<?php 
    use Synciteg\PosSystem\Controllers\ProductsController;
    use Synciteg\PosSystem\Controllers\CategoriesController;
    use Synciteg\PosSystem\Controllers\InvoicesController;

    Route::get('/', function() {
        return response()
        ->json(
            ['message' => 'syncit pos is responding', 'version' => config('pos.version')],
         201);
    });
    // Categories Route 
    Route::prefix('categories')->group( function () {
        Route::post('/', [CategoriesController::class, 'create']);
        Route::put('/', [CategoriesController::class, 'update']);
        Route::get('/', [CategoriesController::class, 'list']);
        Route::delete('/', [CategoriesController::class, 'delete']);
    });
    // Products Route 
    Route::prefix('products')->group( function () {
        Route::get('/', [ProductsController::class, 'list']);
        Route::post('/import', [ProductsController::class, 'start_import']);
        Route::post('/search', [ProductsController::class, 'search_product']);
        Route::post('/', [ProductsController::class, 'create_product']);
        Route::put('/', [ProductsController::class, 'update_product']);
        Route::delete('/', [ProductsController::class, 'delete_product']);
    });

    Route::prefix('sales')->group( function () {
        Route::get('/invoices', [InvoicesController::class, 'list_open_invoices']);
        Route::post('/invoices', [InvoicesController::class, 'openInvoice']);
        Route::post('/invoices/show', [InvoicesController::class, 'show_invoice']);
        Route::post('/add/item', [InvoicesController::class, 'addItem']);
        Route::post('/barcode/query', [InvoicesController::class, 'barcode_query']);
    });

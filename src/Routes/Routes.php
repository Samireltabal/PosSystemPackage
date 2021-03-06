<?php 
    use Synciteg\PosSystem\Controllers\ProductsController;
    use Synciteg\PosSystem\Controllers\CategoriesController;
    use Synciteg\PosSystem\Controllers\InvoicesController;
    use Synciteg\PosSystem\Controllers\BundleController;
    use Synciteg\PosSystem\Controllers\IptvController;

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
    Route::prefix('iptv')->group( function () {
        Route::post('/server/create', [IptvController::class, 'create_server']);
        Route::get('/server/list', [IptvController::class, 'list_servers']);
        Route::post('/codes/add', [IptvController::class, 'add_codes']);
        Route::post('/codes/list', [IptvController::class, 'show_codes']);
        Route::post('/request', [IptvController::class, 'generate']);
        Route::post('/show', [IptvController::class, 'show']);
        Route::post('/query', [IptvController::class, 'query']);
        Route::get('/server', [IptvController::class, 'show_server']);
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

    Route::prefix('bundles')->group( function () {
        Route::get('/', [BundleController::class, 'list']);
        Route::post('/', [BundleController::class, 'create']);
        Route::get('/disable/{id}', [BundleController::class, 'disable']);
        Route::get('/enable/{id}', [BundleController::class, 'enable']);
        Route::get('/delete/{id}', [BundleController::class, 'delete']);
        Route::get('/edit/{id}', [BundleController::class, 'edit']);
        Route::get('/show/{id}', [BundleController::class, 'show']);
    });
    Route::prefix('sales')->group( function () {
        Route::get('/invoices', [InvoicesController::class, 'list_open_invoices']);
        Route::post('/invoices', [InvoicesController::class, 'openInvoice']);
        Route::post('/invoices/show', [InvoicesController::class, 'show_invoice']);
        Route::post('/add/item', [InvoicesController::class, 'addItem']);
        Route::post('/barcode/query', [InvoicesController::class, 'barcode_query']);
    });

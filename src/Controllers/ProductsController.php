<?php

namespace Synciteg\PosSystem\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Synciteg\PosSystem\Models\Product;
use Synciteg\PosSystem\Models\ProductType;
use Synciteg\PosSystem\Models\Category;
use Illuminate\Support\Facades\DB;


class ProductsController extends Controller
{
    // V.0.1.2 Pos Package 
    public function list(Request $request) {
        $parts = Product::query();
        $parts = $parts->with(['inventory', 'category']);
        if($request->has('search') && $request->input('search')) {
            $term = $request->input('search');
            $parts = $parts->where('product_name', 'like', "%$term%");
        }
        if($request->has('in_stock') && $request->input('in_stock')) {
            $parts = $parts->InStock();
        }
        $per_page = 10;
        if($request->has('per_page')) {
            $per_page = $request->input('per_page');
        }
        $parts = $parts->paginate($per_page);
        return response()->json($parts, 200);
    }
    // V.0.1.2 Pos Package 
    public function search_product(Request $request) {
        $term = $request->input('term');
        $parts = Product::where('product_name', 'like', "%$term%")->orWhere('barcode', '=', $term)->get();
        return response()->json($parts, 200);
    }

    public function start_import(Request $request) {
        $validation = $request->validate([
            'file_id' => 'required|exists:uploads,id',
            'import_type' => 'required',
        ]);
        $file = Upload::find($request->input('file_id'));
        // check if file is excel file 
        if ($file->is_excel_file) {
            // check if import type is product
            if($request->input('import_type') == "Product") {
                return self::handle_import($file);
                // return response()->json(['data' => $file, 'type' => 'Product']);
            } 
            // check if import type is Part
            if($request->input('import_type') == "Part") {
                return self::handle_import($file, "Part");
            } 
            // return if import type is unknown 
            return response()->json(['data' => $file, 'type' => 'unknown']);
        }
        // return if not excel file
        return response()->json(['message' => 'failed to import']);
    } 
    // V.0.1.2 Pos Package 
    public function create_product(Request $request) {
        $validation = $request->validate([
            'product_name' => 'required',
            'newCategory' => 'required',
            'product_type' => 'required|in:منتج,خدمة',
            'category_id' => 'required_unless:newCategory,true',
            'category' => 'requiredIf:newCategory,true',
            'original_price' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $product_type = self::handle_product_type($request->input('product_type'));
            $category = self::handle_category($request->input('category'));
            $array_of_data = array(
                'product_name' => $request->input('product_name'),
                'category_id' => $category->id,
                'product_type_id' => $product_type->id,
                'original_price' => $request->input('original_price')
            );
            $product = Product::create($array_of_data);
            $product = self::generateBarcode($product);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->errorInfo[2]
            ], 400);
        }    
        
        DB::commit();
        return response()->json($product, 201);
    }
    // V.0.1.2 Pos Package 
    public function update_product(Request $request) {
        $validation = $request->validate([
            'id' => 'required|exists:products,id',
        ]);
        $product = Product::find($request->input('id'));
        $product->update($request->only(['product_name', 'original_price']));
        return $product;
        return $request->except('id');
    }

    public function delete_product(Request $request) {
        $validation = $request->validate([
            'product_name' => 'required|unique:products,product_name',
        ]);
    }

    public static function handle_import(Upload $file, $type = "Product") {
        $url = storage_path('app/' . $file->path . '/' . $file->file_name);
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $parse = $reader->load($url);
        $sheetData = $parse->getActiveSheet()->toArray(null, true, true, true);
        $results = array();
        foreach ($sheetData as $key => $row) {
            if($key === 1) {
                
            }
            else {
                $product = self::import_product($row);
                $results[$key] = $product;
                if($product instanceOf Product) {
                    $inv = self::handle_quantity($product, $row['E']);
                    $product->save();
                    $product = self::generateBarcode($product);
                    $product->save();
                }
            }
        }
        // $file->imported = true;
        // $file->save();
        return response()->json(['data' => $results,'type' => $type], 200);
    }    

    public static function import_product($row) {
        $product = new Product();
        $product->product_name = $row['B'];
        $product->original_price = $row['D'];
        $product->product_type_id = self::handle_product_type($row['F']);
        $product->category_id = self::handle_category($row['C']);
        try {
            $product->save();
        } catch (\Throwable $th) {
            return 'product exists';
        }
        return $product;
    }
    // V.0.1.2 Pos Package 
    public static function handle_category($name) {
        $category = Category::firstOrNew(['category_name' => $name]);
        $category->save();
        return $category;
    }   
    // V.0.1.2 Pos Package 
    public static function handle_product_type($name) {
        $type = productType::firstOrNew(['type_name' => $name]);
        $type->save();
        return $type;
    }

    public static function handle_quantity(Product $product, $quantity) {
            $inventory = $product->inventory()->where('inventory_type', '=', setting('default_inventory'))->first();
            if($product->inventory()->exists() && $inventory) {
                $data = array(
                    'quantity' => $quantity
                );
                $inventory->update($data);
                $inventory->save();
                return $inventory;
            } else {
                $inv = $product->inventory()->create([
                    'inventory_type' => setting('default_inventory'),
                    'quantity' => $quantity,
                    'purchase_price' => 0,
                    'unit' => 'item',
                ]);
                $inv->save();
                return $inv;
            }
    }
    // V.0.1.2 Pos Package 
    public static function generateBarcode(Product $record) {
        $barcode = env('ORGANIZATION_CODE', "SYN") . '-' . env('PRODUCTS_CODE', "PRD") . '-' ."20" . $record->product_type_id . $record->category_id . $record->id;
        $record->barcode = $barcode;
        $record->save();
        return $record;
    }


}

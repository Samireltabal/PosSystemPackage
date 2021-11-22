<?php

namespace Synciteg\PosSystem\Controllers;

use Synciteg\PosSystem\Models\Bundle;
use Synciteg\PosSystem\Models\BundleItem;
use Synciteg\PosSystem\Models\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BundleController extends Controller
{
    //
    public function __construct () {
        $admin_middlewares = config('pos.adminstrator_role');
        $general_middlewares = config('pos.employee_role');
        $this->middleware(
            ["role:$admin_middlewares"])
        ->except(
            ['generate', 
             'show',
             'query',
             'list_servers']
        );
        $this->middleware(
            ["role:$general_middlewares"])->only(['generate', 'show', 'list_servers']);
    }

    public function list () {
        $bundles = Bundle::all();
        return response()->json($bundles, 200);
    }

    public function create(Request $request) {
        $validation = $request->validate([
            'name' => 'required|unique:bundles,name',
            'price' => 'required',
            'expires_at' => 'required',
            'products' => 'required|array'
        ]);

        $bundle = Bundle::create([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'expires_at' => $request->input('expires_at'),
            'products' => $request->input('products'),
            'active' => true
        ]);
        $bundle->save();
        $bundle = self::generateBarcode($bundle);
        $bundle_id = $bundle->id;
        $ps = collect($request->input('products'));
        foreach ($ps as $p) {
            $product = Product::find($p['id']);
            $product->groupable()->create([
                'bundle_id' => $bundle_id
            ]);
        }
        return $bundle;
    }

    public function disable($id) {
        $bundle = Bundle::find($id);
        $bundle->disable();
        return $bundle;
    }

    public function enable($id) {
        $bundle = Bundle::find($id);
        $bundle->enable();
        return $bundle;
    }

    public function edit (Request $request, $id) {

    }

    public function delete ($id) {
        $bundle = Bundle::find($id);
        $bundle->delete();
        return response()->json(['message' => 'deleted'], 200);
    }

    public function show ($id) {
        $bundle = Bundle::find($id);
        return response()->json($bundle, 200);
    }

    public static function generateBarcode(Bundle $record) {
        $barcode = env('ORGANIZATION_CODE', "SYN") . '-' . env('BUNDLE_CODE', "BUN") . '-' ."300" . $record->id;
        $record->barcode = $barcode;
        $record->save();
        return $record;
    }
}

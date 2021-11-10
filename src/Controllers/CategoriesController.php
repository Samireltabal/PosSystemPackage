<?php

namespace Synciteg\PosSystem\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Synciteg\PosSystem\Requests\CreateCategoryRequest;
use Synciteg\PosSystem\Requests\UpdateCategoryRequest;
use Synciteg\PosSystem\Models\Category; 

class CategoriesController extends Controller
{
    public function __construct() {

    }

    public function create(CreateCategoryRequest $request) {
        
        $cat = Category::firstOrCreate($request->only('category_name'));
        return response($cat, 201);

    }

    public function Update(UpdateCategoryRequest $request) {
        
        $cat = Category::find($request->input('id'));
        $cat->update(['category_name' => $request->input('category_name')]);
        $cat->save();
        return response()->json($cat, 200);

    }

    public function list(Request $request) {
        $cats = Category::query();
        $cats = $cats->withCount('products');
        if($request->has('paginate')) {
            $per_page = $request->input('paginate');
            $cats = Category::paginate($per_page);
        } else {
            $cats = Category::get();
        }
        return response()->json($cats, 200);
    }

    public function delete(Request $request) {
        $validation = $request->validate([
            'id' => 'required|exists:categories,id'
        ]);

        $cat = Category::find($request->input('id'));
        if ( $cat->delete() ) {
            return response()->json([
                'message' => 'Category deleted'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Something Went Wrong'
            ], 400);
        }
    }


}

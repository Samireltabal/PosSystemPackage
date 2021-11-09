<?php

namespace Synciteg\PosSystem\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductsController extends Controller
{
    //
    public function list(Request $request) {
        return response()->json(['message' => 'ok']);
    }
}

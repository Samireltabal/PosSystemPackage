<?php

namespace Synciteg\PosSystem\Controllers;

use Illuminate\Http\Request;
use Synciteg\PosSystem\Models\IptvCode;
use Synciteg\PosSystem\Models\IptvServer;
use Synciteg\PosSystem\Models\IptvSubscription;

class IptvController extends Controller
{
    public function __construct() {
        $admin_middlewares = config('pos.adminstrator_role');
        $general_middlewares = config('pos.employee_role');
        $this->middleware(["role:$admin_middlewares"])->except(['generate', 'show']);
        $this->middleware(["role:$general_middlewares"])->only(['generate', 'show']);
    }

    public function create_server(Request $request) {
        $validation = $request->validate([
            'server_name' => 'required|unique:iptv_servers,server_name',
            'supplier_id' => 'required|exists:suppliers,id',
            'purcahse_price' => 'required',
        ]);
        
        $server = IptvServer::create($request->all());
        return response()->json($server, 201);

    }

    public function add_codes(Request $request) {
        $validation = $request->validate([
            'server_id' => 'required|exists:iptv_servers,id',
            'periodByMonth' => 'required',
            'codes' => 'required'
        ]);

        return $request;
    }

    public function show_codes(Request $request) {

    }

    public function generate(Request $request) {

    }

    public function show(Request $request) {

    }
}

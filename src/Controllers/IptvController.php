<?php

namespace Synciteg\PosSystem\Controllers;

use Illuminate\Http\Request;
use Synciteg\PosSystem\Models\IptvCode;
use Synciteg\PosSystem\Models\IptvServer;
use Synciteg\PosSystem\Models\IptvSubscription;
use App\Http\Controllers\Controller;

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
            'purchase_price' => 'required',
        ]);
        
        $server = IptvServer::create($request->all());
        return response()->json($server, 201);

    }

    public function list_servers(Request $request) {
        $servers = IptvServer::query();
        if($request->has('supplier_id')) {
            $servers = $servers->with(['supplier'])->where('supplier_id', '=', $request->input('supplier_id'));
        }
        $servers = $servers->get();
        return response()->json($servers, 200);
    }

    public function add_codes(Request $request) {
        $validation = $request->validate([
            'server_id' => 'required|exists:iptv_servers,id',
            'periodByMonth' => 'required',
            'codes' => 'required'
        ]);
        $values = explode(',', $request->input('codes'));
        foreach ($values as $code) {
            $record = IptvCode::create([
                'server_id' => $request->input('server_id'),
                'code' => $code,
                'periodByMonth' => $request->input('periodByMonth'),
                'used' => false
            ]);
        }
        return $values;
    }

    public function show_codes(Request $request) {
        $validation = $request->validate([
            'subscription_id' => 'required|exists:iptv_subscriptions,id'
        ]);

        $subscription = IptvSubscription::find($request->input('subscription_id'));
        $subscription = $subscription->makeVisible(['code']);
        return $subscription;
    }

    public function generate(Request $request) {
        $validation = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'price' => 'required',
            'server_type' => 'required|exists:iptv_servers,id',
            'device_type_id' => 'required|exists:device_types,id'
        ]);
        \DB::beginTransaction();
        try {
            $code = IptvCode::GetFirstAvailableCode($request->input('server_type'));
            // return $code;
            $record = $code->record()->create([
                'customer_id' => $request->input('customer_id'),
                'server_id' => $code->server_id,
                'start_date' => \Carbon\Carbon::now(),
                'end_date' => \Carbon\Carbon::now()->addMonths($code->periodByMonth),
                'paid' => true,
                'price' => $request->input('price'),
                'device_type_id' => $request->input('device_type_id')
            ]);
            $code->markAsUsed($code);
            \DB::commit();
            return $record;
        } catch (\Throwable $th) {
            \DB::rollback();;
        }
        return response()->json(['message' => 'something went wrong'], 400);
    }

    public function show(Request $request) {
        $validation = $request->validate([
            'code_id' => 'required|exists:iptv_subscriptions,id'
        ]);

        $subscription = IptvSubscription::with(['customer','server', 'code'])->find($request->input('code_id'));
        $subscription->server->makeHidden(['id','supplier_id','purchase_price','created_at','updated_at', 'codesCount', 'supplier', 'availableCodes']);
        $subscription->customer->makeHidden(['id','uuid','is_employee', 'created_at','updated_at','gender','phone']);
        $subscription->customer->makeHidden(['id','created_at','updated_at', 'customer_id', 'record_id']);
        $subscription->makeVisible(['code']);
        return response()->json($subscription, 200);
    }
}

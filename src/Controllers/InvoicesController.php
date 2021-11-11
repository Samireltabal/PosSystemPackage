<?php

namespace Synciteg\PosSystem\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Synciteg\PosSystem\Models\Invoice;
use Synciteg\PosSystem\Models\Product;
use App\Models\Main\Shift;
use Syncit\MaintenanceCenter\Models\Record;

class InvoicesController extends Controller
{
    //
    public function __construct() {
        $roles = config('pos.employee_role');
        return $this->middleware(["role:$roles,api"]);
    }

    public function list_open_invoices() {
        $invoices = Invoice::all();
        return response()->json($invoices, 200);
    }

    public function openInvoice(Request $request) {
        $validation = $request->validate([
            'customer_id' => 'required|exists:customers,id'
        ]);
        $current_shift = get_current_shift_id();
        $data = array(
            'shift_id' => $current_shift,
            'customer_id' => $request->input('customer_id')
        );
        $invoice = Invoice::create($data);
        return response()->json($invoice, 201);
    }

    public function show_invoice(Request $request) {
        // $validation = $request->validate([
        //     'invoice_id' => 'required|exists:invoices,id'
        // ]);
        $data = array();
        if($request->has('invoice_id') && $request->input('invoice_id')) {
            $data['is_invoice'] = true;
            $invoice_id = $request->input('invoice_id');
            $invoice = Invoice::with(['items'])->find($invoice_id);
            $data['items'] = $invoice->items;
        } else {
            $data['is_invoice'] = false;
            $current = Shift::active()->orderBy('created_at', 'desc')->first();
            // $invoice = $current->makeHidden(['TotalPaid', 'OrdersCount', 'ClosedOrdersCount', 'TotalExpenses', 'orders', 'expenses', 'user', 'invoices']);
            $data['items'] = $current->items;
        }
        $items = collect($data['items']);
        $total_before_discount = 0;
        foreach ($items as $item) {
            if ($item->invoicable instanceOf Product) {
                $sub_total = $item->invoicable->original_price * $item->quantity;
                $total_before_discount = $total_before_discount + $sub_total;
            }
            if ($item->invoicable instanceOf Record) {
                $total_before_discount = $total_before_discount + $item->selling_price;
            }
        }
        $data['meta'] = array(
            'totalBeforeDiscount' => $total_before_discount,
            'discount' => $total_before_discount - $items->sum('total'),
            'total' => $items->sum('total'),
            'count' => $items->sum('quantity'),
        );
        return response()->json($data, 200);
    }

    public function barcode_query(Request $request) {
        $validation = $request->validate([
            'barcode' => 'required'
        ]);
        $barcode = $request->input('barcode');
        $data = explode("-", $barcode);
        if($data[1] == env('MAINTAINENCE_CODE', "MNT")) {
            $response = Record::Barcode($barcode)->first();
        } elseif ($data[1] == env("PRODUCTS_CODE", "PRD")) {
            $response = Product::Barcode($barcode)->first();
        }
        return response()->json($response);

    }
    public function addItem(Request $request) {
        $validation = $request->validate([
            'barcode' => 'required',
            'quantity' => 'required',
            'selling_price' => 'required',
            'discount_fixed' => 'required',
            'discount' => 'required'
        ]);
        // get barcode
        $barcode = $request->input('barcode');
        // handle barcode & fetch item
        $data = explode("-", $barcode);
        if (count($data) != 3 ) {
            return response()->json(['message' => 'no records found'], 404);
        }
        if($data[1] == env('MAINTAINENCE_CODE', "MNT")) {
            $sellable = Record::Barcode($barcode)->first();
        } elseif ($data[1] == env("PRODUCTS_CODE", "PRD")) {
            $sellable = Product::Barcode($barcode)->first();
        }
        $shift_id = get_current_shift_id();
        $price_after_discount = self::calculate_discount($request->input('selling_price'), $request->input('discount'), $request->input('discount_fixed'));
        $data = array(
            'quantity' => $request->input('quantity'),
            'selling_price' => $price_after_discount,
            'total' => $price_after_discount * $request->input('quantity'),
            'discount' => $request->input('discount'),
            'invoice_id' => $request->has('invoice_id') && $request->input('invoice_id') ? $request->input('invoice_id') : null,
            'shift_id' => $shift_id,
            'fixed_discount' => $request->input('discount_fixed'),
            'accepted' => Auth()->guard('api')->user()->hasRole('admin') ? true : false,
        );

        $item = $sellable->invoicable()->create($data);
        $item->save();
        $sellable->save();
        return $item;
    }

    public static function calculate_discount($original_price, $discount_value = 0, $is_fixed = false) {
        if (!$is_fixed) {
            $discount = $original_price * ( $discount_value / 100);
            $final_price = $original_price - $discount; 
        } else {
            $final_price = $original_price - $discount_value;
        }

        return $final_price;
    }

}

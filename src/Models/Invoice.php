<?php

namespace Syncit\PosSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Syncit\PosSystem\Models\InvoiceItem;
use App\Models\Erp\Customer;
use App\Models\Main\Shift;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'shift_id', 'active'];
    protected $with = ['customer']; 
    
    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function shift() {
        return $this->belongsTo(Shift::class, 'shift_id', 'id');
    }

    public function items() {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }
}

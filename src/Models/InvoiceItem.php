<?php

namespace Synciteg\PosSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Syncit\PosSystem\Models\Invoice;
use App\Models\Main\Shift;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'selling_price',
        'discount',
        'fixed_discount',
        'invoice_id',
        'shift_id',
        'total',
        'accepted'
    ];

    protected $with = ['invoicable'];

    public function invoicable() {
        return $this->morphTo();
    }

    public function invoice() {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function shift() {
        return $this->belongsTo(Shift::class, 'shift_id', 'id');
    }


}

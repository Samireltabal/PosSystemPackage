<?php

namespace Synciteg\PosSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Synciteg\PosSystem\Models\BundleItem;
use Synciteg\PosSystem\Traits\Invoiceable;

class Bundle extends Model
{
    use HasFactory, Invoiceable;

    protected $fillable = [
        'name',
        'price',
        'products',
        'expires_at',
        'active'
    ];

    protected $with = [
    ];

    protected $hidden = ['items', 'products'];
    protected $appends = [ 'BarcodeUrl', 'InProducts'];

    protected $casts = [
        'products' => 'array'
    ];

    public function items() {
        return $this->hasMany(BundleItem::class, 'bundle_id', 'id');
    }

    public function scopeActive($query) {
        return $query->where('active', '=', true)->where('expires_at', '>=',  \Carbon\Carbon::now());
    }

    public function getInProductsAttribute () {
        $products = array();
        if($this->has('items') && $this->items->count() > 0) {
            foreach ($this->items as $item) {
                $products[] = $item->groupable;
            }
        } 
        return $products;
    }
    public function disable() {
        $this->active = false;
        $this->save();
    }

    public function enable () {
        $this->active = true;
        $this->save();
    }

    public function scopeBarcode($query, $barcode) {
        return $query->where('barcode', $barcode);
    }

    public function getBarcodeUrlAttribute() {
        $uri = \DNS1D::getBarcodePNGPath($this->attributes['barcode'], 'C128',1,64,array(0,0,0), true);
        return url($uri);
    }
    
}

<?php

namespace Synciteg\PosSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Synciteg\PosSystem\Traits\Invoiceable;
use Synciteg\PosSystem\Traits\Groupable;
use Syncit\StockControl\Traits\hasInventory;
use \Spatie\Tags\HasTags;
use App\Models\Purchase\PurchaseItem;

class Product extends Model
{
    use HasFactory, Invoiceable, hasInventory, HasTags, Groupable;

    protected $fillable = ['product_name', 'original_price', 'category_id', 'product_type_id', 'barcode', 'active', 'featured'];
    protected $with = ['tags'];
    protected $appends = ['stock', 'BarcodeUrl', 'type'];
    protected $hidden = ['inventory'];
    public function purchase() {
        return $this->morphMany(PurchaseItem::class, 'purchasable');
    }
    public function category() {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function getStockAttribute() {
        $data = array();
        foreach ($this->inventory as $inventory) {
            $data[$inventory->type->inventory_name] = $inventory->quantity;
        }
        
        if(count($data)) {
            return $data;
        } else {
            return 0;
        }
    }
    
    public function getTypeAttribute() {
        return 'product';
    }
    
    public function scopeBarcode($query, $barcode) {
        return $query->where('barcode', $barcode);
    }

    public function scopeInStock($query) {
        return $query->whereHas('inventory', function($q) {
            $q->where('quantity', '>', 0);
        });
    }

    public function scopeOutOfStock($query) {
        return $query->whereHas('inventory', function($q) {
            $q->where('quantity', '=<', 0);
        })->doesntHave('inventory', 'or');
    }

    public function decrementStock($inventory_id, $quantity) {
        $inventory = $this->inventory()->where('inventory_id', '=', $inventory_id)->first();
        if($inventory && $inventory->quantity > 0) {
            $inventory->decrement('quantity', $quantity);
            if($inventory->save()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getBarcodeUrlAttribute() {
        $uri = \DNS1D::getBarcodePNGPath($this->attributes['barcode'], 'C128',1,64,array(0,0,0), true);
        return url($uri);
    }

    public function incrementStock($inventory_id, $quantity) {
        $inventory = $this->inventory()->where('inventory_id', '=', $inventory_id)->first();
        $inventory->increment('quantity', $quantity);
        if($inventory->save()) {
            return true;
        } else {
            return false;
        }
    }
    public function moveStock($from, $to, $quantity) {
        $inventory_from = $this->inventory()->where('inventory_id', '=', $from)->first();
        if($inventory_from && $inventory_from->quantity < $quantity) {
            return false;
        }
        if($inventory_from) {
            $inventory_from->decrement('quantity', $quantity);
            if($inventory_from->save()) {
                $inventory = $this->inventory()->where('inventory_id', '=', $to)->first();
                if($inventory) {
                    $inventory->increment('quantity', $quantity);
                    if($inventory->save()) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    $inventory = $inventory_from->replicate();
                    $inventory->inventory_id = $to;
                    $inventory->created_at = \Carbon\Carbon::now();
                    $inventory->quantity = $quantity;
                    $inventory->save();
                    return true;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

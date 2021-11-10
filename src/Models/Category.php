<?php

namespace Synciteg\PosSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['category_name'];
    protected $appends = ['ProductsCount'];
    public function products() {
        return $this->hasMany('Synciteg\PosSystem\Models\Product', 'category_id', 'id');
    }

    public function getProductsCountAttribute() {
        return $this->products()->count();
    }
}

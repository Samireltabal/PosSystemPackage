<?php

namespace Synciteg\PosSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Synciteg\PosSystem\Models\Bundle;

class BundleItem extends Model
{
    use HasFactory;

    protected $fillable = ['bundle_id'];

    public function groupable () {
        return $this->morphTo();
    }

    public function bundle()  {
        return $this->belongsTo(Bundle::class, 'bundle_id', 'id');
    }
}

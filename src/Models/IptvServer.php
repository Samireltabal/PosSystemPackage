<?php

namespace Synciteg\PosSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IptvServer extends Model
{
    use HasFactory;
    protected $fillable = ['server_name', 'supplier_id', 'purchase_price'];
    protected $with = ['supplier'];
    protected $appends = ['CodesCount'];

    
    public function supplier() {
        return $this->belongsTo('App\Models\Purchase\Supplier', 'supplier_id', 'id');
    }

    public function availableCodes () {
        return $this->hasMany('Synciteg\PosSystem\Models\IptvCode', 'server_id', 'id')->where('used', '=', false);
    }

    public function Codes () {
        return $this->hasMany('Synciteg\PosSystem\Models\IptvCode', 'server_id', 'id');
    }

    public function getCodesCountAttribute() {
        return $this->availableCodes->count();
    }
}

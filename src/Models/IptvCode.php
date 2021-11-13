<?php

namespace Synciteg\PosSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IptvCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'server_id', 
        'customer_id', 
        'record_id', 
        'periodByMonth', 
        'code', 
        'start_date', 
        'end_date', 
        'used'
    ];
    protected $appends = ['active'];
    public function server() {
        return $this->belongsTo('Synciteg\PosSystem\Models\IptvServer', 'server_id', 'id');
    }

    public function customer() {
        return $this->belongsTo('App\Models\Erp\Customer', 'customer_id', 'id');
    }

    public function record() {
        return $this->hasOne('Synciteg/PosSystem/Models/IptvSubscription', 'record_id', 'id');
    }

    public function getActiveAttribute() {
        if ($this->end_date < \Carbon\Carbon::now() ) {
            return false;
        }
    }

}

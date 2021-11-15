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
        return $this->hasOne('Synciteg\PosSystem\Models\IptvSubscription', 'code_id', 'id');
    }

    public function scopeGetFirstAvailableCode($query, $server_id) {
        return $query->where('server_id', '=', $server_id)->where('used', '=', false)->first();
    }
    public function getActiveAttribute() {
        if ($this->end_date < \Carbon\Carbon::now() ) {
            return false;
        }
    }

    public function scopeCode($query, $code) {
        return $query->where('code', '=', $code)->where('used', '=', true);
    }

    public function markAsUsed($customer, $record) {
        $this->used = true;
        $this->customer_id = $customer;
        $this->record_id = $record;
        $this->start_date = \Carbon\Carbon::now();
        $this->end_date = \Carbon\Carbon::now()->addMonths($this->periodByMonth);
        $this->save();
        return true;
    }

}

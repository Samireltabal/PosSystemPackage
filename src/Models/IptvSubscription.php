<?php

namespace Synciteg\PosSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Synciteg\PosSystem\Traits\Invoiceable;

class IptvSubscription extends Model
{
    use HasFactory, Invoiceable;

    protected $fillable = [
        'customer_id', 
        'code_id', 
        'server_id', 
        'start_date', 
        'end_date',
        'paid', 
        'price', 
        'device_type_id'
    ];

    protected $appends = ['ServerName'];
    protected $hidden = ['code','server'];

    public function customer() {
        return $this->belongsTo('App\Models\Erp\Customer', 'customer_id', 'id');
    }

    public function server() {
        return $this->belongsTo('Synciteg\PosSystem\Models\IptvServer', 'server_id', 'id');
    }

    public function code() {
        return $this->hasOne('Synciteg\PosSystem\Models\IptvCode', 'id', 'code_id');
    }

    public function getServerNameAttribute() {
        return $this->server->server_name;
    }
}

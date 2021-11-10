<?php
    namespace Synciteg\PosSystem\Traits;
    use Synciteg\PosSystem\Models\InvoiceItem;
    
    /**
     * 
     */
    trait Invoiceable
    {
        public function invoicable () {
            return $this->morphMany(InvoiceItem::class, 'invoicable');
        }
    }
    
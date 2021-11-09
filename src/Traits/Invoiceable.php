<?php
    namespace Syncit\PosSystem\Traits;
    use Syncit\PosSystem\Models\InvoiceItem;
    
    /**
     * 
     */
    trait Invoiceable
    {
        public function invoicable () {
            return $this->morphMany(InvoiceItem::class, 'invoicable');
        }
    }
    
<?php
    namespace Synciteg\PosSystem\Traits;
    use Synciteg\PosSystem\Models\BundleItem;
    
    /**
     * 
     */
    trait Groupable
    {
        public function groupable () {
            return $this->morphMany(BundleItem::class, 'groupable');
        }
    }
    
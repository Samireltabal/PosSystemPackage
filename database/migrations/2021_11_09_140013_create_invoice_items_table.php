<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('invoicable');
            $table->double('quantity', 10, 2)->default(1);
            $table->double('selling_price', 10, 2);
            $table->double('discount', 10, 2)->default(0);
            $table->double('total', 10, 2)->default(0);
            $table->boolean('fixed_discount')->default(true);
            $table->unsignedBigInteger('invoice_id')->nullable()->default(null);
            $table->unsignedBigInteger('shift_id');
            $table->boolean('accepted')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_items');
    }
}

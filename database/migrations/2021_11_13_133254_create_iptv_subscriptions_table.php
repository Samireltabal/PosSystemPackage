<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIptvSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iptv_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id');
            $table->bigInteger('code_id');
            $table->bigInteger('server_id');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('paid')->default(true);
            $table->double('price',10, 2);
            $table->bigInteger('device_type_id');
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
        Schema::dropIfExists('iptv_subscriptions');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIptvCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iptv_codes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('server_id');
            $table->bigInteger('customer_id')->nullable();
            $table->bigInteger('record_id');
            $table->double('periodByMonth')->default(12);
            $table->char('code', 20)->unique();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('used');
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
        Schema::dropIfExists('iptv_codes');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->double('amount')->nullable()->default(0);
            $table->double('origin_amount')->nullable()->default(0);
            $table->integer('coupon_id')->nullable();
            $table->tinyInteger('status')->nullable()->comment("0: fail, 1: success, 2:pending");
            $table->text('description')->nullable();
            $table->integer('payment_card_id');
            $table->bigInteger('created_by');
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
        Schema::dropIfExists('payments');
    }
};

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
        Schema::table('plans', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->renameColumn('price', 'monthly_fee');
            $table->integer('genre_id');
            $table->tinyInteger('viewing_restriction');
            $table->tinyInteger('set_back_number_sale');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->renameColumn('monthly_fee', 'price');
            $table->dropColumn('genre_id');
            $table->dropColumn('viewing_restriction');
            $table->dropColumn('set_back_number_sale');
        });
    }
};

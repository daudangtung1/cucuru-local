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
        Schema::table('posts', function (Blueprint $table) {
            $table->tinyInteger('type')->default(0)->comment("1: normal post, 2: member only, 3: sold separately");
            $table->tinyInteger('is_adult')->default(0);
            $table->dropColumn('title');
            $table->dropColumn('status');
            $table->foreignId('plan_id')->references('id')->on('plans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('is_adult');
            $table->string('title');
            $table->tinyInteger('status')->default(1)->comment("1: Active, 2: Draft");
            $table->dropColumn('plan_id');
        });
    }
};

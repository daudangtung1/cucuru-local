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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gender');
            $table->dropColumn('avatar');
            $table->dropColumn('profile');
            $table->dropColumn('is_active');
            $table->dropColumn('full_name');
            $table->dropColumn('phone_number');
            $table->dropColumn('account_type');
            $table->dropColumn('date_of_birth');

            $table->string('user_name')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name');
            $table->text('profile')->nullable();
            $table->string('avatar')->default('');
            $table->dateTime('date_of_birth')->nullable();
            $table->string('phone_number')->unique()->nullable();
            $table->tinyInteger('gender')->nullable()->comment('0: female, 1: male');
            $table->tinyInteger('is_active')->default(1)->comment('0: inactive, 1: active');
            $table->tinyInteger('account_type')->default(1)->comment('1-Member. 2-Admin');

            $table->dropUnique('user_name');
        });
    }
};

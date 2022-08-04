<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('avatar')->default('');
            $table->tinyInteger('account_type')->default('1')->comment('1-Member. 2-Admin');
            $table->dateTime('date_of_birth')->nullable();
            $table->string('phone_number')->unique()->nullable();
            $table->tinyInteger('is_active')->default(1)->comment('0: inactive, 1: active');
            $table->tinyInteger('gender')->nullable()->comment('0: female, 1: male');
            $table->text('profile')->nullable();
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
        Schema::dropIfExists('users');
    }
}

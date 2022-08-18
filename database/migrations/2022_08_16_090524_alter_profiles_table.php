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
        Schema::table('profiles', function (Blueprint $table) {
            $table->date('birth_day')->nullable();
            $table->text('profile_info')->nullable();
            $table->tinyInteger('allow_set_post_sensitive_content')->default(0)->comment('0: not allow, 1: allow');
            $table->tinyInteger('allow_view_post_sensitive_content')->default(0)->comment('0: not allow, 1: allow');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('birth_day');
            $table->dropColumn('profile_info');
            $table->dropColumn('allow_set_post_sensitive_content');
            $table->dropColumn('allow_view_post_sensitive_content');
        });
    }
};

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
        Schema::table('medias', function (Blueprint $table) {
            $table->smallInteger('type')->comment("1: Image, 2: Thumbnail blur, 3: Thumbnail origin, 4 Video")->change();
            $table->string('mime_type')->nullable();
            $table->string('disk')->nullable();
            $table->double('size')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medias', function (Blueprint $table) {
            $table->tinyInteger('type')->comment('1:image,2:video')->change();
            $table->dropColumn('mime_type');
            $table->dropColumn('disk');
            $table->dropColumn('size');
        });
    }
};

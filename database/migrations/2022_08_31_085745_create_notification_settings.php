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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->integer('email_notification')->default(0)->comment('1: Sent every day at 22:00');
            $table->integer('comment_notification')->default(0)->comment('1: Sent every day at 22:00');
            $table->integer('reply_notification')->default(0)->comment('1: Sent every day at 22:00');
            $table->integer('follow_notification')->default(0)->comment('1: Sent every day at 22:00');
            $table->integer('join_fan_club_notification')->default(0)->comment('1: Sent every day at 22:00');
            $table->integer('tip_notification')->default(0)->comment('1: Sent every day at 22:00');
            $table->integer('post_video_compression_notification')->default(0)->comment('1: Sent every day at 22:00');
            $table->integer('following_creator_post_notification')->default(0);
            $table->integer('subscribing_creator_post_notification')->default(0);
            $table->integer('in_site_notification')->default(0);
            $table->integer('cancel_of_plan_notification')->default(0)->comment('1: Sent every day at 22:00');
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
        Schema::dropIfExists('notification_settings');
    }
};

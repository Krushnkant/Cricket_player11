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
        Schema::create('user_subscription_log', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->dateTime('subscription_time');
            $table->integer('package_id')->index();
            $table->string('reason',500)->nullable();
            $table->integer('user_coupon_id')->index();
            $table->dateTime('created_at')->default(\Carbon\Carbon::now());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_subscription_log');
    }
};

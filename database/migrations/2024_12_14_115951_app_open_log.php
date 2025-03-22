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
        Schema::create('app_open_log', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->integer('user_device_id')->index();
            $table->time('visit_time'); // Defines a time field
            $table->text('app_open_source')->nullable();
            $table->text('campaign_id')->nullable();
            $table->text('campaign_detail_id')->nullable();
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
        Schema::dropIfExists('app_open_log');
    }
};

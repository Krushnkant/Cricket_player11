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
        Schema::create('app_campaign_log_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('app_campaign_log_id')->index();
            $table->text('utm_key');
            $table->text('utm_value');
            $table->dateTime('created_at')->default(\Carbon\Carbon::now());
            Schema::dropIfExists('app_campaign_log');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};

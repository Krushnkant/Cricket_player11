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
        Schema::create('app_campaign_log', function (Blueprint $table) {
            $table->id();
            $table->integer('app_campaign_id')->index();
            $table->text('app_campaign_detail');
            $table->text('browser_name');
            $table->text('os_name');
            $table->text('parent_os');
            $table->text('user_agent');
            $table->text('client_ip');
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
        Schema::dropIfExists('app_campaign_log');
    }
};

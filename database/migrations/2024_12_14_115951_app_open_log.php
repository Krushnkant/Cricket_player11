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
            $table->time('visit_time'); // Defines a time field
            $table->text('device_id')->nullable();
            $table->enum('device_type', ['android', 'ios'])->default('android')->notNullable();
            $table->text('brand')->nullable();
            $table->text('model')->nullable();
            $table->text('device')->nullable();
            $table->text('manufacturer')->nullable();
            $table->text('os_version')->nullable();
            $table->text('app_version_name')->nullable();
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

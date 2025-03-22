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
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->integer('login_user_id')->index();
            $table->string('device_id', 100)->nullable();
            $table->enum('device_type', ['android', 'ios'])->default('android')->notNullable();
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('device', 100)->nullable();
            $table->string('manufacturer', 100)->nullable();
            $table->string('os_version', 100)->nullable();
            $table->string('app_version_name', 100)->nullable();
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
        Schema::dropIfExists('user_devices');
    }
};

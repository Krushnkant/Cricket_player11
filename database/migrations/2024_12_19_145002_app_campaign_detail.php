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
        Schema::create('app_campaign_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('app_campaign_id')->index();
            $table->enum('source_type', [1,2,3])->index()->comment('1->Google,2->Facebook,3->Email');
            $table->text('platform_id');
            $table->enum('campaign_type', [1,2])->index()->comment('1->Direct App,2->Landing Page');
            $table->dateTime('created_at')->default(\Carbon\Carbon::now());
            $table->dateTime('updated_at')->default(null)->onUpdate(\Carbon\Carbon::now());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_campaign_detail');
    }
};

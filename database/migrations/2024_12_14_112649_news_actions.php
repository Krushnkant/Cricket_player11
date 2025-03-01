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
        Schema::create('news_actions', function (Blueprint $table) {
            $table->id();
            $table->integer('news_id')->index();
            $table->integer('user_id')->index();
            $table->enum('action', [1,2])->default(1)->comment('1->Like,2->Share')->index();
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
        Schema::dropIfExists('news_actions');
    }
};

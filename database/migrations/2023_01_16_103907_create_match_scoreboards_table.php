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
        Schema::create('match_scoreboards', function (Blueprint $table) {
            $table->id();
            $table->integer('match_player_id')->nullable();
            $table->integer('match_id')->nullable();
            $table->integer('player_id')->nullable();
            $table->integer('ball')->nullable(); 
            $table->integer('run')->nullable();
            $table->integer('four')->nullable();
            $table->integer('six')->nullable();
            $table->float('strike_rate')->nullable();
            $table->float('over')->nullable();
            $table->integer('ball_run')->nullable();
            $table->integer('maiden')->nullable(); 
            $table->integer('wicket')->nullable(); 
            $table->integer('wide')->nullable();
            $table->integer('noball')->nullable();
            $table->float('economy_rate')->nullable();
            $table->dateTime('created_at')->default(\Carbon\Carbon::now());
            $table->dateTime('updated_at')->default(null)->onUpdate(\Carbon\Carbon::now());
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('match_scoreboards');
    }
};

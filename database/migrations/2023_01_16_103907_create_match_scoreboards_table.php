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
            $table->integer('match_player_id')->index();
            $table->integer('match_id')->index();
            $table->integer('player_id')->index();
            $table->integer('ball')->nullable(); 
            $table->integer('run')->nullable();
            $table->integer('four')->nullable();
            $table->integer('six')->nullable();
            $table->float('strike_rate')->nullable();
            $table->float('over')->nullable();
            $table->integer('ball_run')->default(0)->comment('Total Given Run by Bowler');
            $table->integer('maiden')->default(0)->comment('Total Maiden'); 
            $table->integer('wicket')->default(0)->comment('Total Wickets'); 
            $table->integer('wide')->default(0)->comment('Total Wide');
            $table->integer('noball')->default(0)->comment('Total Noball');
            $table->float('economy_rate')->nullable();
            $table->float('fantasy_point')->default(0);
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

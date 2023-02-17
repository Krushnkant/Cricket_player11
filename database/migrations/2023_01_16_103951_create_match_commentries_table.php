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
        Schema::create('match_commentries', function (Blueprint $table) {
            $table->id();
            $table->integer('match_player_id')->nullable(); 
            $table->integer('match_id')->nullable(); 
            $table->integer('batsman_id')->nullable(); 
            $table->integer('bowler_id')->nullable();
            $table->float('ball_number')->nullable();
            $table->integer('ball_type')->comment('1->noBall,2->regular,3->wide,51->other'); 
            $table->integer('run')->nullable();
            $table->integer('is_boundary')->default(0)->comment('0->No,1->Yes'); 
            $table->integer('is_out')->default(0)->comment('0->No,1->Yes'); 
            $table->integer('out_type')->default(0)->comment('1->lbw,2->hit_wkt,3->caught_bowled,4->caught,5->bowled,6->stumped,7->run_out,51->other'); 
            $table->text('out_by_fielder_id')->nullable();
            $table->integer('run_out_batsman_id')->nullable();
            $table->integer('is_extra_run')->default(0)->comment('0->No,1->Yes'); 
            $table->text('commentry',255)->nullable();
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
        Schema::dropIfExists('match_commentries');
    }
};

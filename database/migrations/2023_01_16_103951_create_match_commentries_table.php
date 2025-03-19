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
            $table->integer('match_player_id_bat')->index();
            $table->integer('match_player_id_bowl')->index(); 
            $table->integer('match_id')->index(); 
            $table->integer('batsman_id')->index(); 
            $table->integer('bowler_id')->index();
            $table->float('ball_number')->nullable();
            $table->integer('ball_type')->comment('1->noBall,2->regular,3->wide,51->other'); 
            $table->integer('run')->default(0);
            $table->integer('is_boundary')->default(0)->comment('0->No,1->Yes'); 
            $table->integer('is_out')->default(0)->comment('0->No,1->Yes'); 
            $table->integer('out_type')->default(0)->comment('1->bowled, 2->caught, 3->run_out, 4->hit_wkt, 5->lbw, 6->caught_bowled, 7->stumped'); 
            $table->text('out_by_fielder1_id')->nullable();
            $table->text('out_by_fielder2_id')->nullable();
            $table->integer('run_out_batsman_id')->nullable();
            $table->integer('is_extra_run')->default(0)->comment('0->No,1->Yes'); 
            $table->text('commentry',255)->nullable();
            $table->float('fantasy_point_bat')->default(0);
            $table->float('fantasy_point_bowl')->default(0);
            $table->float('fantasy_point_field1')->default(0);
            $table->float('fantasy_point_field2')->default(0);
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

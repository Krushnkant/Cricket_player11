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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->integer('series_id')->nullable();
            $table->integer('team1_id')->nullable(); 
            $table->string('team1_name',255)->nullable(); 
            $table->integer('team2_id')->nullable(); 
            $table->string('team2_name',255)->nullable(); 
            $table->integer('match_type')->default(1)->comment('1->T20,2->ODI');
            $table->integer('stadium_id')->nullable(); 
            $table->dateTime('start_date')->nullable();
            $table->integer('estatus')->default(1)->comment('1->Active,2->Deactive,3->Deleted,4->Pending');
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
        Schema::dropIfExists('matches');
    }
};

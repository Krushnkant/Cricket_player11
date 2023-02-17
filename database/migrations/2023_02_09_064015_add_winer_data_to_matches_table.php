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
        Schema::table('matches', function (Blueprint $table) {
            $table->integer('winner_team_id')->default(0)->after('start_date');
            $table->string('team1_score',255)->nullable()->after('winner_team_id');
            $table->string('team2_score',255)->nullable()->after('team1_score');
            $table->string('winning_statement',255)->nullable()->after('team2_score');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('matches', function (Blueprint $table) {
            //
        });
    }
};

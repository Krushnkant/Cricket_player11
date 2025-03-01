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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name',255)->nullable();
            $table->text('thumb_img',255)->nullable();
            $table->integer('country_id')->index(); 
            $table->integer('player_type')->default(1)->index()->comment('1->Batsman,2->Bowler,3->WkBatsman,4->Allrounder');
            $table->integer('batting_style')->default(1)->comment('1->Right Hand,2->Left Hand');
            $table->integer('bowling_style')->default(1)->comment('1->Fast,2->Spinner,3->Medium,4->None');
            $table->integer('bowling_arm')->default(1)->comment('1->Left Arm,2->Right Arm,3->Both,4->None');
            $table->integer('is_approved_by_admin')->default(0)->index()->comment('0->Not approved,1->approved');
            $table->integer('estatus')->default(1)->index()->comment('1->Active,2->Deactive,3->Deleted,4->Pending');
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
        Schema::dropIfExists('players');
    }
};

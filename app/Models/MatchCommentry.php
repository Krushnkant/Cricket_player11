<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchCommentry extends Model
{
    use HasFactory;

    public function batsman(){
        return $this->hasOne(Player::class,'id','batsman_id');
    }

    public function bowler(){
        return $this->hasOne(Player::class,'id','bowler_id');
    }

    public function outbyfielder(){
        return $this->hasOne(Player::class,'id','out_by_fielder_id');
    }

    public function runoutbatsman(){
        return $this->hasOne(Player::class,'id','run_out_batsman_id');
    }

    public function getUserAge() {
        return $this->username.', '.$this->age;
    }
}

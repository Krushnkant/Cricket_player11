<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matche extends Model
{
    use HasFactory;

    public function series()
    {
        return $this->belongsTo(Series::class, 'series_id');
    }

    public function team1(){
        return $this->hasOne(Team::class,'id','team1_id');
    }

    public function team2(){
        return $this->hasOne(Team::class,'id','team2_id');
    }

    public function stadium(){
        return $this->hasOne(Stadium::class,'id','stadium_id');
    }

    public function win_team(){
        return $this->hasOne(Team::class,'id','win_team_id');
    }
}

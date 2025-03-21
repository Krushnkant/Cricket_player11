<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeriesTeam extends Model
{
    use HasFactory;

    public function team(){
        return $this->hasOne(Team::class,'id','team_id');
    }

    public function players()
    {
        return $this->hasMany(SeriesTeamPlayer::class, 'series_team_id');
    }
}

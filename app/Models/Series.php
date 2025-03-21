<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    use HasFactory;

    public function tournament(){
        return $this->hasOne(Tournament::class,'id','tournament_id');
    }

    public function series_team(){
        return $this->hasMany(SeriesTeam::class,'series_id','id');
    }

    public function teams()
    {
        return $this->hasMany(SeriesTeam::class, 'series_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stadium extends Model
{
    use HasFactory;

    protected $table = 'stadium';

    public function coutry(){
        return $this->hasOne(Country::class,'id','country_id');
    }
}

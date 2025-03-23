<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCoupon extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_coupon';

    // protected $fillable = [
    //     'user_id',
    //     'coupon_code',
    //     'expiry_date',
    //     'estatus'
    // ];
}

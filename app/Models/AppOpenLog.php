<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppOpenLog extends Model
{
    use HasFactory;

    protected $table = 'app_open_log';

    protected $fillable = [
        'user_id',
        'device_id',
        'device_type',
        'brand',
        'model',
        'device',
        'manufacturer',
        'os_version',
        'app_version_name',
        'utm_referrer',
        'visit_time',
        'created_at',
    ];

    public $timestamps = false;
}

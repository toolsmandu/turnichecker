<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pack extends Model
{
    protected $fillable = [
        'name',
        'description',
        'quota',
        'duration_days',
    ];
}

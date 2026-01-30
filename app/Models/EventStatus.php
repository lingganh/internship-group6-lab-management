<?php
// app/Models/EventStatus.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventStatus extends Model
{
    protected $fillable = ['code', 'name', 'color', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
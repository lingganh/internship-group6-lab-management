<?php
// app/Models/EventCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    protected $fillable = ['code', 'name', 'icon', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
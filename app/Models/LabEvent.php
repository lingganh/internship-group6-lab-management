<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabEvent extends Model
{
    use HasFactory;
    protected $table = 'lab_events';
    protected $fillable = [
        'title',
        'category',
        'start_datetime',
        'end_datetime',
        'description',
        'location',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];
}

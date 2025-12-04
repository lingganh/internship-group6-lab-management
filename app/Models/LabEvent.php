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
        'start',
        'end',
        'description',
        'location',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabEventFile extends Model
{
    protected $fillable = [
        'lab_event_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function event()
    {
        return $this->belongsTo(LabEvent::class);
    }
}
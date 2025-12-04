<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $fillable = [
        'lab_id',
        'name',
        'code',
        'type',
        'status',
        'purchased_date',
        'specifications',
        'notes',
    ];
    protected $casts = [
        'specifications' => 'array',
        'purchased_date'=>'date',
    ];



    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }
}

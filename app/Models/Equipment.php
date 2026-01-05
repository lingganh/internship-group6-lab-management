<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{

    protected $table = 'equipment';
    protected $fillable  = [
        'lab_id',
        'name',
        'code',
        'type',
        'status',
        'purchased_date',
        'specifications',
        'notes',
        'quantity',
        'broken_quantity',
        'actual_quantity',
    ];
    protected $casts = [
        'purchased_date'=>'date',
    ];

    protected static function booted()
    {
        static::saving(function ($equipment)
        {
            $equipment->actual_quantity =
                max(0, $equipment->quantity - $equipment->broken_quantity);
        });
    }


    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }
}

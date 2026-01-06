<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabEquipmentItem extends Model
{
    protected $table = 'lab_equipment_items';
    protected $fillable = [
        'lab_id',
        'equipment_id',
        'quantity',
        'broken_quantity',
        'actual_quantity',
    ];

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->actual_quantity = max(0, $item->quantity - $item->broken_quantity);
        });
    }
    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}

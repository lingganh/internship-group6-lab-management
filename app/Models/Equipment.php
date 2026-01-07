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
    ];
    protected $casts = [
        'purchased_date'=>'date',
    ];


    public function labItems()
    {
        return $this->hasMany(LabEquipmentItem::class);
    }
}

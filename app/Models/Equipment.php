<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipment extends Model
{
    use HasFactory;

  

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
        'specifications' => 'array',
        'purchased_date' => 'date',
    ];


    public function labItems()
    {
        return $this->hasMany(LabEquipmentItem::class);
    }
}

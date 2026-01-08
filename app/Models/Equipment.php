<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipment extends Model
{
    use HasFactory;



    protected $table = 'equipment';
    protected $fillable  = [
        'name',
        'code',
        'type',
        'status',
        'purchased_date',
        'specifications',
        'notes',
    ];
    protected $casts = [
        'purchased_date' => 'date',
    ];


    public function labItems()
    {
        return $this->hasMany(LabEquipmentItem::class);
    }
}

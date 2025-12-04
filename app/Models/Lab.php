<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Lab extends Model
{

    protected $fillable = [
        'name',
        'code',
        'location',
        'capacity',
        'description',
        'facilities',
        'status',
        'image_url',
        'created_by',
    ];
    protected $casts = [
        'facilities' => 'array',
    ];


    public function equipment()
    {
       return  $this->hasMany(Equipment::class);
    }


}

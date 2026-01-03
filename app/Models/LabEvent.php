<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabEvent extends Model
{
    use HasFactory;
    protected $table = 'lab_events';
    // protected $fillable = [
    //     'title',
    //     'category',
    //     'start',
    //     'end',
    //     'description',
    //     'status',
    //     'user_id',
    // ];
    protected $guarded = [];
    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];
     public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function files()
    {
        return $this->hasMany(LabEventFile::class);
    }

      public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_code', 'code');
    }
}

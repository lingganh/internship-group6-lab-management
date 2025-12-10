<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    protected $fillable = [
        'full_name',
        'code',
        'class_name',
    ];
    public function groups():BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_student', 'group_id', 'student_id');
    }
}

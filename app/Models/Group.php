<?php

namespace App\Models;

use App\Enums\GroupStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    protected $fillable = [
        'name',
        'description',
        'leader_id',
    ];

    public function students():BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'group_student', 'group_id', 'student_id');
    }

    public function leader():BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function getGroupStatusAttribute(): string
    {
        if($this->status === GroupStatus::Active->value)
        {
            return '<span class="badge bg-info bg-opacity-10 text-info"> Hoạt động </span>';
        }

        if($this->status === GroupStatus::Inactive->value)
        {
            return '<span class="badge bg-info bg-opacity-10 text-warning"> Ngừng hoạt động </span>';
        }


        return '';
    }

}

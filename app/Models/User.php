<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\Role as RoleEnum;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'sso_id',
        'code',
        'phone',
        'class_name',
        'gender',
        'date_of_birth',
        'thumbnail',
        'last_login_at',
        'access_token',
        'remember_token',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role():BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role->name === $roleName;
    }

    public function groups():BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user', 'group_id', 'user_id');
    }

    public function getRoleTextAttribute()
    {
        if ($this->role->name === RoleEnum::Admin->value) {
            return '<span class="badge bg-info bg-opacity-10 text-info"> Quản trị viên </span>';
        }
        if ($this->role->name === RoleEnum::Officer->value) {
            return '<span class="badge bg-info bg-opacity-10 text-warning"> Cán Bộ Khoa</span>';
        }

        if ($this->role->name === RoleEnum::Teacher->value) {
            return '<span class="badge bg-info bg-opacity-10 text-warning"> Giảng Viên</span>';
        }

        if ($this->role->name === RoleEnum::Student->value) {
            return '<span class="badge bg-info bg-opacity-10 text-success"> Sinh viên</span>';
        }

        return '<span class="badge bg-info bg-opacity-10 text-success">' . $this->role?->name . '</span>';
    }

    public function getRoleValueAttribute()
    {
        if( $this->role->name === RoleEnum::Student->value ){
            return  RoleEnum::Student->name;
        }
        if( $this->role->name === RoleEnum::Admin->value ){
            return  RoleEnum::Admin->name;
        }
        if( $this->role->name === RoleEnum::Officer->value ){
            return  RoleEnum::Officer->name;
        }
        if( $this->role->name === RoleEnum::Teacher->value ){
            return  RoleEnum::Teacher->name;
        }
        return RoleEnum::Student->name;
    }
}

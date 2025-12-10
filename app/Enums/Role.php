<?php

namespace App\Enums;

enum Role:string
{
    case Admin = 'admin';
    case Teacher = 'teacher';
    case Officer = 'officer';
    case Student = 'student';

    public static function getLabels(): array
    {
        return [
            self::Admin->value => self::Admin->label(),
            self::Teacher->value => self::Teacher->label(),
            self::Officer->value => self::Officer->label(),
            self::Student->value => self::Student->label(),
        ];
    }

    public function label():string
    {
        return match ($this){
            self::Admin => 'Quản trị viên',
            self::Teacher => 'Giảng viên',
            self::Officer => 'Cán bộ khoa',
            self::Student => 'Sinh viên',
        };
    }

    public static function displayAll()
    {
        $roles = [];
        foreach (self::cases() as $role){
            $roles[$role->name] = $role->label();
        }
        return $roles;
    }

}

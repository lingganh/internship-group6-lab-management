<?php

namespace App\Enums;

enum GroupStatus:string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Archived = 'archived';

    public function label(): string
    {
        return match($this) {
            GroupStatus::Active => 'Hoạt động',
            GroupStatus::Inactive => 'Ngừng hoạt động',
            GroupStatus::Archived => 'Lưu trữ',
        };
    }

    public static function displayAll()
    {
        $status = [];
        foreach (self::cases() as $items){
            $status[$items->value] = $items->label();
        }
        return $status;
    }
}

<?php

namespace App\Enums;

enum UserStatus:string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Archived = 'archived';

    public function label(): string
    {
        return match($this) {
            UserStatus::Pending => 'Chờ duyệt',
            UserStatus::Approved => 'Đã duyệt',
            UserStatus::Archived => 'Lưu trữ',
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

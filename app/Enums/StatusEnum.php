<?php

namespace App\Enums;

enum StatusEnum: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';


    public function label(): string
    {
        return match($this) {
            self::Pending => 'قيد الانتظار',
            self::Accepted => 'تم القبول',
            self::Rejected => 'مرفوض',
        };
    }
}

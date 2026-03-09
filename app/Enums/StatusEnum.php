<?php

namespace App\Enums;

enum StatusEnum: string
{
    case Pending = 'Pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';


    public function label(): string
    {
        return match($this) {
            self::Pending => 'قيد الانتظار',
            self::ACCEPTED => 'تم القبول',
            self::REJECTED => 'مرفوض',
        };
    }
}

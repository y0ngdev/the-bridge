<?php

namespace App\Enums;

enum CommunicationType: string
{
    case Call = 'call';
    case SMS = 'sms';
    case Email = 'email';
    case WhatsApp = 'whatsapp';
    case Visit = 'visit';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Call => 'Phone Call',
            self::SMS => 'SMS',
            self::Email => 'Email',
            self::WhatsApp => 'WhatsApp',
            self::Visit => 'Physical Visit',
            self::Other => 'Other',
        };
    }
}

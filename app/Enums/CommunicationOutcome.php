<?php

namespace App\Enums;

enum CommunicationOutcome: string
{
    case Unsuccessful = 'unsuccessful';
    case Successful = 'successful';
    case NoAnswer = 'no_answer';
    case Busy = 'busy';
    case WrongNumber = 'wrong_number';
    case Voicemail = 'voicemail';
    case ScheduledCallback = 'scheduled_callback';

    public function label(): string
    {
        return match ($this) {
            self::Unsuccessful => 'Unsuccessful',
            self::Successful => 'Successful',
            self::NoAnswer => 'No Answer',
            self::Busy => 'Busy / Line Engaged',
            self::WrongNumber => 'Wrong Number',
            self::Voicemail => 'Left Voicemail',
            self::ScheduledCallback => 'Scheduled Callback',
        };
    }
}

<?php

namespace App\Enums;

enum RedemptionWeekDay: string
{
    case CulturalDay = 'cultural_day';
    case WordNight = 'word_night';
    case PowerNight = 'power_night';
    case DramaNight = 'drama_night';
    case ChoirConcert = 'choir_concert';
    case AlumniReunion = 'alumni_reunion';
    case HandingOverService = 'handing_over_service';

    public function label(): string
    {
        return match ($this) {
            self::CulturalDay => 'Cultural Day',
            self::WordNight => 'Word Night',
            self::PowerNight => 'Power Night',
            self::DramaNight => 'Drama Night',
            self::ChoirConcert => 'Choir Concert',
            self::AlumniReunion => 'Alumni Reunion',
            self::HandingOverService => 'Handing Over Service',
        };
    }

    public function order(): int
    {
        return match ($this) {
            self::CulturalDay => 1,
            self::WordNight => 2,
            self::PowerNight => 3,
            self::DramaNight => 4,
            self::ChoirConcert => 5,
            self::AlumniReunion => 6,
            self::HandingOverService => 7,
        };
    }

    /**
     * Get all days in order.
     *
     * @return array<RedemptionWeekDay>
     */
    public static function ordered(): array
    {
        $days = self::cases();
        usort($days, fn ($a, $b) => $a->order() <=> $b->order());

        return $days;
    }
}

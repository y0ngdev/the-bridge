<?php

namespace App\Enums;

enum Unit: string
{

    case PresidentsUnit = "President's Unit (Non Worker)";
    case Academic = 'Academic Unit';
    case Alumni = 'Alumni Relations Unit';
    case BibleStudy = 'Bible Study Unit';
    case Choir = 'Choir Unit';
    case DDF = 'Drama and Dance Fellowship'; // DDF - found in 2011-2012, 2007-2008 data
    case Drama = 'Drama Unit';
    case Editorial = 'Editorial Unit';
    case Evangelism = 'Evangelism Unit';
    case FollowUpCounselling = 'Follow up/Counselling Unit';
    case Library = 'Library Unit';
    case MediaAndAmbience = 'Media and Ambience Unit'; // Previously Publicity Unit
    case Organising = 'Organising Unit';
    case Prayer = 'Prayer Unit';
    case Sanctuary = 'Sanctuary Keeping Unit';
    case Ushering = 'Ushering Unit';
    case Welfare = 'Welfare Unit';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

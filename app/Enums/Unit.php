<?php

namespace App\Enums;

enum Unit: string
{
    case Academic = 'Academic Unit';
    case Alumni = 'Alumni Relations Unit';
    case BibleStudy = 'Bible Study Unit';
    //TVM
    case Choir = 'Choir Unit';
    //DDF
    case Drama = 'Drama Unit';
    case Editorial = 'Editorial Unit';
    case Evangelism = 'Evangelism Unit';
    case FollowUpCounselling = 'Follow up/Counselling Unit';
    case Library = 'Library Unit';

    //MediaAndAmbience was previously publicity unit
    case MediaAndAmbience = 'Media and Ambience Unit';
    case Organising = 'Organising Unit';
    case Prayer = 'Prayer Unit';
    case PresidentsUnit = "President's Unit";
    case Sanctuary = "Sanctuary Keeping Unit";
    case Ushering = 'Ushering Unit';
    case Welfare = 'Welfare Unit';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

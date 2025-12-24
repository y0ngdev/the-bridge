<?php

namespace App\Enums;

enum PastExcoOffice: string
{
    // Central Executive Positions
    case President = 'President';
    case VicePresidentAdmin = 'Vice President Administration';
    case VicePresidentChurchGrowth = 'Vice President Church Growth';
    case PrayerSecretary = 'Prayer Secretary';
    case BibleStudySecretary = 'Bible Study Secretary';
    case GeneralSecretary = 'General Secretary';
    case FinancialSecretary = 'Financial Secretary';
    case BrothersCoordinator = "Brother's Coordinator";
    case SistersCoordinator = "Sister's Coordinator";

    // Unit Coordinators
    case AcademicCoordinator = 'Academic Coordinator';
    case AsstAcademicCoordinator = 'Asst. Academic Coordinator';
    case AlumniCoordinator = 'Alumni Coordinator';
    case AsstAlumniCoordinator = 'Asst. Alumni Coordinator';
    case AsstBibleStudyCoordinator = 'Asst. Bible Study Coordinator';
    case ChoirCoordinator = 'Choir Coordinator';
    case AsstChoirCoordinator = 'Asst. Choir Coordinator';
    case DramaCoordinator = 'Drama Coordinator';
    case AsstDramaCoordinator = 'Asst. Drama Coordinator';
    case EditorialCoordinator = 'Editorial Coordinator';
    case AsstEditorialCoordinator = 'Asst. Editorial Coordinator';
    case EvangelismCoordinator = 'Evangelism Coordinator';
    case AsstEvangelismCoordinator = 'Asst. Evangelism Coordinator';
    case FollowUpCounsellingCoordinator = 'Follow up/Counselling Coordinator';
    case AsstFollowUpCounsellingCoordinator = 'Asst. Follow up/Counselling Coordinator';
    case LibraryCoordinator = 'Library Coordinator';
    case AsstLibraryCoordinator = 'Asst. Library Coordinator';
    case MediaAndAmbienceCoordinator = 'Media and Ambience Coordinator';
    case AsstMediaAndAmbienceCoordinator = 'Asst. Media and Ambience Coordinator';
    case OrganisingCoordinator = 'Organising Coordinator';
    case AsstOrganisingCoordinator = 'Asst. Organising Coordinator';

    case AsstPrayerCoordinator = 'Asst. Prayer Coordinator';
    case UsheringCoordinator = 'Ushering Coordinator';
    case AsstUsheringCoordinator = 'Asst. Ushering Coordinator';
    case WelfareCoordinator = 'Welfare Coordinator';
    case AsstWelfareCoordinator = 'Asst. Welfare Coordinator';

    // Other Positions
    case LevelCoordinator = 'Level Coordinator';
    case HundredLevelCoordinator = '100 Level Coordinator';
    case ICTCoordinator = 'ICT Coordinator';
    case TransportCoordinator = 'Transport Coordinator';
    case DirectorOfCommerce = 'Director of Commerce';
    case HallRep = 'Hall Reps Coordinator';
    case AsstHallRep = 'Asst. Hall Reps Coordinator';

    case AsstBrothersCoordinator = "Asst. Brother's Coordinator";
    case AsstSistersCoordinator = "Asst. Sister's Coordinator";
    case AsstGeneralSecretary = 'Asst. General Secretary';
    case UABSPDS = 'UABS/PDS';
    case AsstUABSPDS = 'Asst. UABS/PDS';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

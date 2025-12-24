<?php

namespace App\Enums;

enum Department: string
{
    //    Todo: errors in the abbreviation
    // School of Agriculture and Agricultural Technology (SAAT)
    case AgriculturalExtension = 'AEC';
    case AgriculturalEconomics = 'ARE';
    case AnimalProduction = 'APH';
    case CropSoilPest = 'CSP';
    case EcotourismWildlife = 'EWM';
    case FisheriesAquaculture = 'FAT';
    case FoodScience = 'FST';
    case ForestryWood = 'FWT';
    case NutritionDietetics = 'NDT';


    // School of Engineering & Engineering Technology (SEET)
    case AgriculturalEnvironmentalEngineering = 'AGE';
    case CivilEnvironmentalEngineering = 'CVE';
    case ComputerEngineering = 'CPE';
    case ElectricalElectronics = 'EEE';
    case IndustrialProduction = 'IPE';
    case InformationCommunicationTech = 'ICT';
    case MechanicalEngineering = 'MEE';
    case MetallurgicalMaterials = 'MME';
    case MiningEngineering = 'MNE';


    // School of Computing (SOC)
    case ComputerScience = 'CSC';
    case CyberSecurity = 'CYS';
    case InformationSystems = 'IFS';
    case InformationTechnology = 'IFT';
    case SoftwareEngineering = 'SEN';
    case DataScience = 'DSC';

    // School of Earth & Mineral Sciences (SEMS)
    case AppliedGeophysics = 'AGP';
    case AppliedGeology = 'AGY';
    case MarineScience = 'MST';
    case MeteorologicalClimate = 'MCS';
    case RemoteSensingGIS = 'RSG';

    // School of Environmental Technology (SET)
    case Architecture = 'ARC';
    case BuildingTechnology = 'BDG';
    case EstateManagement = 'ESM';
    case IndustrialDesign = 'IDD';
    case QuantitySurveying = 'QSV';
    case SurveyingGeoinformatics = 'SVG';
    case UrbanRegionalPlanning = 'URP';

    // School of Life Sciences (SLS)
    case Biochemistry = 'BCH';
    case Biology = 'BIO';
    case Biotechnology = 'BTH';
    case Microbiology = 'MCB';

    // School of Physical Sciences (SPS)
    case Chemistry = 'CHE';
    case GeneralStudies = 'GNS';
    case Mathematics = 'MTS';
    case Physics = 'PHY';
    case Statistics = 'STA';

    // School of Logistics and Innovation Technology (SLIT)
    case BusinessInformationTech = 'BIT';
    case EntrepreneurshipManagement = 'EMT';
    case LogisticsTransport = 'LTT';
    case ProjectManagement = 'PMT';
    case SecuritiesInvestment = 'SIMT';
    case LibraryManagement = 'LMT';
    case TransportManagement = 'TMT';

    // College of Health Sciences
    case Anatomy = 'ANA';
    case BiomedicalTechnology = 'BMT';
    case MedicalLabScience = 'MLS';
    case Physiology = 'PHS';
    case MedicineSurgery = 'MBBS';
    case PublicHealth = 'PUH';

    public function label(): string
    {
        return match ($this) {
                // SAAT
            self::AgriculturalExtension => 'Agricultural Extension & Communication Technology',
            self::AgriculturalEconomics => 'Agricultural & Resource Economics',
            self::AnimalProduction => 'Animal Production & Health',
            self::CropSoilPest => 'Crop, Soil & Pest Management',
            self::EcotourismWildlife => 'Ecotourism & Wildlife Management',
            self::FisheriesAquaculture => 'Fisheries & Aquaculture Technology',
            self::FoodScience => 'Food Science & Technology',
            self::ForestryWood => 'Forestry & Wood Technology',
            self::NutritionDietetics => 'Nutrition and Dietetics',

                // SEET
            self::AgriculturalEnvironmentalEngineering => 'Agricultural & Environmental Engineering',
            self::CivilEnvironmentalEngineering => 'Civil & Environmental Engineering',
            self::ComputerEngineering => 'Computer Engineering',
            self::ElectricalElectronics => 'Electrical & Electronics Engineering',
            self::IndustrialProduction => 'Industrial & Production Engineering',
            self::InformationCommunicationTech => 'Information & Communication Technology',
            self::MechanicalEngineering => 'Mechanical Engineering',
            self::MetallurgicalMaterials => 'Metallurgical & Materials Engineering',
            self::MiningEngineering => 'Mining Engineering',

                // SOC
            self::ComputerScience => 'Computer Science',
            self::CyberSecurity => 'Cyber Security',
            self::InformationSystems => 'Information Systems',
            self::InformationTechnology => 'Information Technology',
            self::SoftwareEngineering => 'Software Engineering',
            self::DataScience => 'Data Science',

                // SEMS
            self::AppliedGeophysics => 'Applied Geophysics',
            self::AppliedGeology => 'Applied Geology',
            self::MarineScience => 'Marine Science & Technology',
            self::MeteorologicalClimate => 'Meteorology & Climate Science',
            self::RemoteSensingGIS => 'Remote Sensing & GIS',


                // SET
            self::Architecture => 'Architecture',
            self::BuildingTechnology => 'Building Technology',
            self::EstateManagement => 'Estate Management',
            self::IndustrialDesign => 'Industrial Design',
            self::QuantitySurveying => 'Quantity Surveying',
            self::SurveyingGeoinformatics => 'Surveying and Geoinformatics',
            self::UrbanRegionalPlanning => 'Urban & Regional Planning',


                // SLS
            self::Biochemistry => 'Biochemistry',
            self::Biology => 'Biology',
            self::Biotechnology => 'Biotechnology',
            self::Microbiology => 'Microbiology',

                // SPS
            self::Chemistry => 'Chemistry',
            self::GeneralStudies => 'General Studies',
            self::Mathematics => 'Mathematics',
            self::Physics => 'Physics',
            self::Statistics => 'Statistics',

                // SLIT
            self::BusinessInformationTech => 'Business Information Technology',
            self::EntrepreneurshipManagement => 'Entrepreneurship Management Technology',
            self::LibraryManagement => 'Library Management Technology',
            self::ProjectManagement => 'Project Management Technology',
            self::TransportManagement => 'Transport Management Technology',

                // CHS
            self::Anatomy => 'Anatomy',
            self::BiomedicalTechnology => 'Biomedical Technology',
            self::MedicalLabScience => 'Medical Laboratory Science',
            self::Physiology => 'Physiology',
            self::MedicineSurgery => 'Medicine and Surgery',
            self::PublicHealth => 'Public Health',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn(self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}

<?php

namespace App\Enums;

enum Department: string
{
    //    Todo: errors in the abbreviation
    // School of Agriculture and Agricultural Technology (SAAT)
    case AgriculturalExtension = 'AECT';
    case AgriculturalEconomics = 'ARE';
    case AnimalProduction = 'APH';
    case CropSoilPest = 'CSP';
    case EcotourismWildlife = 'EWM';
    case FisheriesAquaculture = 'FAT';
    case FoodScience = 'FST';
    case ForestryWood = 'FWT';
    case NutritionDietetics = 'NUD';

    // School of Computing (SOC)
    case ComputerScience = 'CSC';
    case CyberSecurity = 'CYS';
    case InformationSystems = 'IFS';
    case InformationTechnology = 'IFT';
    case SoftwareEngineering = 'SEN';
    case DataScience = 'DSC';

    // School of Earth & Mineral Sciences (SEMS)
    case AppliedGeophysics = 'AGP';
    case AppliedGeology = 'AGL';
    case MarineScience = 'MST';
    case MeteorologicalClimate = 'MCS';
    case RemoteSensingGIS = 'RSG';

    // School of Engineering & Engineering Technology (SEET)
    case AgriculturalEngineering = 'AGE';
    case BiomedicalEngineering = 'BME';
    case ChemicalEngineering = 'CHE';
    case CivilEngineering = 'CVE';
    case ComputerEngineering = 'CPE';
    case ElectricalElectronics = 'EEE';
    case IndustrialProduction = 'IPE';
    case InformationCommunication = 'ICE';
    case MechanicalEngineering = 'MEE';
    case MetallurgicalMaterials = 'MME';
    case MechatronicsEngineering = 'MCE';
    case MiningEngineering = 'MNE';

    // School of Environmental Technology (SET)
    case Architecture = 'ARC';
    case BuildingTechnology = 'BDT';
    case EstateManagement = 'ESM';
    case IndustrialDesign = 'IDD';
    case QuantitySurveying = 'QSV';
    case SurveyingGeoinformatics = 'SVG';
    case UrbanRegionalPlanning = 'URP';
    case TextileDesign = 'TDT';

    // School of Life Sciences (SLS)
    case Biochemistry = 'BCH';
    case Biology = 'BIO';
    case Biotechnology = 'BTH';
    case Microbiology = 'MCB';

    // School of Physical Sciences (SPS)
    case Chemistry = 'CHM';
    case Mathematics = 'MTH';
    case Physics = 'PHY';
    case Statistics = 'STA';

    // School of Logistics and Innovation Technology (SLIT)
    case BusinessInformationTech = 'BIT';
    case BusinessManagementTech = 'BMT';
    case EntrepreneurshipManagement = 'EMT';
    case LibraryManagement = 'LMT';
    case ProjectManagement = 'PMT';
    case TransportManagement = 'TMT';

    // College of Health Sciences
    case Anatomy = 'ANA';
    case BiomedicalTechnology = 'BMDT';
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
            self::MeteorologicalClimate => 'Meteorological and Climate Science',
            self::RemoteSensingGIS => 'Remote Sensing & GIS',

            // SEET
            self::AgriculturalEngineering => 'Agricultural Engineering',
            self::BiomedicalEngineering => 'Biomedical Engineering',
            self::ChemicalEngineering => 'Chemical Engineering',
            self::CivilEngineering => 'Civil Engineering',
            self::ComputerEngineering => 'Computer Engineering',
            self::ElectricalElectronics => 'Electrical & Electronics Engineering',
            self::IndustrialProduction => 'Industrial & Production Engineering',
            self::InformationCommunication => 'Information & Communication Engineering',
            self::MechanicalEngineering => 'Mechanical Engineering',
            self::MetallurgicalMaterials => 'Metallurgical & Materials Engineering',
            self::MechatronicsEngineering => 'Mechatronics Engineering',
            self::MiningEngineering => 'Mining Engineering',

            // SET
            self::Architecture => 'Architecture',
            self::BuildingTechnology => 'Building Technology',
            self::EstateManagement => 'Estate Management',
            self::IndustrialDesign => 'Industrial Design',
            self::QuantitySurveying => 'Quantity Surveying',
            self::SurveyingGeoinformatics => 'Surveying and Geoinformatics',
            self::UrbanRegionalPlanning => 'Urban & Regional Planning',
            self::TextileDesign => 'Textile Design Technology',

            // SLS
            self::Biochemistry => 'Biochemistry',
            self::Biology => 'Biology',
            self::Biotechnology => 'Biotechnology',
            self::Microbiology => 'Microbiology',

            // SPS
            self::Chemistry => 'Chemistry',
            self::Mathematics => 'Mathematical Sciences',
            self::Physics => 'Physics',
            self::Statistics => 'Statistics',

            // SLIT
            self::BusinessInformationTech => 'Business Information Technology',
            self::BusinessManagementTech => 'Business Management Technology',
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
            fn (self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}

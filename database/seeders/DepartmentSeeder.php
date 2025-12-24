<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            // School of Agriculture and Agricultural Technology (SAAT)
            ['code' => 'AEC', 'name' => 'Agricultural Extension & Communication Technology', 'school' => 'SAAT'],
            ['code' => 'ARE', 'name' => 'Agricultural & Resource Economics', 'school' => 'SAAT'],
            ['code' => 'APH', 'name' => 'Animal Production & Health', 'school' => 'SAAT'],
            ['code' => 'CSP', 'name' => 'Crop, Soil & Pest Management', 'school' => 'SAAT'],
            ['code' => 'EWM', 'name' => 'Ecotourism & Wildlife Management', 'school' => 'SAAT'],
            ['code' => 'FAT', 'name' => 'Fisheries & Aquaculture Technology', 'school' => 'SAAT'],
            ['code' => 'FST', 'name' => 'Food Science & Technology', 'school' => 'SAAT'],
            ['code' => 'FWT', 'name' => 'Forestry & Wood Technology', 'school' => 'SAAT'],
            ['code' => 'NDT', 'name' => 'Nutrition and Dietetics', 'school' => 'SAAT'],

            // School of Engineering & Engineering Technology (SEET)
            ['code' => 'AGE', 'name' => 'Agricultural & Environmental Engineering', 'school' => 'SEET'],
            ['code' => 'CVE', 'name' => 'Civil & Environmental Engineering', 'school' => 'SEET'],
            ['code' => 'CPE', 'name' => 'Computer Engineering', 'school' => 'SEET'],
            ['code' => 'EEE', 'name' => 'Electrical & Electronics Engineering', 'school' => 'SEET'],
            ['code' => 'IPE', 'name' => 'Industrial & Production Engineering', 'school' => 'SEET'],
            ['code' => 'ICT', 'name' => 'Information & Communication Technology', 'school' => 'SEET'],
            ['code' => 'MEE', 'name' => 'Mechanical Engineering', 'school' => 'SEET'],
            ['code' => 'MME', 'name' => 'Metallurgical & Materials Engineering', 'school' => 'SEET'],
            ['code' => 'MNE', 'name' => 'Mining Engineering', 'school' => 'SEET'],

            // School of Computing (SOC)
            ['code' => 'CSC', 'name' => 'Computer Science', 'school' => 'SOC'],
            ['code' => 'CYS', 'name' => 'Cyber Security', 'school' => 'SOC'],
            ['code' => 'IFS', 'name' => 'Information Systems', 'school' => 'SOC'],
            ['code' => 'IFT', 'name' => 'Information Technology', 'school' => 'SOC'],
            ['code' => 'SEN', 'name' => 'Software Engineering', 'school' => 'SOC'],
            ['code' => 'DSC', 'name' => 'Data Science', 'school' => 'SOC'],

            // School of Earth & Mineral Sciences (SEMS)
            ['code' => 'AGP', 'name' => 'Applied Geophysics', 'school' => 'SEMS'],
            ['code' => 'AGY', 'name' => 'Applied Geology', 'school' => 'SEMS'],
            ['code' => 'MST', 'name' => 'Marine Science & Technology', 'school' => 'SEMS'],
            ['code' => 'MCS', 'name' => 'Meteorology & Climate Science', 'school' => 'SEMS'],
            ['code' => 'RSG', 'name' => 'Remote Sensing & GIS', 'school' => 'SEMS'],

            // School of Environmental Technology (SET)
            ['code' => 'ARC', 'name' => 'Architecture', 'school' => 'SET'],
            ['code' => 'BDG', 'name' => 'Building Technology', 'school' => 'SET'],
            ['code' => 'ESM', 'name' => 'Estate Management', 'school' => 'SET'],
            ['code' => 'IDD', 'name' => 'Industrial Design', 'school' => 'SET'],
            ['code' => 'QSV', 'name' => 'Quantity Surveying', 'school' => 'SET'],
            ['code' => 'SVG', 'name' => 'Surveying and Geoinformatics', 'school' => 'SET'],
            ['code' => 'URP', 'name' => 'Urban & Regional Planning', 'school' => 'SET'],

            // School of Life Sciences (SLS)
            ['code' => 'BCH', 'name' => 'Biochemistry', 'school' => 'SLS'],
            ['code' => 'BIO', 'name' => 'Biology', 'school' => 'SLS'],
            ['code' => 'BTH', 'name' => 'Biotechnology', 'school' => 'SLS'],
            ['code' => 'MCB', 'name' => 'Microbiology', 'school' => 'SLS'],

            // School of Physical Sciences (SPS)
            ['code' => 'CHE', 'name' => 'Chemistry', 'school' => 'SPS'],
            ['code' => 'GNS', 'name' => 'General Studies', 'school' => 'SPS'],
            ['code' => 'MTS', 'name' => 'Mathematics', 'school' => 'SPS'],
            ['code' => 'PHY', 'name' => 'Physics', 'school' => 'SPS'],
            ['code' => 'STA', 'name' => 'Statistics', 'school' => 'SPS'],

            // School of Logistics and Innovation Technology (SLIT)
            ['code' => 'BIT', 'name' => 'Business Information Technology', 'school' => 'SLIT'],
            ['code' => 'EMT', 'name' => 'Entrepreneurship Management Technology', 'school' => 'SLIT'],
            ['code' => 'LTT', 'name' => 'Logistics and Transport Technology', 'school' => 'SLIT'],
            ['code' => 'PMT', 'name' => 'Project Management Technology', 'school' => 'SLIT'],
            ['code' => 'SIMT', 'name' => 'Securities and Investment Management Technology', 'school' => 'SLIT'],
            ['code' => 'LMT', 'name' => 'Library Management Technology', 'school' => 'SLIT'],
            ['code' => 'TMT', 'name' => 'Transport Management Technology', 'school' => 'SLIT'],

            // College of Health Sciences (SBMS)
            ['code' => 'ANA', 'name' => 'Anatomy', 'school' => 'SBMS'],
            ['code' => 'BMT', 'name' => 'Biomedical Technology', 'school' => 'SBMS'],
            ['code' => 'MLS', 'name' => 'Medical Laboratory Science', 'school' => 'SBMS'],
            ['code' => 'PHS', 'name' => 'Physiology', 'school' => 'SBMS'],
            ['code' => 'MBBS', 'name' => 'Medicine and Surgery', 'school' => 'SBMS'],
            ['code' => 'PUH', 'name' => 'Public Health', 'school' => 'SBMS'],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['code' => $department['code']],
                $department
            );
        }
    }
}

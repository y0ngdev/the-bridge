<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AlumniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder calls all individual year seeders.
     */
    public function run(): void
    {
        $this->call([
            
            Alumni2000_2001Seeder::class,
            Alumni2001_2002Seeder::class,
            Alumni2002_2003Seeder::class,
            Alumni2003_2004Seeder::class,
            Alumni2004_2005Seeder::class,
            Alumni2005_2006Seeder::class,
            Alumni2006_2007Seeder::class,
            Alumni2007_2008Seeder::class,
            Alumni2008_2009Seeder::class,
            Alumni2009_2010Seeder::class,
            Alumni2010_2011Seeder::class,
            Alumni2011_2012Seeder::class,
            Alumni2012_2013Seeder::class,
            Alumni2013_2014Seeder::class,
            Alumni2014_2015Seeder::class,
            Alumni2015_2016Seeder::class,
            Alumni2016_2017Seeder::class,
            Alumni2017_2018Seeder::class,
            Alumni2018_2019Seeder::class,
        ]);
    }
}

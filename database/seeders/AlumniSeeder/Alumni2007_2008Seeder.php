<?php

namespace Database\Seeders\AlumniSeeder;

use App\Models\Alumnus;
use App\Models\Tenure;
use Illuminate\Database\Seeder;

class Alumni2007_2008Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenure = Tenure::updateOrCreate(
            ['year' => '2007-2008'],
            ['name' => '2007-2008 Set']
        );

        $alumni = [
            [
                'name' => 'ADENIYI ONAOPEPO',
                'email' => null,
                'phones' => ['08067117231'],
                'department' => 'QSV',
                'gender' => 'F',
                'birth_date' => '2000-01-13',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'OHIKHENA IFEOLUWA',
                'email' => null,
                'phones' => ['08057319263'],
                'department' => 'CHE',
                'gender' => null,
                'birth_date' => '2000-01-18',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'ADEDEJI TOLA',
                'email' => null,
                'phones' => ['08068976897'],
                'department' => 'MET',
                'gender' => null,
                'birth_date' => '2000-12-22',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'ODUNAYO G.O',
                'email' => null,
                'phones' => ['08035125076'],
                'department' => 'MET',
                'gender' => null,
                'birth_date' => '2000-09-14',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'SHADYA JUMOKE',
                'email' => null,
                'phones' => ['07033388932'],
                'department' => 'ESM',
                'gender' => 'F',
                'birth_date' => '2000-03-28',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'DARE ADEGBITE',
                'email' => null,
                'phones' => ['08034095273'],
                'department' => 'ARC',
                'gender' => 'M',
                'birth_date' => '2000-01-22',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'AWE OLUWAYOMI',
                'email' => null,
                'phones' => ['08056179816'],
                'department' => 'CHE',
                'gender' => 'M',
                'birth_date' => '2000-03-17',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'OSHO BANKOLE',
                'email' => null,
                'phones' => ['08035799932'],
                'department' => 'QSV',
                'gender' => 'M',
                'birth_date' => '2000-04-04',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'ELEHINLE FOLASHADE',
                'email' => null,
                'phones' => ['08030741427'],
                'department' => 'BIO',
                'gender' => 'F',
                'birth_date' => '2000-07-13',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'OJO TIMILEHIN',
                'email' => null,
                'phones' => ['08034397019'],
                'department' => 'CVE',
                'gender' => 'M',
                'birth_date' => '2000-09-02',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'AJAYI OLALEKAN',
                'email' => null,
                'phones' => ['08033970544'],
                'department' => 'IDD',
                'gender' => 'M',
                'birth_date' => '2000-10-16',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'ADELUGBA OLORUNTOBA',
                'email' => null,
                'phones' => ['08039774539'],
                'department' => 'IDD',
                'gender' => 'M',
                'birth_date' => '2000-10-19',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'AFELUYI OLUWAYEMISI',
                'email' => null,
                'phones' => ['07030670659'],
                'department' => 'CHE',
                'gender' => 'F',
                'birth_date' => '2000-10-22',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'OMOLE BOLUWATIFE',
                'email' => null,
                'phones' => ['08038048353'],
                'department' => 'MET',
                'gender' => 'M',
                'birth_date' => '2000-11-27',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'DAUDU ORINAMI',
                'email' => null,
                'phones' => ['08032234727'],
                'department' => 'MEE',
                'gender' => 'M',
                'birth_date' => '2000-08-08',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'PELEMO BUKOLA',
                'email' => null,
                'phones' => ['08038459639'],
                'department' => 'BCH',
                'gender' => 'F',
                'birth_date' => '2000-01-07',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'ADEYEMO BIMBO',
                'email' => null,
                'phones' => ['08036552522'],
                'department' => 'FST',
                'gender' => 'M',
                'birth_date' => '2000-09-29',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'ADEGOKE OLOLADE',
                'email' => null,
                'phones' => ['08068976814'],
                'department' => 'CSC',
                'gender' => 'M',
                'birth_date' => '2000-03-21',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'ADESKO MAYOWA',
                'email' => null,
                'phones' => ['08034831502'],
                'department' => 'MME',
                'gender' => 'M',
                'birth_date' => '2000-01-06',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'ADENIYI ADEOLA',
                'email' => null,
                'phones' => ['08062228024'],
                'department' => 'FST',
                'gender' => 'F',
                'birth_date' => '2000-05-09',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'AFOLABI JANET',
                'email' => null,
                'phones' => ['08060352988'],
                'department' => 'PHY',
                'gender' => 'F',
                'birth_date' => '2000-06-20',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'AKINYEMI BUNMI',
                'email' => null,
                'phones' => ['08037840477'],
                'department' => 'CSC',
                'gender' => 'F',
                'birth_date' => '2000-07-22',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'DILE OLUWASEYI J.',
                'email' => null,
                'phones' => ['08038528643'],
                'department' => 'AGY',
                'gender' => 'M',
                'birth_date' => '2000-09-19',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'FAGBEMIGUN FEMI',
                'email' => null,
                'phones' => ['08039415726'],
                'department' => 'CHE',
                'gender' => 'M',
                'birth_date' => '2000-03-11',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'FATUNBI TOPE',
                'email' => null,
                'phones' => ['08038207756'],
                'department' => 'AEE',
                'gender' => 'M',
                'birth_date' => '2000-05-23',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'FAWIBE OLAYIWOLA',
                'email' => null,
                'phones' => ['08038567604'],
                'department' => 'AGY',
                'gender' => 'M',
                'birth_date' => '2000-11-02',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'IBUKUN OLUWASEGUN',
                'email' => null,
                'phones' => ['08027191851'],
                'department' => 'PHY',
                'gender' => 'M',
                'birth_date' => '2000-03-02',
                'state' => null,
                'address' => null,
            ],
            [
                'name' => 'ONI OLUWATOSIN',
                'email' => null,
                'phones' => ['08060345979'],
                'department' => 'CSP',
                'gender' => 'F',
                'birth_date' => '2000-01-11',
                'state' => null,
                'address' => null,
            ],
        ];

        foreach ($alumni as $data) {
            if (empty($data['name'])) {
                continue;
            }

            Alumnus::updateOrCreate(
                ['name' => $data['name'], 'tenure_id' => $tenure->id],
                array_merge($data, ['tenure_id' => $tenure->id])
            );
        }

        $this->command->info("Seeded {$tenure->year} alumni: ".count($alumni).' records');
    }
}

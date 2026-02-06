<?php

namespace Database\Seeders;

use App\Models\Alumnus;
use App\Models\Tenure;
use Illuminate\Database\Seeder;

class AlumniLocationSeeder extends Seeder
{
    /**
     * Seed Alumni location data (state and address).
     * Data source: ALUMNI.md - Alumni by location
     */
    public function run(): void
    {
        $alumni = [
            [
                'name' => 'Oyekola Oladapo M',
                'state' => 'Kwara',
                'address' => 'Corper-Ilorin Omuran',
                'phones' => ['07032026773'],
                'year' => '2013-2014',
            ],
            [
                'name' => 'Owobumuyi Tolulope',
                'state' => 'Kwara',
                'address' => '126, Ajala Street, Off Western Reservoir, Ilorin.',
                'phones' => ['08038089626'],
                'year' => '2009-2010',
            ],
            [
                'name' => 'Bada Oluwafemi',
                'state' => 'Kogi',
                'address' => '25, Makama Street, Off Kewon road, Lokoja, Kogi State.',
                'phones' => ['08053222386'],
                'year' => null,
            ],
            [
                'name' => 'Kayode Oluwaseyi',
                'state' => 'Kogi',
                'address' => 'Office 2, Ferma office, Behind Unity Bank, Lokoja, Kogi State',
                'phones' => ['07065719553'],
                'year' => null,
            ],
            [
                'name' => 'Ogunyomi Idowu',
                'state' => 'Kogi',
                'address' => '26, Arkola Lane, Kajola, Kaaba, Kogi State',
                'phones' => ['08062107242'],
                'year' => null,
            ],
            [
                'name' => 'Balogun Olabisi',
                'state' => 'Osun',
                'address' => 'No 44,omotto line 2, ile-ife.',
                'phones' => ['08030781684'],
                'year' => null,
            ],
            [
                'name' => 'Pastor Akindoyi',
                'state' => 'Osun',
                'address' => 'Oke oloyibo opp NTA ile-ife(no 18\\) Saturday',
                'phones' => ['08056149750'],
                'year' => null,
            ],
            [
                'name' => 'Apantaku Temitope',
                'state' => 'Osun',
                'address' => '142 oke bale,oshogbo local govt.',
                'phones' => ['08162391172'],
                'year' => null,
            ],
            [
                'name' => 'Oladipo Oluremi',
                'state' => 'Osun',
                'address' => 'Ogo-oluwa,osjogbo,beside heritage hotel',
                'phones' => ['08068881045'],
                'year' => null,
            ],
            [
                'name' => 'Ajetomobi Jacob. O.',
                'state' => 'Osun',
                'address' => 'Greater tomorrow international academy,osuwo,osun state.',
                'phones' => ['08033662091'],
                'year' => null,
            ],
            [
                'name' => 'Afe Bola .A.',
                'state' => 'Osun',
                'address' => 'No. 64 Hezekiah Oluwasanmi Road 7,ile-ife.',
                'phones' => ['07037837501'],
                'year' => null,
            ],
            [
                'name' => 'AWOYEMI Olanrewaju Olaniyi',
                'state' => 'Osun',
                'address' => 'Road 2,house 11, Oreofe Quarters, Ile ife, Osun state',
                'phones' => ['08151177997'],
                'year' => '2017-2018',
            ],
            [
                'name' => 'Adetayo OLUSANYA',
                'state' => 'Osun',
                'address' => 'Oluwaloni Villa, Ago-Iwoye',
                'phones' => ['07061935632'],
                'year' => '2009-2010',
            ],
            [
                'name' => 'Pastor Fakojede Soji',
                'state' => 'Rivers',
                'address' => 'II, Ihunwe Chinwo Street, Nuigwe, Woji, Portharcourt.',
                'phones' => ['08038256683'],
                'year' => null,
            ],
            [
                'name' => 'Pastor Francis',
                'state' => 'Rivers',
                'address' => 'Portharcourt',
                'phones' => ['08038853849'],
                'year' => null,
            ],
            [
                'name' => 'Daramola Stephen O.',
                'state' => 'Rivers',
                'address' => 'Npigwu-Igwe Estate, Woji, Port-harcourt',
                'phones' => ['07033340879'],
                'year' => null,
            ],
            [
                'name' => 'Adeleye Bernard',
                'state' => 'Rivers',
                'address' => 'New Heaven Avenue, Rumuowhule Ęneka, Port-Harcourt',
                'phones' => ['08132364486'],
                'year' => null,
            ],
            [
                'name' => 'OloruntoyeAdeseku',
                'state' => 'Rivers',
                'address' => '2B, Close 12, Peace Drive,Treasure Estate, Power Encounter, Ruomuodara, Port-Harcourt',
                'phones' => null,
                'year' => null,
            ],
            [
                'name' => 'Ojo Mojisola',
                'state' => 'Ekiti',
                'address' => 'Along 132 KV, Oke-Osun, Ado-Ekiti.',
                'phones' => ['08034899177'],
                'year' => '2008-2009',
            ],
            [
                'name' => 'Adeniyi Akinwale',
                'state' => 'Ekiti',
                'address' => '19, Oke-Oniye, Adekunle Market, Along Ajisosun, Ado-Ekiti.',
                'phones' => ['08036365919'],
                'year' => '2011-2012',
            ],
            [
                'name' => 'Omole Olumuyiwa',
                'state' => 'Ekiti',
                'address' => 'No 205, Moferere Estate, Ado-Ekiti.',
                'phones' => ['07031545239'],
                'year' => '2009-2010',
            ],
            [
                'name' => 'Ayodele Ayodeyi T.',
                'state' => 'Ekiti',
                'address' => 'Agbado Road, Behind General Hospital, Ise-Ekiti',
                'phones' => ['08038128263'],
                'year' => '2009-2010',
            ],
            [
                'name' => 'Adelugba Omolara',
                'state' => 'Ekiti',
                'address' => 'No 22, Ilore Street, Ootunja, Ikole-Ekiti.',
                'phones' => ['08060714709'],
                'year' => '2009-2010',
            ],
            [
                'name' => 'DurojaiyeTitilope',
                'state' => 'Ekiti',
                'address' => 'W2/70, Ilore Quarters, Ootunja Ekiti, Ikole-Ekiti.',
                'phones' => ['07033385160', '08076000613'],
                'year' => '2009-2010',
            ],
            [
                'name' => 'Ajayi Olugbenga',
                'state' => 'Ekiti',
                'address' => 'AB 13, Bolorunduro Street, Ikole-Ekiti.',
                'phones' => ['07033089935'],
                'year' => null,
            ],
            [
                'name' => 'Ayeni Oluwatosin',
                'state' => 'Ekiti',
                'address' => 'Beside Onward, Ikere-Ekiti.',
                'phones' => ['08066306319'],
                'year' => '2011-2012',
            ],
            [
                'name' => 'Shittu Akibu',
                'state' => 'Ekiti',
                'address' => '32 Adebayo Afeez Street, Ajilosun, Ado-Ekiti.',
                'phones' => ['08069635218'],
                'year' => null,
            ],
            [
                'name' => 'Oloruntemi Micheal',
                'state' => 'Ekiti',
                'address' => 'Along Igbole Road, Opp RCCG Jesus House Province 3, Ikole-Ekiti.',
                'phones' => ['08035291762'],
                'year' => null,
            ],
            [
                'name' => 'Olaiya Sunday',
                'state' => 'Ekiti',
                'address' => 'Ekiti',
                'phones' => ['07062737144'],
                'year' => '2015-2016',
            ],
            [
                'name' => 'Olaiya Tomilola',
                'state' => 'Ekiti',
                'address' => 'Ekiti',
                'phones' => ['07033661876'],
                'year' => '2015-2016',
            ],
            [
                'name' => 'Esther Agboalu',
                'state' => 'Ekiti',
                'address' => 'No 3 isalu street odo oro. Ikole Ekiti',
                'phones' => ['07057614560'],
                'year' => '2013-2014',
            ],
            [
                'name' => 'Joseph Ayodeji',
                'state' => 'Ekiti',
                'address' => 'Ado ekiti',
                'phones' => ['08130563068'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Remiluyi Adesuyi',
                'state' => 'FCT',
                'address' => 'The world overcomers ministries, Navy junior rode quarters, Phase 4, Kubwa, Abuja',
                'phones' => ['08024199759'],
                'year' => '2003-2004',
            ],
            [
                'name' => 'Arigbede Timileyin E.',
                'state' => 'FCT',
                'address' => 'Zemba Settlement street, Abuja',
                'phones' => ['08132930847'],
                'year' => '2014-2015',
            ],
            [
                'name' => 'Shonuga Busola',
                'state' => 'FCT',
                'address' => '2nd gate, Post housing army estate, Phase 2, Karahala junction, Kurudu, Abuja',
                'phones' => ['07068347197'],
                'year' => '2013-2014',
            ],
            [
                'name' => 'Obioenwe Amechi',
                'state' => 'FCT',
                'address' => 'W38A Federal BHousing, Kubwa, Abuja',
                'phones' => ['08038226951'],
                'year' => '2009-2010',
            ],
            [
                'name' => 'Julius Dare',
                'state' => 'FCT',
                'address' => '7, Bamaco Street, Wuse zone 1, Abuja',
                'phones' => ['08060972906'],
                'year' => '2012-2013',
            ],
            [
                'name' => 'Adebowale Muyiwa',
                'state' => 'FCT',
                'address' => 'Tundis Nigeria Limited, Suite 47, God’s Own Plaza beside Lumonde Hotel Area 2, Garki, Abuja',
                'phones' => ['07030480818'],
                'year' => '2012-2013',
            ],
            [
                'name' => 'Omotayo Bolaji',
                'state' => 'FCT',
                'address' => 'Apo Primary School, Wari, Abuja',
                'phones' => ['08135357337'],
                'year' => null,
            ],
            [
                'name' => 'Aribanusi Funke',
                'state' => 'FCT',
                'address' => 'Abuja',
                'phones' => ['07039436167'],
                'year' => null,
            ],
            [
                'name' => 'Awoyemi Samuel',
                'state' => 'FCT',
                'address' => 'Gwagwalada, Abuja',
                'phones' => ['08066379609'],
                'year' => '2011-2012',
            ],
            [
                'name' => 'Odewale Temitope',
                'state' => 'FCT',
                'address' => 'Kubwa, Arab road, Abuja',
                'phones' => ['08034168335'],
                'year' => null,
            ],
            [
                'name' => 'Olayanju Benson',
                'state' => 'FCT',
                'address' => '13, celdlink off AdemolaAdetokunbo, Wuse2, Abuja',
                'phones' => ['08066906651'],
                'year' => null,
            ],
            [
                'name' => 'Lamide Johnson',
                'state' => 'FCT',
                'address' => 'Abuja',
                'phones' => ['07033634106'],
                'year' => null,
            ],
            [
                'name' => 'Adewuni yemisi',
                'state' => 'FCT',
                'address' => 'Abuja',
                'phones' => ['08162878230'],
                'year' => '2015-2016',
            ],
            [
                'name' => 'Olasunkanmi Israel Ayoade',
                'state' => 'FCT',
                'address' => 'Zone 4 Alo close dutse bukpma Abuja.',
                'phones' => ['07061008645'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Majolagbe Olufemi',
                'state' => 'FCT',
                'address' => 'Lokogoma Abuja',
                'phones' => ['08065000583'],
                'year' => '2015-2016',
            ],
            [
                'name' => 'Iyanuloluwa Olowe',
                'state' => 'FCT',
                'address' => 'Lugbe, Abuja',
                'phones' => ['07069298798'],
                'year' => '2017-2018',
            ],
            [
                'name' => 'Blessing Johnson Thoto',
                'state' => 'FCT',
                'address' => 'Abuja',
                'phones' => ['\\+2348032534625'],
                'year' => '2013-2014',
            ],
            [
                'name' => 'Olufemi Adeola Orekoya',
                'state' => 'FCT',
                'address' => 'No74 Ibadan Street, Jedo Investment Estate, Sabon Lugbe Abuja',
                'phones' => ['08120111343'],
                'year' => '2001-2002',
            ],
            [
                'name' => 'Ademiluyi Ibidapo Omoniyi',
                'state' => 'FCT',
                'address' => 'Fmard, Wuse Zone 5, Abuja',
                'phones' => ['07039066338'],
                'year' => '2001-2002',
            ],
            [
                'name' => 'DurojaiyeTitilope',
                'state' => 'Ogun',
                'address' => '1, Adeyemi Sherifat Street, Opp Emirate Castle, along American Junction road, Alogi , Abeokuta.',
                'phones' => null,
                'year' => null,
            ],
            [
                'name' => 'Alo Ayopo B.',
                'state' => 'Ogun',
                'address' => 'Nigeria Building and Road Research,Km 10 ,Idi-Iroko Road, Ota , Ogun State.',
                'phones' => ['07030077119'],
                'year' => '2011-2012',
            ],
            [
                'name' => 'Olagunju Benson',
                'state' => 'Ogun',
                'address' => 'Mowe, Ogun State.',
                'phones' => ['08066906651'],
                'year' => '2009-2010',
            ],
            [
                'name' => 'Aborisade Lawrence',
                'state' => 'Ogun',
                'address' => '16/17 Adogeh Street, Off Unity Bus-stop, Alagbado, Ogun State',
                'phones' => ['08168275435'],
                'year' => '2012-2013',
            ],
            [
                'name' => 'Pst Oluleye James (President)',
                'state' => 'Ogun',
                'address' => 'Federal Housing Estates, Abeokuta',
                'phones' => ['08037722574'],
                'year' => null,
            ],
            [
                'name' => 'Adepoju Moradeke',
                'state' => 'Ogun',
                'address' => 'Magborodo , Ogun State',
                'phones' => ['08034902707'],
                'year' => null,
            ],
            [
                'name' => 'Adekunle Temitope S.',
                'state' => 'Ogun',
                'address' => 'Idi-Iroko Road, via NNPC 9th Estate , Ota , Ogun State.',
                'phones' => ['08138268032'],
                'year' => '2012-2013',
            ],
            [
                'name' => 'Oguntimehin Tope',
                'state' => 'Ogun',
                'address' => 'From Sango-Ota take a bus going to Papa, then drop at Abule Oko and ask for St Campbell Schools.',
                'phones' => ['08032432733'],
                'year' => '2008-2009',
            ],
            [
                'name' => 'Awotale Seun',
                'state' => 'Ogun',
                'address' => '5, Omonimi Street, Quarry , Abeokuta',
                'phones' => ['08035289288'],
                'year' => '2008-2009',
            ],
            [
                'name' => 'Ogunleye Damilola',
                'state' => 'Ogun',
                'address' => '5, Ilupeju Street, Unity Estate, Aiserejoju Bus-stop , Sango Ota(Work: Drugfield Pharmaceutical Limited, Sango Ota',
                'phones' => ['08062929972'],
                'year' => '2008-2009',
            ],
            [
                'name' => 'Fapetu Abimbola',
                'state' => 'Ogun',
                'address' => 'Iyana Iyesi Bus-Stop, Otta, Ogun State, 2 Bus-stop away from Covenant University.',
                'phones' => ['07036844188'],
                'year' => null,
            ],
            [
                'name' => 'Omoniyi Temitope',
                'state' => 'Ogun',
                'address' => 'Federal University  Of Agriculture, Abeokuta',
                'phones' => ['07033958234'],
                'year' => null,
            ],
            [
                'name' => 'Akinsuyi Olayemi Irenitemi',
                'state' => 'Ogun',
                'address' => '2, Ike-oluwa Block Industry, Off Ebute Road, Ibafo, Ogun State',
                'phones' => ['08132962361'],
                'year' => '2017-2018',
            ],
            [
                'name' => 'Onigbinde Gbenga Daniel',
                'state' => 'Ogun',
                'address' => '18, Adeyinka unity street, Akute. Ogun State',
                'phones' => ['08064626803'],
                'year' => '2015-2016',
            ],
            [
                'name' => 'Olajide Ademola',
                'state' => 'Ogun',
                'address' => '41, Peace Avenue off The Belss Junction Sango otta Ogun state',
                'phones' => ['08106174438'],
                'year' => '2017-2018',
            ],
            [
                'name' => 'Afe Temidayo Ebenezer',
                'state' => 'Ogun',
                'address' => 'No 2, Kappor ade str, Olosun, ota, Ogun state .',
                'phones' => ['07032244084'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Oluwole Hammond',
                'state' => 'Ogun',
                'address' => '27, Poju Adeyemi Street, Ire-akari Estate, Ayetoro, Ogun State.',
                'phones' => ['08031186934'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Osunkalu Funmilayo',
                'state' => 'Ogun',
                'address' => 'Redemption Camp',
                'phones' => ['08034610877'],
                'year' => null,
            ],
            [
                'name' => 'Pastor Osunkalu Ololade',
                'state' => 'Ogun',
                'address' => 'Redemption Camp',
                'phones' => ['08060171374'],
                'year' => null,
            ],
            [
                'name' => 'Adeoti Adebayo',
                'state' => 'Ogun',
                'address' => 'Redemption Camp',
                'phones' => ['08028194150'],
                'year' => null,
            ],
            [
                'name' => 'Adeyeye Ayodeji Bayo',
                'state' => 'Ogun',
                'address' => '19B, Ogunsoye street, Oremeji bus-stop, Ojodu rd, Lagos state',
                'phones' => ['08030512701'],
                'year' => null,
            ],
            [
                'name' => 'Pastor Julius Olalekan',
                'state' => 'Ogun',
                'address' => 'Redemption Camp',
                'phones' => ['08033935662'],
                'year' => null,
            ],
            [
                'name' => 'Fatayo Imolaya T.',
                'state' => 'Ogun',
                'address' => '21, Budband street, grammer school bus-stop, Ojodu-berger',
                'phones' => ['08060244281'],
                'year' => null,
            ],
            [
                'name' => 'Dada Adeyinka',
                'state' => 'Ogun',
                'address' => '9, Alhaji Olorunyonusi street, Alagbole Akute, Ojodu, Lagos',
                'phones' => ['08066555144', '07057770084'],
                'year' => null,
            ],
            [
                'name' => 'Ajibola Temidayo',
                'state' => 'Ogun',
                'address' => '15, Egbin street, Ojodu-berger, Lagos',
                'phones' => ['07033320543', '08082567043'],
                'year' => null,
            ],
            [
                'name' => 'Segun Akinloye',
                'state' => 'Ogun',
                'address' => 'Olowotede bus-stop after deeper life junction',
                'phones' => ['09098690620'],
                'year' => null,
            ],
            [
                'name' => 'Oluwaseun Babalola',
                'state' => 'Ogun',
                'address' => 'RCCG Camp  Projects, Redemption Camp',
                'phones' => ['08028199150'],
                'year' => null,
            ],
            [
                'name' => 'Ayeni Temidayo TeeDee',
                'state' => 'Ogun',
                'address' => 'Redemption Camp',
                'phones' => ['08030422426'],
                'year' => null,
            ],
            [
                'name' => 'Akinsuyi Olayemi Irenitemi',
                'state' => 'Ogun',
                'address' => '2, Ikeoluwa Block Industry, Off Ebute Road, Ibafo.',
                'phones' => ['08132962361'],
                'year' => null,
            ],
            [
                'name' => 'Akintaro Emmanuel A',
                'state' => 'Ogun',
                'address' => 'PPDC Redemption Camp',
                'phones' => ['08032377309'],
                'year' => null,
            ],
            [
                'name' => 'Adeyina Omowunmi',
                'state' => 'Oyo',
                'address' => 'UI Ibadan',
                'phones' => ['07034641907'],
                'year' => null,
            ],
            [
                'name' => 'Ogungbemi Paul',
                'state' => 'Oyo',
                'address' => 'ibadan',
                'phones' => ['08168338204'],
                'year' => null,
            ],
            [
                'name' => 'Sneubu Joshua',
                'state' => 'Oyo',
                'address' => 'ibadan',
                'phones' => ['0815857910'],
                'year' => null,
            ],
            [
                'name' => 'Ladipo Oluseun',
                'state' => 'Oyo',
                'address' => 'ibadan',
                'phones' => ['08132363295'],
                'year' => null,
            ],
            [
                'name' => 'Quadri Reuben',
                'state' => 'Oyo',
                'address' => 'ibadan',
                'phones' => ['08134595724'],
                'year' => null,
            ],
            [
                'name' => 'Fatayo Imolaya T.',
                'state' => 'Oyo',
                'address' => '21, Budband street, grammer school bus-stop, Ojodu-berger',
                'phones' => ['08060244281'],
                'year' => null,
            ],
            [
                'name' => 'Olapeju F',
                'state' => 'Oyo',
                'address' => 'No1, ariyidi street bodija ibadan',
                'phones' => ['07034206892'],
                'year' => null,
            ],
            [
                'name' => 'Azike Chenyene F',
                'state' => 'Oyo',
                'address' => 'Ibadan',
                'phones' => ['0813671891'],
                'year' => null,
            ],
            [
                'name' => 'Ota olagunjun oluwatominsin',
                'state' => 'Oyo',
                'address' => 'Ibadan',
                'phones' => ['07062590884'],
                'year' => null,
            ],
            [
                'name' => 'Akinloye Peter Oluwaseyi',
                'state' => 'Oyo',
                'address' => 'House 2, Beside Fatgbems Filling Station, Sanyo, Ibadan',
                'phones' => ['08138662883'],
                'year' => '2014-2015',
            ],
            [
                'name' => 'Ayodeji Oyasina',
                'state' => 'Oyo',
                'address' => '16 Hon Folaranmi Street, Oluode Estate, Sharp Corner, Oluyole Extension, Ibadan',
                'phones' => ['08062434137'],
                'year' => '2013-2014',
            ],
            [
                'name' => 'Omojopa Victor Boluwatife',
                'state' => 'Oyo',
                'address' => 'Eleyele Ibadan',
                'phones' => ['08099391761'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Taylor oluwaseyi',
                'state' => 'Oyo',
                'address' => 'Flat 35, Anigilaje street, odo ona elewe. Ibadan',
                'phones' => ['09039651925'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Adedapo Joseph Adeleye',
                'state' => 'Oyo',
                'address' => '7 Oke Ibadan Estate Akobo Ibadan',
                'phones' => ['07062328929'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Adedoyin Amoo-Onidundu',
                'state' => 'Oyo',
                'address' => 'Ibadan',
                'phones' => ['08099566339'],
                'year' => '2009-2010',
            ],
            [
                'name' => 'Oladele Amoo-Onidundu',
                'state' => 'Oyo',
                'address' => 'Ibadan',
                'phones' => ['08028227909'],
                'year' => '2004-2005',
            ],
            [
                'name' => 'Oyetoran Wole',
                'state' => 'Lagos',
                'address' => '10A Fabac Close, Victoria Island, Lagos',
                'phones' => ['07034081273'],
                'year' => '1991-1992',
            ],
            [
                'name' => 'Oladejo Mercy Toluwanimi.',
                'state' => 'Lagos',
                'address' => 'No 40 Abdulrasheed street, Ipaja,Lagos',
                'phones' => ['08168007656'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Aroboto Temiloluwa',
                'state' => 'Lagos',
                'address' => '18 Atinuke Olabanji street off unity road Ikeja Lagos',
                'phones' => ['08132586404'],
                'year' => '2017-2018',
            ],
            [
                'name' => 'Kayode ogunnowo',
                'state' => 'Lagos',
                'address' => '13, adesina balogun I gbogbo ikorodu lagos',
                'phones' => ['08083840470'],
                'year' => '2014-2015',
            ],
            [
                'name' => 'Adedoyin Adebisi James',
                'state' => 'Lagos',
                'address' => '20 Unity Drive, Odukoya Estate, Akowonjo Lagos',
                'phones' => ['\\+2348099448796'],
                'year' => null,
            ],
            [
                'name' => 'YUSUF Benjamin',
                'state' => 'Lagos',
                'address' => 'G.p 754, olobayo estate, Lokoja',
                'phones' => ['08139331357'],
                'year' => '2017-2018',
            ],
            [
                'name' => 'Ogunmola Oluwafemi Babatunde',
                'state' => 'Lagos',
                'address' => '32 , Ibrahim iyiola street community road ijegun-Ikotun ,Lagos',
                'phones' => ['08086596523'],
                'year' => '2013-2014',
            ],
            [
                'name' => 'Faith Adetoye',
                'state' => 'Lagos',
                'address' => 'LAGOS',
                'phones' => ['08104884510'],
                'year' => '2017-2018',
            ],
            [
                'name' => 'Jaiyeoba Olorunfemi',
                'state' => 'Lagos',
                'address' => '24, Adedosu street, Ogba-Agege, Lagos.',
                'phones' => ['07066322927'],
                'year' => '2015-2016',
            ],
            [
                'name' => 'Adeyinka Okeowo Fagbemigun',
                'state' => 'Lagos',
                'address' => 'Nigerian Navy Base Beecroft Apapa Lagos',
                'phones' => ['07035072299'],
                'year' => '2014-2015',
            ],
            [
                'name' => 'Fapetu Abimbola',
                'state' => 'Lagos',
                'address' => 'Iyana Iyesi Bus-Stop, Otta, Ogun State, 2 Bus-stop away from Covenant University.',
                'phones' => ['07036844188'],
                'year' => null,
            ],
            [
                'name' => 'Omoniyi Temitope',
                'state' => 'Lagos',
                'address' => 'Federal University  Of Agriculture, Abeokuta',
                'phones' => ['07033958234'],
                'year' => null,
            ],
            [
                'name' => 'Adesola Dada',
                'state' => 'Lagos',
                'address' => '55, Alimosho Road, Iyana Ipaja Lagos',
                'phones' => ['08179673324'],
                'year' => '2014-2015',
            ],
            [
                'name' => 'Ayeni Temidayo TeeDee',
                'state' => 'Lagos',
                'address' => 'Lagos',
                'phones' => ['08030422426'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Ogundimu omobolanle bukola',
                'state' => 'Lagos',
                'address' => 'Lagos',
                'phones' => ['08035380785'],
                'year' => '1997-1998',
            ],
            [
                'name' => 'Kayode sheriff Adebayo',
                'state' => 'Lagos',
                'address' => '1, asefon street ayobo-ipaja, Lagos',
                'phones' => ['07065479782'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Oluwarinu Bunmi Adamu',
                'state' => 'Lagos',
                'address' => 'Alauasa Ikeja',
                'phones' => ['07086730412'],
                'year' => '2015-2016',
            ],
            [
                'name' => 'Ajibade Adeoye',
                'state' => 'Lagos',
                'address' => '1, sonaike street, Ketu, Lagos',
                'phones' => ['07083236924'],
                'year' => '2013-2014',
            ],
            [
                'name' => 'Oyeyemi Sefunmi Kemisola',
                'state' => 'Lagos',
                'address' => '22, kings avenue, along bemil ojodu-berger, Lagos State',
                'phones' => ['08166135613'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Sunday',
                'state' => 'Lagos',
                'address' => 'Ogba, Ikeja, Lagos state',
                'phones' => ['07069644691'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Olatunji Olafasakin',
                'state' => 'Lagos',
                'address' => '6 Aguja Close, off Adebayo Olukanni Street, Ojodu, Lagos',
                'phones' => ['08026067235'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Wilson Tosin IGE',
                'state' => 'Lagos',
                'address' => '22, Olaleye street, Powerline, Iju-Ishaga, Lagos State, Nigeria.',
                'phones' => ['\\+2348123200753'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Segun Ajileye',
                'state' => 'Lagos',
                'address' => 'Lagos state',
                'phones' => ['08034113375'],
                'year' => '2005-2006',
            ],
            [
                'name' => 'Masebinu Godwin',
                'state' => 'Lagos',
                'address' => '9, babalola ige street, Ojo Lagos.',
                'phones' => ['08169770810'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Lanre Famakin',
                'state' => 'Lagos',
                'address' => 'Lagos',
                'phones' => ['08068265443'],
                'year' => '2009-2010',
            ],
            [
                'name' => 'Lawrence Samuel',
                'state' => 'Lagos',
                'address' => '3, Olayinka Olowu Street, Ikorodu, Lagos.',
                'phones' => ['08025679024'],
                'year' => '2015-2016',
            ],
            [
                'name' => 'Adewunmi Adesanwo',
                'state' => 'Lagos',
                'address' => '1, Prince Fashiku Street, Federal Housing Estate, Ago 40, Aboru, Iyana Ipaja, Lagos.',
                'phones' => ['08030704506'],
                'year' => '2005-2006',
            ],
            [
                'name' => 'EJIGA SHADRACH AGADA',
                'state' => 'Delta',
                'address' => 'Erimak integrated farm limited, Ogharra, delta state.',
                'phones' => ['08166680878'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Samuel O. Adesuyi',
                'state' => 'Delta',
                'address' => '27, Vanguard Avenue, opposite Asaba Airport',
                'phones' => ['08033738797'],
                'year' => '2009-2010',
            ],
            [
                'name' => 'Adesuyi Rebecca Oluwakemi',
                'state' => 'Delta',
                'address' => 'Flat C20 NUT Estate, midwifery junction, okpanam road Asaba, Delta state, Nigeria.',
                'phones' => ['07031378567'],
                'year' => '2010-2011',
            ],
            [
                'name' => 'Agbidi Emmanuel Ifeanyi',
                'state' => 'Delta',
                'address' => 'Ndemili, Ndokwa West, Delta state',
                'phones' => ['07036314250'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Samuel Agbokpo Alabi',
                'state' => 'Adamawa',
                'address' => 'American University Of Nigeria, Centre For Ict Innovation & Training, 226 Moddibbo Adama Way, Yola Adamawa State',
                'phones' => ['08065821763'],
                'year' => '2011-2012',
            ],
            [
                'name' => 'TAIWO, ANUOLUWAPO SAMUEL',
                'state' => 'Ondo',
                'address' => 'Department of Metallurgical and Materials Engineering, FUTA.',
                'phones' => ['07063283214'],
                'year' => '2013-2014',
            ],
            [
                'name' => 'Ademoluti Isaac',
                'state' => 'Ondo',
                'address' => 'No 5, Oni Eniola Road, Off Oda Road,Akure',
                'phones' => ['08165295689'],
                'year' => '2014-2015',
            ],
            [
                'name' => 'Dada Boluwatife',
                'state' => 'Ondo',
                'address' => 'FUTA',
                'phones' => ['\\+2348038048353'],
                'year' => '2007-2008',
            ],
            [
                'name' => 'Akindoyo Felix Akinola',
                'state' => 'Ondo',
                'address' => '8 kayode street lafe Akure',
                'phones' => ['08130561055'],
                'year' => '2011-2012',
            ],
            [
                'name' => 'Shittu Oluwaseun Andrew',
                'state' => 'Ondo',
                'address' => '7, Ofuya Close, FUTA Road, Akure, Ondo State',
                'phones' => ['08104281382'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Olaleye Israel',
                'state' => 'Ondo',
                'address' => 'Akure',
                'phones' => ['08162072200'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Abidakun Ifedayo Ayomide',
                'state' => 'Ondo',
                'address' => '28 Oshinle quarters Akure Ondo state',
                'phones' => ['09051240140'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Gideon Opeyemi',
                'state' => 'Ondo',
                'address' => 'Apatapiti, Futa South gate, Akure.',
                'phones' => ['08167347972'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Omotola Ibukunoluwa Racheal',
                'state' => 'Plateau',
                'address' => 'Kufang, Miango Junction, Jos, Plateau state',
                'phones' => ['07036330052'],
                'year' => '2016-2017',
            ],
            [
                'name' => 'Benneth Joel Chibuikem',
                'state' => 'Ondo',
                'address' => 'Rd 5b, Jerusalem Street, Yaba, Ondo, Ondo State.',
                'phones' => ['08130623743'],
                'year' => '2015-2016',
            ],
            [
                'name' => 'Ekwueme Judith Adaku',
                'state' => 'Edo',
                'address' => '2 osagiede street off upper Adesuwa road, GRA Benin city. Edo state',
                'phones' => ['08138719453'],
                'year' => '2015-2016',
            ],
        ];

        $updated = 0;
        $created = 0;

        foreach ($alumni as $data) {
            if (empty($data['name'])) {
                continue;
            }

            // Extract year to find tenure
            $year = $data['year'] ?? null;
            unset($data['year']);

            // Try to find existing alumnus by email or name
            $alumnus = null;
            if (! empty($data['email'])) {
                $alumnus = Alumnus::where('email', $data['email'])->first();
            }
            if (! $alumnus) {
                $alumnus = Alumnus::where('name', $data['name'])->first();
            }

            if ($alumnus) {
                // Update state and address
                $updateData = ['state' => $data['state']];
                if ($data['address']) {
                    $updateData['address'] = $data['address'];
                }
                if ($data['phones']) {
                    $existingPhones = $alumnus->phones ?? [];
                    $newPhones = array_unique(array_merge($existingPhones, $data['phones']));
                    if (count($newPhones) > count($existingPhones)) {
                        $updateData['phones'] = $newPhones;
                    }
                }
                $alumnus->update($updateData);
                $updated++;
            } else {
                // Create new record - tenure is optional
                if ($year) {
                    $tenure = Tenure::where('year', $year)->first();
                    if ($tenure) {
                        $data['tenure_id'] = $tenure->id;
                    }
                }
                unset($data['year']);
                Alumnus::create($data);
                $created++;
            }
        }

        $this->command->info("Alumni Location Seeder: Updated {$updated}, Created {$created} records");
    }
}

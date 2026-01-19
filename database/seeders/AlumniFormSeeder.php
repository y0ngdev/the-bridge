<?php

namespace Database\Seeders;

use App\Models\Alumnus;
use App\Models\Tenure;
use Illuminate\Database\Seeder;

class AlumniFormSeeder extends Seeder
{
    /**
     * Seed Alumni from alumni_de3.pdf (Google Forms responses).
     */
    public function run(): void
    {
        $alumni = [
            [
                'name' => 'Oyetoran Wole',
                'email' => 'oyetoran@yahoo.com',
                'phones' => ['07034081273'],
                'gender' => 'F',
                'address' => '10A Fabac Close, Victoria Island, Lagos',
                'department' => 'Applied Geophysics',
                'unit' => null,
                'year' => '1991-1992',
            ],
            [
                'name' => 'EJIGA SHADRACH AGADA',
                'email' => 'ejigashadrach@gmail.com',
                'phones' => ['08166680878'],
                'gender' => 'F',
                'address' => 'Erimak integrated farm limited, Ogharra, delta state.',
                'department' => 'Agricultural extension and communication technology',
                'unit' => 'Drama Unit',
                'year' => '2016-2017',
            ],
            [
                'name' => 'ADEYEYE SAMUEL ADEDEJI',
                'email' => 'samueladeyeye2012@gmail.com',
                'phones' => ['+2347030563936'],
                'gender' => 'F',
                'address' => 'NCCF secretariat, Area V New Owerri, Imo state.',
                'department' => 'Physics',
                'unit' => 'Drama Unit',
                'year' => '2016-2017',
            ],
            [
                'name' => 'Oladejo Mercy Toluwanimi',
                'email' => 'mercyoladejo@ymail.com',
                'phones' => ['08168007656'],
                'gender' => 'F',
                'address' => 'No 40 Abdulrasheed street, Ipaja,Lagos',
                'department' => 'Surveying and geoinformatics',
                'unit' => 'Welfare Unit',
                'year' => '2016-2017',
            ],
            [
                'name' => 'Adewuni yemisi',
                'email' => 'yemilizzy@gmail.com',
                'phones' => ['8162878230'],
                'gender' => 'F',
                'address' => null,
                'department' => 'Microbiology',
                'unit' => 'Ushering Unit',
                'year' => '2015-2016',
            ],
            [
                'name' => 'Aroboto Temiloluwa',
                'email' => 'arobototemi@gmail.como',
                'phones' => ['08132586404'],
                'gender' => 'F',
                'address' => '18 Atinuke Olabanji street off unity road Ikeja Lagos',
                'department' => 'Agricultural and Environmental Engineering',
                'unit' => 'Media and Ambience Unit',
                'year' => '2017-2018',
            ],
            [
                'name' => 'Olaiya Sunday Temitope',
                'email' => 'olaiyasunday71@gmail.com',
                'phones' => ['07062737144'],
                'gender' => 'F',
                'address' => 'ROOM 90, PG HOSTEL, FUTA AKURE',
                'department' => 'MME',
                'unit' => 'Prayer Unit',
                'year' => '2015-2016',
            ],
            [
                'name' => 'Kayode ogunnowo',
                'email' => 'olukayodeogunnowo@gmail.com',
                'phones' => ['08083840470'],
                'gender' => 'F',
                'address' => '13, adesina balogun I gbogbo ikorodu lagos',
                'department' => 'Electrical and electronics engineering',
                'unit' => null,
                'year' => '2014-2015',
            ],
            [
                'name' => 'Adedoyin Adebisi James',
                'email' => 'jamesdoyin@gmail.com',
                'phones' => ['+2348099448796'],
                'gender' => 'F',
                'address' => '20 Unity Drive, Odukoya Estate, Akowonjo Lagos',
                'department' => 'Industrial Design',
                'unit' => 'Drama Unit',
                'year' => '2009-2010',
            ],
            [
                'name' => 'TAIWO, ANUOLUWAPO SAMUEL',
                'email' => 'samueltaiwoanu@gmail.com',
                'phones' => ['07063283214'],
                'gender' => 'F',
                'address' => 'Department of Metallurgical and Materials Engineering, FUTA.',
                'department' => 'Metallurgical and Materials Engineering (MME)',
                'unit' => 'Academic Unit',
                'year' => '2013-2014',
            ],
            [
                'name' => 'Ayobami Edun',
                'email' => 'Edun2014@gmail.com',
                'phones' => ['3525192389'],
                'gender' => 'F',
                'address' => '3, Apomu new site via ifo. Lagos Abeokuta expressway',
                'department' => 'Electrical and Computer Engineering',
                'unit' => 'Prayer Unit',
                'year' => '2013-2014',
            ],
            [
                'name' => 'YUSUF Benjamin',
                'email' => 'yusufbenjaminayuba@gmail.com',
                'phones' => ['08139331357'],
                'gender' => 'F',
                'address' => 'G.p 754, olobayo estate, Lokoja',
                'department' => 'Civil Engineering',
                'unit' => 'Media and Ambience Unit',
                'year' => '2017-2018',
            ],
            [
                'name' => 'Olasunkanmi Israel Ayoade',
                'email' => 'sammyayo.7@gmail.com',
                'phones' => ['07061008645'],
                'gender' => 'F',
                'address' => 'Zone 4 Alo close dutse bukpma Abuja.',
                'department' => 'Applied Geophysics',
                'unit' => 'Sanctuary Keeping Unit',
                'year' => '2016-2017',
            ],
            [
                'name' => 'Samuel Agbokpo Alabi',
                'email' => 'Agbokpoalabi@gmail.com',
                'phones' => ['08065821763'],
                'gender' => 'F',
                'address' => 'American University Of Nigeria, Centre For Ict Innovation & Training, 226 Moddibbo',
                'department' => 'Applied Geology',
                'unit' => 'Prayer Unit',
                'year' => '2011-2012',
            ],
            [
                'name' => 'Ogunmola Oluwafemi Babatunde',
                'email' => 'obogunmola097513@gmail.com',
                'phones' => ['08086596523'],
                'gender' => 'F',
                'address' => '32 , Ibrahim iyiola street community road ijegun-Ikotun ,Lagos',
                'department' => 'Estate Management',
                'unit' => 'Follow up/Counselling Unit',
                'year' => '2013-2014',
            ],
            [
                'name' => 'AWOYEMI Olanrewaju Olaniyi',
                'email' => 'awoyemi.olamrewaju94@gmail.com',
                'phones' => ['08151177997'],
                'gender' => 'F',
                'address' => 'Road 2,house 11, Oreofe Quarters,  Ile ife, Osun state',
                'department' => 'Mechanical Engineering',
                'unit' => 'Editorial Unit',
                'year' => '2017-2018',
            ],
            [
                'name' => 'Faith Adetoye',
                'email' => 'faithadetoye@yahoo.com',
                'phones' => ['08104884510'],
                'gender' => 'F',
                'address' => '7, Gbenro Ogunbiyi Street, Osuntokun Avenue, Old Bodija, Ibadan',
                'department' => 'Architecture',
                'unit' => 'Alumni Relations Unit',
                'year' => '2017-2018',
            ],
            [
                'name' => 'Jaiyeoba Olorunfemi',
                'email' => 'jaiyeobaolorunfemi@gmail.com',
                'phones' => ['07066322927'],
                'gender' => 'F',
                'address' => '24, Adedosu street, Ogba-Agege, Lagos.',
                'department' => 'Civil Engineering',
                'unit' => 'Sanctuary Keeping Unit',
                'year' => '2015-2016',
            ],
            [
                'name' => 'AKINSUYI OLAYEMI IRENITEMI',
                'email' => 'olayemiakinsuyi@gmail.com',
                'phones' => ['08132962361'],
                'gender' => 'F',
                'address' => '2, Ike-oluwa Block Industry, Off Ebute Road, Ibafo, Ogun State',
                'department' => 'Project Management Technology',
                'unit' => 'Choir Unit',
                'year' => '2017-2018',
            ],
            [
                'name' => 'Adeyinka Okeowo Fagbemigun',
                'email' => 'yinkafabs@gmail.com',
                'phones' => ['07035072299'],
                'gender' => 'F',
                'address' => 'Nigerian Navy Base Beecroft Apapa Lagos',
                'department' => 'Mechanical Engineering',
                'unit' => 'Academic Unit',
                'year' => '2014-2015',
            ],
            [
                'name' => 'Adetayo OLUSANYA',
                'email' => 'adetayur@gmail.com',
                'phones' => ['07061935632'],
                'gender' => 'F',
                'address' => 'Oluwaloni Villa, Ago-Iwoye',
                'department' => 'Biology',
                'unit' => 'Editorial Unit',
                'year' => '2009-2010',
            ],
            [
                'name' => 'Adesola Dada',
                'email' => 'adesoladada8@gmail.com',
                'phones' => ['08179673324'],
                'gender' => 'F',
                'address' => '55, Alimosho Road, Iyana Ipaja Lagos',
                'department' => 'Mechanical Engineering',
                'unit' => 'Ushering Unit',
                'year' => '2014-2015',
            ],
            [
                'name' => 'Ademoluti Isaac',
                'email' => 'isaacademoluti@gmail.com',
                'phones' => ['08165295689'],
                'gender' => 'F',
                'address' => 'No 5, Oni Eniola Road, Off Oda Road,Akure',
                'department' => 'Applied Geophysics',
                'unit' => 'Evangelism Unit',
                'year' => '2014-2015',
            ],
            [
                'name' => 'Akintaro Emmanuel A',
                'email' => 'akintaroakinyemi@gmail.com',
                'phones' => ['08032377309'],
                'gender' => 'F',
                'address' => 'PPDC Redemption Camp',
                'department' => 'Architecture',
                'unit' => 'Media and Ambience Unit',
                'year' => '2010-2011',
            ],
            [
                'name' => 'Adewusi Adebayo',
                'email' => 'tonycrownwusi@gmail.com',
                'phones' => ['08169641858'],
                'gender' => 'F',
                'address' => 'Road 11, Harmony Estate, Aboko',
                'department' => 'A.E.E',
                'unit' => 'Drama Unit',
                'year' => '2014-2015',
            ],
            [
                'name' => 'Onigbinde Gbenga Daniel',
                'email' => 'onigbindeg@gmail.com',
                'phones' => ['8064626803'],
                'gender' => 'F',
                'address' => '18, Adeyinka unity street, Akute. Ogun State',
                'department' => 'Agricultural Engineering',
                'unit' => null,
                'year' => '2015-2016',
            ],
            [
                'name' => 'MAJOLAGBE OLUFEMI',
                'email' => 'simplyobalola@gmail.com',
                'phones' => ['08065000583'],
                'gender' => 'F',
                'address' => null,
                'department' => 'Applied Geology',
                'unit' => null,
                'year' => '2015-2016',
            ],
            [
                'name' => 'Adeyinka Dada Adeyeye',
                'email' => 'adeyinkadadaadeyeye@gmail.com',
                'phones' => ['08066555144'],
                'gender' => 'F',
                'address' => 'Confidence Estate, Karina Pakuro',
                'department' => 'Biochemistry',
                'unit' => null,
                'year' => '2009-2010',
            ],
            [
                'name' => 'Oluwaseun Awolope',
                'email' => 'oluwaseunawolope@gmail.com',
                'phones' => ['7035729644'],
                'gender' => 'F',
                'address' => 'Ojulari close, laketu ikorodu,',
                'department' => 'Surveying and geo-informatics',
                'unit' => 'Follow up/Counselling Unit',
                'year' => '2014-2015',
            ],
            [
                'name' => 'Iyanuloluwa Olowe',
                'email' => 'hiyarnuloluwa@gmail.com',
                'phones' => ['07069298798'],
                'gender' => 'F',
                'address' => null,
                'department' => 'Applied Geology',
                'unit' => null,
                'year' => '2017-2018',
            ],
            [
                'name' => 'Samuel Makinde',
                'email' => 'samakkybest@gmail.com',
                'phones' => ['+17788147390'],
                'gender' => 'M',
                'address' => 'Lower Mall, Marine Drive, Vancouver, BC, Canada',
                'department' => 'Agricultural and Environmental Engineering',
                'unit' => null,
                'year' => '2017-2018',
                'is_overseas' => true,
            ],
            [
                'name' => 'Dada Boluwatife',
                'email' => 'boludada77@gmail.com',
                'phones' => ['+2348038048353'],
                'gender' => 'F',
                'address' => null,
                'department' => 'Meteorology',
                'unit' => 'Choir Unit',
                'year' => '2007-2008',
            ],
            [
                'name' => 'Ayeni Temidayo TeeDee',
                'email' => 'temidayopaul.ayeni@gmail.com',
                'phones' => ['08030422426'],
                'gender' => 'F',
                'address' => 'InfoTech officer',
                'department' => 'Computer Science',
                'unit' => 'Choir Unit',
                'year' => '2016-2017',
            ],
            [
                'name' => 'Akinloye Peter Oluwaseyi',
                'email' => 'chosenp2010@gmail.com',
                'phones' => ['08138662883'],
                'gender' => 'F',
                'address' => 'House 2, Beside Fatgbems Filling Station, Sanyo, Ibadan',
                'department' => 'Biology',
                'unit' => null,
                'year' => '2014-2015',
            ],
            [
                'name' => 'Ogundimu omobolanle bukola',
                'email' => 'akiodeb@yahoo.com',
                'phones' => ['08035380785'],
                'gender' => 'F',
                'address' => 'Working for people',
                'department' => 'Electrical Electronic Engineering',
                'unit' => 'Ushering Unit',
                'year' => '1997-1998',
            ],
            [
                'name' => 'Akindoyo Felix Akinola',
                'email' => 'akindoyofelixa@gmail.com',
                'phones' => ['08130561055'],
                'gender' => 'F',
                'address' => '8 kayode street lafe Akure',
                'department' => 'Animal Production and Health',
                'unit' => 'Ushering Unit',
                'year' => '2011-2012',
            ],
            [
                'name' => 'Kayode sheriff Adebayo',
                'email' => 'kayodesheriffadebayo7@gmail.com',
                'phones' => ['07065479782'],
                'gender' => 'F',
                'address' => '1, asefon street ayobo-ipaja, Lagos',
                'department' => 'Agricultural and Environmental Engineering',
                'unit' => 'Organising Unit',
                'year' => '2016-2017',
            ],
            [
                'name' => 'Oluwarinu Bunmi Adamu',
                'email' => 'adamuoluwarinu99@gmail.com',
                'phones' => ['7086730412'],
                'gender' => 'F',
                'address' => 'Soil Technologist',
                'department' => 'Crop,Soil and Pest Management',
                'unit' => null,
                'year' => '2015-2016',
            ],
            [
                'name' => 'Ayodeji Oyasina',
                'email' => 'dejikaka22@gmail.com',
                'phones' => ['08062434137'],
                'gender' => 'F',
                'address' => '16 Hon Folaranmi Street, Oluode Estate, Sharp Corner, Oluyole Extension, Ibadan',
                'department' => 'Estate Management',
                'unit' => 'Ushering Unit',
                'year' => '2013-2014',
            ],
            [
                'name' => 'Shittu Oluwaseun Andrew',
                'email' => 'holuwarsheunjenny77@gmail.com',
                'phones' => ['08104281382'],
                'gender' => 'F',
                'address' => '7, Ofuya Close, FUTA Road, Akure, Ondo State',
                'department' => 'Crop, Soil and Pest Management (CSP)',
                'unit' => 'Editorial Unit',
                'year' => '2016-2017',
            ],
            [
                'name' => 'Ajibade Adeoye',
                'email' => 'adeoyejibade@yahoo.com',
                'phones' => ['07083236924'],
                'gender' => 'F',
                'address' => '1, sonaike street, Ketu, Lagos',
                'department' => 'Estate Management',
                'unit' => 'Media and Ambience Unit',
                'year' => '2013-2014',
            ],
            [
                'name' => 'Esther Agboalu',
                'email' => 'xterdynamic@gmail.com',
                'phones' => ['07057614560'],
                'gender' => 'F',
                'address' => 'No 3 isalu street odo oro. Ikole Ekiti',
                'department' => 'Chemistry',
                'unit' => null,
                'year' => '2013-2014',
            ],
            [
                'name' => 'Olaleye Israel',
                'email' => 'Olaleyeisrael99@gmail.com',
                'phones' => ['08162072200'],
                'gender' => 'F',
                'address' => null,
                'department' => 'Quantity Surveying',
                'unit' => 'Evangelism Unit',
                'year' => '2016-2017',
            ],
            [
                'name' => 'Oyeyemi Sefunmi Kemisola',
                'email' => 'sefunmioyeyemi@gmail.com',
                'phones' => ['08166135613'],
                'gender' => 'F',
                'address' => '22, kings avenue, along bemil ojodu-berger, Lagos State',
                'department' => 'Agricultural Extension and Communication Technology (AEC)',
                'unit' => null,
                'year' => '2016-2017',
            ],
            [
                'name' => 'Abidakun Ifedayo Ayomide',
                'email' => 'ifedayoabidakun@gmail.com',
                'phones' => ['09051240140'],
                'gender' => 'F',
                'address' => '28 Oshinle quarters Akure Ondo state',
                'department' => 'Ecotourism and wildlife management',
                'unit' => null,
                'year' => '2016-2017',
            ],
            [
                'name' => 'Sunday',
                'email' => 'sunnexoduo@yahoo.com',
                'phones' => ['07069644691'],
                'gender' => 'F',
                'address' => 'Ogba, Ikeja, Lagos state',
                'department' => 'Estate management',
                'unit' => null,
                'year' => '2016-2017',
            ],
            [
                'name' => 'Olatunji Olafasakin',
                'email' => 'olafasakinolatunji@gmail.com',
                'phones' => ['08026067235'],
                'gender' => 'F',
                'address' => '6 Aguja Close, off Adebayo Olukanni Street, Ojodu, Lagos',
                'department' => 'Industrial Deisgn',
                'unit' => 'Media and Ambience Unit',
                'year' => '2016-2017',
            ],
            [
                'name' => 'Omotola Ibukunoluwa Racheal',
                'email' => 'ibkomotola4real@gmail.com',
                'phones' => ['07036330052'],
                'gender' => 'F',
                'address' => 'Kufang, Miango Junction, Jos, Plateau state',
                'department' => 'Computer science',
                'unit' => null,
                'year' => '2016-2017',
            ],
            [
                'name' => 'Omojopa Victor Boluwatife',
                'email' => 'vikiflex@gmail.com',
                'phones' => ['8099391761'],
                'gender' => 'F',
                'address' => null,
                'department' => 'Quantity Surveying',
                'unit' => 'Choir Unit',
                'year' => '2016-2017',
            ],
            [
                'name' => 'Wilson Tosin IGE',
                'email' => 'ige.wilson.tosin@gmail.com',
                'phones' => ['+2348123200753'],
                'gender' => 'F',
                'address' => '22, Olaleye street,  Powerline, Iju-Ishaga, Lagos State, Nigeria.',
                'department' => 'PHYSICS',
                'unit' => 'Academic Unit',
                'year' => '2016-2017',
            ],
            [
                'name' => 'Taylor oluwaseyi g',
                'email' => 'taylorichydroxixe@gmail.com',
                'phones' => ['09039651925'],
                'gender' => 'F',
                'address' => 'Flat 35, Anigilaje street, odo ona elewe. Ibadan',
                'department' => 'Aph',
                'unit' => 'Prayer Unit',
                'year' => '2016-2017',
            ],
            [
                'name' => 'Olajide Ademola',
                'email' => 'jidemola15@gmail.com',
                'phones' => ['08106174438'],
                'gender' => 'F',
                'address' => '41, Peace Avenue off The Belss Junction Sango otta Ogun state',
                'department' => 'Mechanical Engineering',
                'unit' => 'Choir Unit',
                'year' => '2017-2018',
            ],
            [
                'name' => 'Joseph Ayodeji',
                'email' => 'jossyphem89@gmail.com',
                'phones' => ['08130563068'],
                'gender' => 'F',
                'address' => null,
                'department' => 'Architecture',
                'unit' => 'Editorial Unit',
                'year' => '2016-2017',
            ],
            [
                'name' => 'Afe Temidayo Ebenezer',
                'email' => 'afetemidayodeji@yahoo.com',
                'phones' => ['07032244084'],
                'gender' => 'F',
                'address' => 'No 2, Kappor ade str, Olosun, ota, Ogun state .',
                'department' => 'Agricultural and Resource Economics',
                'unit' => null,
                'year' => '2016-2017',
            ],
            [
                'name' => 'Segun Ajileye',
                'email' => 'pisonstructuralservices@gmail.com',
                'phones' => ['08034113375'],
                'gender' => 'F',
                'address' => null,
                'department' => 'MME',
                'unit' => 'Evangelism Unit',
                'year' => '2005-2006',
            ],
            [
                'name' => 'Masebinu Godwin',
                'email' => 'godwinmasebinu@gmail.com',
                'phones' => ['08169770810'],
                'gender' => 'F',
                'address' => '9, babalola ige street, Ojo Lagos.',
                'department' => 'Transport Management Technology',
                'unit' => 'Evangelism Unit',
                'year' => '2016-2017',
            ],
            [
                'name' => 'Samuel O. Adesuyi',
                'email' => 'samadesuyi@gmail.com',
                'phones' => ['08033738797'],
                'gender' => 'F',
                'address' => '27, Vanguard Avenue, opposite Asaba Airport',
                'department' => 'Quantity Surveying',
                'unit' => null,
                'year' => '2009-2010',
            ],
            [
                'name' => 'Adedapo Joseph Adeleye',
                'email' => 'josephadedapo@gmail.com',
                'phones' => ['7062328929'],
                'gender' => 'F',
                'address' => '7 Oke Ibadan Estate Akobo Ibadan',
                'department' => 'Industrial Chemistry',
                'unit' => 'Prayer Unit',
                'year' => '2016-2017',
            ],
            [
                'name' => 'Lanre Famakin',
                'email' => 'fsundaylanre@gmail.com',
                'phones' => ['08068265443'],
                'gender' => 'F',
                'address' => null,
                'department' => 'Civil engineering',
                'unit' => 'Editorial Unit',
                'year' => '2009-2010',
            ],
            [
                'name' => 'Adesuyi Rebecca Oluwakemi',
                'email' => 'rebadesuyi@gmail.com',
                'phones' => ['07031378567'],
                'gender' => 'F',
                'address' => 'Flat C20 NUT Estate, midwifery junction, okpanam road Asaba, Delta state, Nigeria.',
                'department' => 'Architecture',
                'unit' => 'Prayer Unit',
                'year' => '2010-2011',
            ],
            [
                'name' => 'Samuel Adesuyi',
                'email' => 'samadesuyi@gmail.com',
                'phones' => ['08033738797'],
                'gender' => 'F',
                'address' => '27, Vanguard Avenue, opposite Asaba Airport',
                'department' => 'Quantity Surveying',
                'unit' => null,
                'year' => '2009-2010',
            ],
            [
                'name' => 'Oluwole Hammond',
                'email' => null,
                'phones' => ['08031186934'],
                'gender' => 'F',
                'address' => '27, Poju Adeyemi Street, Ire-akari Estate, Ayetoro, Ogun State.',
                'department' => 'Urban and Regional Planning',
                'unit' => 'Prayer Unit',
                'year' => '2016-2017',
            ],
            [
                'name' => 'Gideon Opeyemi',
                'email' => 'Opeyemigideon15@gmail.com',
                'phones' => ['08167347972'],
                'gender' => 'F',
                'address' => 'Apatapiti, Futa South gate, Akure.',
                'department' => 'Statistics',
                'unit' => 'Bible Study Unit',
                'year' => '2016-2017',
            ],
            [
                'name' => 'Benneth Joel Chibuikem',
                'email' => 'joelben2011@gmail.com',
                'phones' => ['08130623743'],
                'gender' => 'F',
                'address' => 'Rd 5b, Jerusalem Street, Yaba, Ondo, Ondo State',
                'department' => 'Marine Science and Technology',
                'unit' => 'Drama Unit',
                'year' => '2015-2016',
            ],
            [
                'name' => 'Ekwueme Judith Adaku',
                'email' => 'princessjudith123@gmail.com',
                'phones' => ['08138719453'],
                'gender' => 'F',
                'address' => '2 osagiede street off upper Adesuwa road, GRA Benin city. Edo state',
                'department' => 'Ecotourism and wildlife management',
                'unit' => 'Evangelism Unit',
                'year' => '2015-2016',
            ],
            [
                'name' => 'BLESSING JOHNSON THOTO',
                'email' => 'thoto50j@gmail.com',
                'phones' => ['+2348032534625'],
                'gender' => 'F',
                'address' => 'Mining Engineer, Entrepreneur & Pastor',
                'department' => 'Mining Engineering',
                'unit' => 'Prayer Unit',
                'year' => '2013-2014',
            ],
            [
                'name' => 'Agbidi Emmanuel Ifeanyi',
                'email' => 'agbidiemmanuel22@yahoo.com',
                'phones' => ['07036314250'],
                'gender' => 'F',
                'address' => 'Ndemili, Ndokwa West, Delta state',
                'department' => 'Meteorology',
                'unit' => 'Bible Study Unit',
                'year' => '2016-2017',
            ],
            [
                'name' => 'Aluko Tomilola',
                'email' => 'alukotomilola@gmail.com',
                'phones' => ['07033661876'],
                'gender' => 'F',
                'address' => null,
                'department' => 'Forestry and Wood Technology',
                'unit' => null,
                'year' => '2015-2016',
            ],
            [
                'name' => 'Alexander Tosin',
                'email' => 'alexandertosino@gmail.com',
                'phones' => ['08037748191'],
                'gender' => 'F',
                'address' => 'Plot 5 Block 4, oluwasuyi estate Akure',
                'department' => 'Agricultural Engineering',
                'unit' => null,
                'year' => '2012-2013',
            ],
            [
                'name' => 'Adedoyin Amoo-Onidundu',
                'email' => 'aeamoo5@gmail.com',
                'phones' => ['08099566339'],
                'gender' => 'F',
                'address' => null,
                'department' => 'Computer Science',
                'unit' => 'Choir Unit',
                'year' => '2009-2010',
            ],
            [
                'name' => 'Oladele Amoo-Onidundu',
                'email' => 'aremunath5@gmail.com',
                'phones' => ['08028227909'],
                'gender' => 'F',
                'address' => null,
                'department' => 'Forestry and Wood Technology',
                'unit' => 'Choir Unit',
                'year' => '2004-2005',
            ],
            [
                'name' => 'Lawrence Samuel',
                'email' => 'laurensam24@gmail.com',
                'phones' => ['08025679024'],
                'gender' => 'F',
                'address' => '3, Olayinka Olowu Street, Ikorodu, Lagos.',
                'department' => 'Geophysics',
                'unit' => null,
                'year' => '2015-2016',
            ],
            [
                'name' => 'OLUFEMI ADEOLA OREKOYA',
                'email' => 'femoreks@gmail.com',
                'phones' => ['08120111343'],
                'gender' => 'F',
                'address' => 'No74 Ibadan Street, Jedo Investment Estate, Sabon Lugbe Abuja',
                'department' => 'Agricultural Engineering',
                'unit' => null,
                'year' => '2001-2002',
            ],
            [
                'name' => 'ADEMILUYI  ibidapo omoniyi',
                'email' => 'dapomiluyi@gmail.com',
                'phones' => ['07039066338'],
                'gender' => 'F',
                'address' => 'FMARD, WUSE ZONE 5, ABUJA',
                'department' => 'Agricultural Engineering',
                'unit' => null,
                'year' => '2001-2002',
            ],
            [
                'name' => 'Adewunmi Adesanwo',
                'email' => 'adesanwoba@yahoo.com',
                'phones' => ['08030704506'],
                'gender' => 'F',
                'address' => '1, Prince Fashiku Street, Federal Housing Estate, Ago 40, Aboru, Iyana Ipaja,',
                'department' => 'Computer Science',
                'unit' => null,
                'year' => '2005-2006',
            ],
        ];

        $updated = 0;
        $created = 0;

        foreach ($alumni as $data) {
            if (empty($data['name'])) {
                continue;
            }

            $year = $data['year'] ?? null;
            unset($data['year']);

            // Lookup department_id from department name
            $departmentName = $data['department'] ?? null;
            unset($data['department']);
            if ($departmentName) {
                $department = \App\Models\Department::where('name', 'like', '%' . $departmentName . '%')
                    ->orWhere('name', 'like', $departmentName . '%')
                    ->first();
                if ($department) {
                    $data['department_id'] = $department->id;
                }
            }

            // Find existing alumnus by email or name
            $alumnus = null;
            if (!empty($data['email'])) {
                $alumnus = Alumnus::where('email', $data['email'])->first();
            }
            if (!$alumnus) {
                $alumnus = Alumnus::where('name', $data['name'])->first();
            }

            if ($alumnus) {
                // Update with new data
                $updateData = [];
                if (($data['email'] ?? null) && empty($alumnus->email)) {
                    $updateData['email'] = $data['email'];
                }
                if (($data['gender'] ?? null) && empty($alumnus->gender)) {
                    $updateData['gender'] = $data['gender'];
                }
                if (($data['address'] ?? null) && empty($alumnus->address)) {
                    $updateData['address'] = $data['address'];
                }
                if (($data['unit'] ?? null) && empty($alumnus->unit)) {
                    $updateData['unit'] = $data['unit'];
                }
                if ($data['phones'] ?? null) {
                    $existingPhones = $alumnus->phones ?? [];
                    $newPhones = array_unique(array_merge($existingPhones, $data['phones']));
                    if (count($newPhones) > count($existingPhones)) {
                        $updateData['phones'] = $newPhones;
                    }
                }
                if (($data['department_id'] ?? null) && empty($alumnus->department_id)) {
                    $updateData['department_id'] = $data['department_id'];
                }
                if ($data['is_overseas'] ?? false) {
                    $updateData['is_overseas'] = true;
                }
                if (!empty($updateData)) {
                    $alumnus->update($updateData);
                    $updated++;
                }
            } else {
                // Create new record
                if ($year) {
                    $tenure = Tenure::where('year', $year)->first();
                    if ($tenure) {
                        $data['tenure_id'] = $tenure->id;
                    }
                }
                Alumnus::create($data);
                $created++;
            }
        }

        $this->command->info("Alumni Form Seeder: Updated {$updated}, Created {$created} records");
    }
}

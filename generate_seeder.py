import json
import re

# Nigerian states for matching
states = ['Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno', 
          'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT', 'Gombe', 'Imo', 
          'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos', 'Nasarawa', 
          'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau', 'Rivers', 'Sokoto', 'Taraba', 
          'Yobe', 'Zamfara', 'Abuja']

def extract_state(address):
    if not address:
        return None
    addr_lower = address.lower()
    for state in states:
        if state.lower() in addr_lower:
            if state == 'Abuja':
                return 'FCT'
            return state
    return None

def escape_php(s):
    if s is None:
        return 'null'
    return "'" + s.replace("\\", "\\\\").replace("'", "\\'") + "'"

with open('alumni_data.json', 'r', encoding='utf-8') as f:
    data = json.load(f)

entries = []
for row in data['data']:
    name = row.get('NAMES', '').strip() if row.get('NAMES') else None
    if not name:
        continue
    
    dept = row.get('DEPARTMENT')
    phones = row.get('PHONE NO')
    gender = row.get('GENDER')
    email = row.get('MAIL')
    address = row.get('HOUSE ADDRESS')
    state = extract_state(address)
    
    # Format phones as JSON array for Laravel
    if phones:
        phone_list = [p.strip() for p in re.split(r'[,\s]+', phones) if p.strip()]
        phones_php = "['" + "', '".join(phone_list) + "']"
    else:
        phones_php = 'null'
    
    entry = f"""            [
                'name' => {escape_php(name)},
                'email' => {escape_php(email)},
                'phones' => {phones_php},
                'department' => {escape_php(dept)},
                'gender' => {escape_php(gender)},
                'state' => {escape_php(state)},
                'address' => {escape_php(address)},
            ],"""
    entries.append(entry)

# Generate the seeder file
seeder_content = '''<?php

namespace Database\\Seeders;

use App\\Models\\Alumni;
use App\\Models\\Tenure;
use Illuminate\\Database\\Seeder;

class AlumniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenure = Tenure::firstOrCreate(
            ['year' => '2024'],
            ['name' => '2024 Set']
        );

        $alumni = [
''' + '\n'.join(entries) + '''
        ];

        foreach ($alumni as $data) {
            Alumni::updateOrCreate(
                ['name' => $data['name'], 'email' => $data['email']],
                array_merge($data, ['tenure_id' => $tenure->id])
            );
        }
    }
}
'''

with open('database/seeders/AlumniSeeder.php', 'w', encoding='utf-8') as f:
    f.write(seeder_content)

print(f'Generated AlumniSeeder with {len(entries)} entries')

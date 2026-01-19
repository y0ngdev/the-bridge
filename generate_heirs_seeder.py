"""
Generate Heirs of Promise (2021-2022) seeder from CSV file.
"""
import csv
import re

def clean_phone(phone):
    """Clean phone number."""
    if not phone:
        return None
    phone = str(phone).strip()
    # Handle multiple phones
    phones = re.split(r'[,/]', phone)
    cleaned = []
    for p in phones:
        p = p.strip()
        if p.startswith('+'):
            p = '+' + re.sub(r'[^\d]', '', p[1:])
        else:
            p = re.sub(r'[^\d]', '', p)
        if len(p) >= 10:
            cleaned.append(p)
    return cleaned if cleaned else None

def escape_php(s):
    """Escape a string for PHP."""
    if s is None:
        return 'null'
    s = str(s).replace("\\", "\\\\").replace("'", "\\'")
    return f"'{s}'"

def format_phones(phones):
    """Format phone array for PHP."""
    if not phones:
        return 'null'
    return '[' + ', '.join([escape_php(p) for p in phones]) + ']'

def parse_dob(dob_str):
    """Parse date of birth from MM-DD format."""
    if not dob_str:
        return None
    dob_str = str(dob_str).strip()
    match = re.match(r'(\d{1,2})-(\d{1,2})', dob_str)
    if match:
        month, day = match.groups()
        # Use 2000 as default year since only month-day provided
        return f"2000-{int(month):02d}-{int(day):02d}"
    return None

def main():
    alumni = []
    
    with open('Heirs of Promise e-Database (1).csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            name = row.get('Full Name', '').strip()
            if not name:
                continue
                
            phone = row.get('Phone number', '')
            phones = clean_phone(phone)
            
            gender = row.get('Gender', '').strip()
            if gender == 'Male':
                gender = 'M'
            elif gender == 'Female':
                gender = 'F'
            else:
                gender = None
                
            address = row.get('Address', '').strip()
            if address and len(address) < 5:
                address = None
                
            department = row.get('Department', '').strip()
            if department and department.lower() == 'none':
                department = None
                
            dob = parse_dob(row.get('Date of Birth', ''))
            
            record = {
                'name': name,
                'phones': phones,
                'gender': gender,
                'address': address if address else None,
                'department': department if department else None,
                'birth_date': dob,
            }
            alumni.append(record)
    
    # Remove duplicates by name
    seen = set()
    unique_alumni = []
    for a in alumni:
        if a['name'] not in seen:
            seen.add(a['name'])
            unique_alumni.append(a)
    
    print(f"Parsed {len(unique_alumni)} unique alumni records")
    
    # Generate seeder
    alumni_entries = []
    for a in unique_alumni:
        entry = f"""            [
                'name' => {escape_php(a.get('name'))},
                'phones' => {format_phones(a.get('phones'))},
                'gender' => {escape_php(a.get('gender')) if a.get('gender') else 'null'},
                'address' => {escape_php(a.get('address')) if a.get('address') else 'null'},
                'department' => {escape_php(a.get('department')) if a.get('department') else 'null'},
                'birth_date' => {escape_php(a.get('birth_date')) if a.get('birth_date') else 'null'},
            ],"""
        alumni_entries.append(entry)
    
    alumni_array = '\n'.join(alumni_entries)
    
    seeder = f"""<?php

namespace Database\\Seeders;

use App\\Models\\Alumnus;
use App\\Models\\Tenure;
use App\\Models\\Department;
use Illuminate\\Database\\Seeder;

class HeirsOfPromiseSeeder extends Seeder
{{
    /**
     * Seed Heirs of Promise (2021-2022) alumni from CSV.
     * Source: Heirs of Promise e-Database (1).csv
     */
    public function run(): void
    {{
        // Get the 2021-2022 tenure
        $tenure = Tenure::where('year', '2021-2022')->first();
        
        $alumni = [
{alumni_array}
        ];

        $updated = 0;
        $created = 0;

        foreach ($alumni as $data) {{
            if (empty($data['name'])) {{
                continue;
            }}

            // Lookup department_id from department name
            $departmentName = $data['department'] ?? null;
            unset($data['department']);
            if ($departmentName) {{
                $department = Department::where('name', 'like', '%' . $departmentName . '%')
                    ->orWhere('name', 'like', $departmentName . '%')
                    ->first();
                if ($department) {{
                    $data['department_id'] = $department->id;
                }}
            }}

            // Find existing alumnus by name
            $alumnus = Alumnus::where('name', $data['name'])->first();
            
            if ($alumnus) {{
                // Update with new data
                $updateData = [];
                if (($data['gender'] ?? null) && empty($alumnus->gender)) {{
                    $updateData['gender'] = $data['gender'];
                }}
                if (($data['address'] ?? null) && empty($alumnus->address)) {{
                    $updateData['address'] = $data['address'];
                }}
                if (($data['phones'] ?? null) && empty($alumnus->phones)) {{
                    $updateData['phones'] = $data['phones'];
                }}
                if (($data['birth_date'] ?? null) && empty($alumnus->birth_date)) {{
                    $updateData['birth_date'] = $data['birth_date'];
                }}
                if (($data['department_id'] ?? null) && empty($alumnus->department_id)) {{
                    $updateData['department_id'] = $data['department_id'];
                }}
                if (!empty($updateData)) {{
                    $alumnus->update($updateData);
                    $updated++;
                }}
            }} else {{
                // Create new record with 2021-2022 tenure
                if ($tenure) {{
                    $data['tenure_id'] = $tenure->id;
                }}
                Alumnus::create($data);
                $created++;
            }}
        }}

        $this->command->info("Heirs of Promise Seeder: Updated {{$updated}}, Created {{$created}} records");
    }}
}}
"""

    with open('database/seeders/HeirsOfPromiseSeeder.php', 'w', encoding='utf-8') as f:
        f.write(seeder)
    
    print(f"Created HeirsOfPromiseSeeder.php")

if __name__ == '__main__':
    main()

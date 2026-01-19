"""
Parse alumni_de3.pdf (Google Forms export) and generate seeder.
"""
import re

def parse_year(year_str):
    """Parse year string like '2013/2014' to '2013-2014' format."""
    if not year_str or not year_str.strip():
        return None
    year_str = year_str.strip()
    match = re.match(r'(\d{4})[/-](\d{4})', year_str)
    if match:
        return f"{match.group(1)}-{match.group(2)}"
    return None

def parse_phone(phone_str):
    """Clean phone number."""
    if not phone_str or not phone_str.strip():
        return None
    phone = phone_str.strip()
    # Remove invalid/test phones
    if len(phone) < 10 or phone.startswith('123456'):
        return None
    return phone

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

# Unit normalization map
UNIT_MAP = {
    'ddf': 'Drama Unit',
    'dynamic drama family': 'Drama Unit',
    'drama': 'Drama Unit',
    'welfare unit': 'Welfare Unit',
    'welfare': 'Welfare Unit',
    'ushering': 'Ushering Unit',
    'publicity unit': 'Media and Ambience Unit',
    'publicity': 'Media and Ambience Unit',
    'prayer unit': 'Prayer Unit',
    'prayer': 'Prayer Unit',
    'sanctuary keeping unit': 'Sanctuary Keeping Unit',
    'sanctuary': 'Sanctuary Keeping Unit',
    'choir unit (tvm)': 'Choir Unit',
    'tvm': 'Choir Unit',
    'academic unit': 'Academic Unit',
    'academic': 'Academic Unit',
    'editorial / alumni unit': 'Editorial Unit',
    'editorial': 'Editorial Unit',
    'alumni relations unit': 'Alumni Relations Unit',
    'evangelism': 'Evangelism Unit',
    'follow up': 'Follow up/Counselling Unit',
    'president': None,  # Not a unit
}

def normalize_unit(unit_str):
    """Normalize unit name."""
    if not unit_str:
        return None
    unit_lower = unit_str.strip().lower()
    return UNIT_MAP.get(unit_lower, unit_str.strip().title() + ' Unit' if unit_lower else None)

def main():
    with open('pdf_content.txt', 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Split by page markers
    pages = content.split('---PAGE---')
    
    alumni = []
    current_record = {}
    
    for page_text in pages:
        lines = [l.strip() for l in page_text.split('\n') if l.strip()]
        
        # Skip template/header lines
        lines = [l for l in lines if not l.startswith('Alumnus Contact Info') 
                 and not l.startswith('Greetings to you')
                 and not l.startswith('proper relation')
                 and not l.startswith('Table of Alumni')
                 and 'Google' not in l
                 and l not in ['Name *', 'Gender *', 'Phone number *', 'Email', 'Address *', 
                               'Occupation', 'Year of Graduation *', 'Department In Futa *',
                               'Unit In The Fellowship.', 'Message For The Fellowship?', 'Forms']]
        
        if not lines:
            continue
        
        # Try to identify what kind of page this is
        # Pages with names typically have pattern: Name, Male/Female, Phone, Email, Address, Occupation
        # Pages with year/dept/unit typically have: Year, Department, Unit, Message
        
        has_gender = any(l in ['Male', 'Female'] for l in lines)
        has_year = any(re.match(r'\d{4}/\d{4}', l) for l in lines)
        
        if has_gender:
            # This is a page with personal info
            # Start new record
            if current_record and 'name' in current_record:
                alumni.append(current_record)
                current_record = {}
            
            # Parse fields
            idx = 0
            while idx < len(lines):
                line = lines[idx]
                
                # Look for name (usually first non-form line)
                if idx == 0 and line not in ['Male', 'Female', 'High', 'Student']:
                    current_record['name'] = line
                elif line in ['Male', 'Female']:
                    current_record['gender'] = 'M' if line == 'Male' else 'F'
                elif re.match(r'^[\+\d][\d\s\-\+]+$', line) and len(line) >= 10:
                    current_record['phone'] = parse_phone(line)
                elif '@' in line:
                    current_record['email'] = line
                elif idx > 3 and 'address' not in current_record and line not in ['High', 'Student', 'Farmer', 'Writer', 'Surveyor', 'Engineer', 'Architect', 'Geophysicist', 'Enterpreneur', 'Enterprenuer', 'Network engineer', 'Estate Surveyor', 'Civil Servant (Lecturer)', 'Grad Student', 'Health Assistant', 'Graphic Designer', 'Geologist/ IT instructor', 'Civil Engineer', 'Personal Assistant to the CEO', 'ENTREPRENEUR', 'Soldiering']:
                    # Could be address
                    if len(line) > 15:  # Addresses are usually longer
                        current_record['address'] = line
                
                idx += 1
                
        elif has_year:
            # This is a page with year/dept/unit info
            dept_found = False
            for i, line in enumerate(lines):
                year_match = re.match(r'(\d{4}/\d{4})', line)
                if year_match:
                    current_record['year'] = parse_year(year_match.group(1))
                elif 'unit' in line.lower() or line.lower() in UNIT_MAP:
                    current_record['unit'] = normalize_unit(line)
                elif not dept_found and not current_record.get('dept'):
                    # First non-year, non-unit, non-message line is likely department
                    # Messages typically start with: keep, god, bless, let, etc
                    if not any(x in line.lower() for x in ['keep', 'god', 'bless', 'fire', 'work', 'grace', 'love', 'let', 'never', 'your', 'continue', 'live', 'hold', 'aggressive', 'today', 'place']):
                        if len(line) > 2 and line.lower() not in UNIT_MAP:
                            current_record['dept'] = line
                            dept_found = True
    
    # Don't forget last record
    if current_record and 'name' in current_record:
        alumni.append(current_record)
    
    # Filter out invalid records (test data, missing names)
    valid_alumni = []
    for a in alumni:
        name = a.get('name', '')
        if name and len(name) > 2 and name not in ['H', 'Ayomide', 'Ff', 'G']:
            valid_alumni.append(a)
    
    print(f"Parsed {len(valid_alumni)} valid alumni records")
    
    # Generate seeder
    alumni_entries = []
    for a in valid_alumni:
        phones = [a['phone']] if a.get('phone') else None
        entry = f"""            [
                'name' => {escape_php(a.get('name'))},
                'email' => {escape_php(a.get('email')) if a.get('email') else 'null'},
                'phones' => {format_phones(phones)},
                'gender' => {escape_php(a.get('gender')) if a.get('gender') else 'null'},
                'address' => {escape_php(a.get('address')) if a.get('address') else 'null'},
                'department' => {escape_php(a.get('dept')) if a.get('dept') else 'null'},
                'unit' => {escape_php(a.get('unit')) if a.get('unit') else 'null'},
                'year' => {escape_php(a.get('year')) if a.get('year') else 'null'},
            ],"""
        alumni_entries.append(entry)
    
    alumni_array = '\n'.join(alumni_entries)
    
    seeder = f"""<?php

namespace Database\\Seeders;

use App\\Models\\Alumnus;
use App\\Models\\Tenure;
use Illuminate\\Database\\Seeder;

class AlumniFormSeeder extends Seeder
{{
    /**
     * Seed Alumni from alumni_de3.pdf (Google Forms responses).
     */
    public function run(): void
    {{
        $alumni = [
{alumni_array}
        ];

        $updated = 0;
        $created = 0;

        foreach ($alumni as $data) {{
            if (empty($data['name'])) {{
                continue;
            }}

            $year = $data['year'] ?? null;
            unset($data['year']);

            // Find existing alumnus by name
            $alumnus = Alumnus::where('name', $data['name'])->first();
            
            if ($alumnus) {{
                // Update with new data
                $updateData = [];
                if ($data['email'] && empty($alumnus->email)) {{
                    $updateData['email'] = $data['email'];
                }}
                if ($data['gender'] && empty($alumnus->gender)) {{
                    $updateData['gender'] = $data['gender'];
                }}
                if ($data['address'] && empty($alumnus->address)) {{
                    $updateData['address'] = $data['address'];
                }}
                if ($data['unit'] && empty($alumnus->unit)) {{
                    $updateData['unit'] = $data['unit'];
                }}
                if ($data['phones'] && empty($alumnus->phones)) {{
                    $updateData['phones'] = $data['phones'];
                }}
                if (!empty($updateData)) {{
                    $alumnus->update($updateData);
                    $updated++;
                }}
            }} else {{
                // Create new record
                if ($year) {{
                    $tenure = Tenure::where('year', $year)->first();
                    if ($tenure) {{
                        $data['tenure_id'] = $tenure->id;
                    }}
                }}
                Alumnus::create($data);
                $created++;
            }}
        }}

        $this->command->info("Alumni Form Seeder: Updated {{$updated}}, Created {{$created}} records");
    }}
}}
"""

    with open('database/seeders/AlumniFormSeeder.php', 'w', encoding='utf-8') as f:
        f.write(seeder)
    
    print(f"Created AlumniFormSeeder.php with {len(valid_alumni)} records")

if __name__ == '__main__':
    main()

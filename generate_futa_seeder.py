"""
Generate FUTA Staff and Akure Alumni seeder from Excel file.
"""
import openpyxl
import re
from datetime import datetime

def clean_phone(phone):
    """Clean phone number."""
    if phone is None:
        return None
    phone = str(phone).strip()
    if not phone or phone == 'None':
        return None
    # Remove non-digit chars except + at start
    if phone.startswith('+'):
        phone = '+' + re.sub(r'[^\d]', '', phone[1:])
    else:
        phone = re.sub(r'[^\d]', '', phone)
    if len(phone) < 10:
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
    cleaned = [p for p in phones if p]
    if not cleaned:
        return 'null'
    return '[' + ', '.join([escape_php(p) for p in cleaned]) + ']'

def parse_date(val):
    """Parse date value."""
    if val is None:
        return None
    if isinstance(val, datetime):
        return val.strftime('%Y-%m-%d')
    return None

def parse_year(val):
    """Parse year value."""
    if val is None:
        return None
    if isinstance(val, (int, float)):
        year = int(val)
        if 1980 <= year <= 2030:
            return f"{year}-{year+1}"
    return None

def main():
    wb = openpyxl.load_workbook('Futa and Akure Alumni 2.xlsx')
    sheet = wb['Sheet1']
    
    # Get all rows
    rows = list(sheet.iter_rows(values_only=True))
    
    # Find header row and data sections
    futa_staff = []
    akure_alumni = []
    current_section = None
    header_row = None
    
    for i, row in enumerate(rows):
        first_cell = str(row[0]).strip().upper() if row[0] else ''
        
        if 'FUTA STAFF' in first_cell:
            current_section = 'futa_staff'
            continue
        elif 'AKURE' in first_cell or 'ALUMNI' in first_cell and 'AKURE' in str(row).upper():
            current_section = 'akure_alumni'
            continue
        elif first_cell in ['NAME', 'NAMES', 'S/N']:
            # Header row
            header_row = row
            continue
        
        # Skip empty rows
        if not row[0]:
            continue
            
        # Parse data row
        # Columns seem to be: Name, Gender, Phone, Email, Address/Other, Year, Department, Date
        name = str(row[0]).strip() if row[0] else None
        if not name or name.upper() in ['NAME', 'NAMES', 'S/N', 'FUTA STAFF']:
            continue
            
        gender = str(row[1]).strip().upper() if row[1] else None
        if gender == 'M':
            gender = 'M'
        elif gender == 'F':
            gender = 'F'
        else:
            gender = None
            
        phone = clean_phone(row[2])
        email = str(row[3]).strip() if row[3] else None
        if email and '@' not in email:
            email = None
            
        # Other columns vary - could be address, employer, year, dept, date
        col4 = row[4] if len(row) > 4 else None
        col5 = row[5] if len(row) > 5 else None
        col6 = row[6] if len(row) > 6 else None
        col7 = row[7] if len(row) > 7 else None
        
        record = {
            'name': name,
            'gender': gender,
            'phone': phone,
            'email': email,
            'is_futa_staff': current_section == 'futa_staff',
            'state': 'Ondo',  # Akure is in Ondo state
        }
        
        # Try to parse additional columns
        for col in [col4, col5, col6, col7]:
            if col is None:
                continue
            col_str = str(col).strip().upper() if isinstance(col, str) else ''
            
            if isinstance(col, datetime):
                record['birth_date'] = col.strftime('%Y-%m-%d')
            elif isinstance(col, (int, float)) and 1980 <= col <= 2030:
                record['year'] = f"{int(col)}-{int(col)+1}"
            elif 'FUTA' in col_str or 'UNIVERSITY' in col_str or 'DEPARTMENT' in col_str:
                record['current_employer'] = str(col).strip()
            elif '@' in str(col):
                record['email'] = str(col).strip()
        
        if current_section == 'futa_staff':
            futa_staff.append(record)
        else:
            akure_alumni.append(record)
    
    all_records = futa_staff + akure_alumni
    print(f"Found {len(futa_staff)} FUTA staff, {len(akure_alumni)} Akure alumni")
    print(f"Total: {len(all_records)} records")
    
    # Generate seeder
    alumni_entries = []
    for a in all_records:
        phones = [a['phone']] if a.get('phone') else None
        entry = f"""            [
                'name' => {escape_php(a.get('name'))},
                'email' => {escape_php(a.get('email')) if a.get('email') else 'null'},
                'phones' => {format_phones(phones)},
                'gender' => {escape_php(a.get('gender')) if a.get('gender') else 'null'},
                'state' => {escape_php(a.get('state')) if a.get('state') else 'null'},
                'is_futa_staff' => {'true' if a.get('is_futa_staff') else 'false'},
                'current_employer' => {escape_php(a.get('current_employer')) if a.get('current_employer') else 'null'},
                'birth_date' => {escape_php(a.get('birth_date')) if a.get('birth_date') else 'null'},
                'year' => {escape_php(a.get('year')) if a.get('year') else 'null'},
            ],"""
        alumni_entries.append(entry)
    
    alumni_array = '\n'.join(alumni_entries)
    
    seeder = f"""<?php

namespace Database\\Seeders;

use App\\Models\\Alumnus;
use App\\Models\\Tenure;
use Illuminate\\Database\\Seeder;

class FutaStaffSeeder extends Seeder
{{
    /**
     * Seed FUTA Staff and Akure Alumni from Excel file.
     * Source: Futa and Akure Alumni 2.xlsx
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
                if (($data['email'] ?? null) && empty($alumnus->email)) {{
                    $updateData['email'] = $data['email'];
                }}
                if (($data['gender'] ?? null) && empty($alumnus->gender)) {{
                    $updateData['gender'] = $data['gender'];
                }}
                if (($data['state'] ?? null) && empty($alumnus->state)) {{
                    $updateData['state'] = $data['state'];
                }}
                if (($data['phones'] ?? null) && empty($alumnus->phones)) {{
                    $updateData['phones'] = $data['phones'];
                }}
                if ($data['is_futa_staff']) {{
                    $updateData['is_futa_staff'] = true;
                }}
                if (($data['current_employer'] ?? null) && empty($alumnus->current_employer)) {{
                    $updateData['current_employer'] = $data['current_employer'];
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

        $this->command->info("FUTA Staff Seeder: Updated {{$updated}}, Created {{$created}} records");
    }}
}}
"""

    with open('database/seeders/FutaStaffSeeder.php', 'w', encoding='utf-8') as f:
        f.write(seeder)
    
    print(f"Created FutaStaffSeeder.php")

if __name__ == '__main__':
    main()

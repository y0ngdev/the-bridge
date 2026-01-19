"""
Generate Alumni Location Seeder from markdown file.
Updates existing alumni with state and address information.
"""
import re

# Nigerian state mapping
STATE_MAP = {
    'KWARA': 'Kwara',
    'KOGI STATE': 'Kogi',
    'OSUN STATE': 'Osun',
    'PORTHARCOURT': 'Rivers',
    'EKITI STATE': 'Ekiti',
    'ABUJA': 'FCT',
    'OGUN STATE': 'Ogun',
    'ALUMNI IN REDEMPTION CAMP': 'Ogun',  # Redemption Camp is in Ogun
    'OYO STATE': 'Oyo',
    'LAGOS STATE': 'Lagos',
    'DELTA STATE': 'Delta',
    'ADAMAWA STATE': 'Adamawa',
    'FUTA AND AKURE ENVIRONS': 'Ondo',
    'JOS, PLATEAU STATE': 'Plateau',
    'ONDO STATE': 'Ondo',
    'EDO STATE': 'Edo',
}

def parse_year(year_str):
    """Parse year string like '13/14' or '2013/2014' to '2013-2014' format."""
    if not year_str or not year_str.strip():
        return None
    year_str = year_str.strip()
    # Match patterns like 13/14, 09/10, 2013/2014
    match = re.match(r'(\d{2,4})[/-](\d{2,4})', year_str)
    if match:
        y1, y2 = match.groups()
        if len(y1) == 2:
            y1 = '20' + y1 if int(y1) < 50 else '19' + y1
        if len(y2) == 2:
            y2 = '20' + y2 if int(y2) < 50 else '19' + y2
        return f"{y1}-{y2}"
    return None

def parse_phones(phone_str):
    """Parse phone numbers from string."""
    if not phone_str or not phone_str.strip():
        return None
    phones = re.split(r'[\s,/]+', phone_str.strip())
    cleaned = []
    for p in phones:
        p = p.strip()
        if p and len(p) >= 10:
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

def parse_table_row(row):
    """Parse a markdown table row."""
    # Remove leading/trailing pipes and split
    row = row.strip()
    if row.startswith('|'):
        row = row[1:]
    if row.endswith('|'):
        row = row[:-1]
    cells = [c.strip() for c in row.split('|')]
    return cells

def main():
    with open('ALUMNI.md', 'r', encoding='utf-8') as f:
        content = f.read()
    
    entries = []
    current_state = None
    lines = content.split('\n')
    
    for i, line in enumerate(lines):
        line = line.strip()
        
        # Check for state headers
        if line.startswith('**') and line.endswith('**'):
            state_name = line.strip('*').strip()
            if state_name in STATE_MAP:
                current_state = STATE_MAP[state_name]
                continue
        
        # Skip header rows and separator rows
        if not line or line.startswith('| S/N') or line.startswith('| SN') or line.startswith('| :'):
            continue
        
        # Parse table rows
        if line.startswith('|') and current_state:
            cells = parse_table_row(line)
            
            # Different formats for different tables
            # Most: S/N, NAME, YEAR, PHONE, ADDRESS
            # Some: S/N, NAME, PHONE, ADDRESS (no year)
            # Redemption Camp: SN, NAME, PHONE, ADDRESS
            
            if len(cells) >= 3:
                # Clean S/N
                sn = cells[0] if cells[0] else ''
                sn = re.sub(r'[\*\\\.\[\]]', '', sn).strip()
                
                name = cells[1].strip() if len(cells) > 1 else ''
                if not name or name.upper() in ['NAMES', 'NAME', '']:
                    continue
                
                # Determine if we have year column or not
                # Check if cell 2 looks like a year or phone
                year = None
                phone = None
                address = None
                
                if len(cells) >= 5:
                    # Full format: S/N, NAME, YEAR, PHONE, ADDRESS
                    year_str = cells[2] if len(cells) > 2 else ''
                    phone_str = cells[3] if len(cells) > 3 else ''
                    address = cells[4] if len(cells) > 4 else ''
                    year = parse_year(year_str)
                    phone = parse_phones(phone_str)
                elif len(cells) == 4:
                    # Short format: S/N, NAME, PHONE, ADDRESS
                    phone_str = cells[2] if len(cells) > 2 else ''
                    address = cells[3] if len(cells) > 3 else ''
                    phone = parse_phones(phone_str)
                
                if address:
                    address = address.strip()
                    if not address or address.upper() == 'ADDRESS':
                        address = None
                
                entry = {
                    'name': name,
                    'state': current_state,
                    'address': address,
                    'phones': phone,
                    'year': year
                }
                entries.append(entry)
    
    # Generate PHP seeder
    alumni_entries = []
    for e in entries:
        entry = f"""            [
                'name' => {escape_php(e['name'])},
                'state' => {escape_php(e['state'])},
                'address' => {escape_php(e['address']) if e['address'] else 'null'},
                'phones' => {format_phones(e['phones'])},
                'year' => {escape_php(e['year']) if e['year'] else 'null'},
            ],"""
        alumni_entries.append(entry)
    
    alumni_array = '\n'.join(alumni_entries)
    
    seeder = f"""<?php

namespace Database\\Seeders;

use App\\Models\\Alumnus;
use App\\Models\\Tenure;
use Illuminate\\Database\\Seeder;

class AlumniLocationSeeder extends Seeder
{{
    /**
     * Seed Alumni location data (state and address).
     * Data source: ALUMNI.md - Alumni by location
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

            // Extract year to find tenure
            $year = $data['year'] ?? null;
            unset($data['year']);

            // Try to find existing alumnus by name
            $alumnus = Alumnus::where('name', $data['name'])->first();
            
            if ($alumnus) {{
                // Update state and address
                $updateData = ['state' => $data['state']];
                if ($data['address']) {{
                    $updateData['address'] = $data['address'];
                }}
                if ($data['phones'] && empty($alumnus->phones)) {{
                    $updateData['phones'] = $data['phones'];
                }}
                $alumnus->update($updateData);
                $updated++;
            }} else {{
                // Create new record if we have tenure year
                if ($year) {{
                    $tenure = Tenure::where('year', $year)->first();
                    if ($tenure) {{
                        $data['tenure_id'] = $tenure->id;
                        unset($data['year']);
                        Alumnus::create($data);
                        $created++;
                    }}
                }}
            }}
        }}

        $this->command->info("Alumni Location Seeder: Updated {{$updated}}, Created {{$created}} records");
    }}
}}
"""

    with open('database/seeders/AlumniLocationSeeder.php', 'w', encoding='utf-8') as f:
        f.write(seeder)

    print(f'Created AlumniLocationSeeder.php with {len(entries)} records')
    
    # Print summary by state
    from collections import Counter
    state_counts = Counter(e['state'] for e in entries)
    print("\nBy state:")
    for state, count in sorted(state_counts.items(), key=lambda x: -x[1]):
        print(f"  {state}: {count}")

if __name__ == '__main__':
    main()

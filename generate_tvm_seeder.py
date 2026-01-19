"""
Generate TVM Alumni Seeder from Word document.
"""
from docx import Document
import re

# Month mapping
MONTH_MAP = {
    'jan': 1, 'january': 1, 'feb': 2, 'february': 2, 'mar': 3, 'march': 3,
    'apr': 4, 'april': 4, 'may': 5, 'jun': 6, 'june': 6, 'jul': 7, 'july': 7,
    'aug': 8, 'august': 8, 'sep': 9, 'sept': 9, 'september': 9, 'oct': 10,
    'october': 10, 'nov': 11, 'november': 11, 'dec': 12, 'december': 12
}

def parse_dob(dob_str):
    if not dob_str:
        return None
    dob_str = dob_str.strip()
    day_match = re.search(r'(\d{1,2})', dob_str)
    day = int(day_match.group(1)) if day_match else None
    month = None
    for m, num in MONTH_MAP.items():
        if m in dob_str.lower():
            month = num
            break
    if day and month:
        return f'2000-{month:02d}-{day:02d}'
    return None

def parse_phones(phone_str):
    if not phone_str:
        return None
    phones = re.split(r'[/,]', phone_str)
    cleaned = [p.strip() for p in phones if p.strip()]
    return cleaned if cleaned else None

def escape_php(s):
    if s is None:
        return 'null'
    s = str(s).replace("\\", "\\\\").replace("'", "\\'")
    return f"'{s}'"

def format_phones(phones):
    if not phones:
        return 'null'
    return '[' + ', '.join([escape_php(p) for p in phones]) + ']'

def main():
    doc = Document('TVM ALUMNI LIST.docx')
    entries = []

    for table in doc.tables:
        for row in table.rows[1:]:
            cells = [cell.text.strip() for cell in row.cells]
            if len(cells) >= 2 and cells[1]:
                name = cells[1]
                phone = cells[2] if len(cells) > 2 else ''
                dob = cells[3] if len(cells) > 3 else ''
                
                phones = parse_phones(phone)
                birth_date = parse_dob(dob)
                
                entry = f"""            [
                'name' => {escape_php(name)},
                'phones' => {format_phones(phones)},
                'birth_date' => {escape_php(birth_date) if birth_date else 'null'},
                'unit' => 'Choir Unit',
            ],"""
                entries.append(entry)

    alumni_array = '\n'.join(entries)

    seeder = f"""<?php

namespace Database\\Seeders;

use App\\Models\\Alumnus;
use App\\Models\\Tenure;
use Illuminate\\Database\\Seeder;

class TvmAlumniSeeder extends Seeder
{{
    /**
     * Seed TVM (Technical Video Ministry / Choir) Alumni.
     * Data source: TVM ALUMNI LIST.docx
     */
    public function run(): void
    {{
        // TVM alumni may come from various years, so we don't assign a specific tenure
        $alumni = [
{alumni_array}
        ];

        foreach ($alumni as $data) {{
            if (empty($data['name'])) {{
                continue;
            }}

            // Try to find existing alumnus by name and update unit
            $alumnus = Alumnus::where('name', $data['name'])->first();
            
            if ($alumnus) {{
                // Update unit to Choir Unit (TVM)
                $alumnus->update(['unit' => 'Choir Unit']);
            }} else {{
                // Create new record without tenure
                Alumnus::create($data);
            }}
        }}

        $this->command->info('Seeded TVM Alumni: ' . count($alumni) . ' records');
    }}
}}
"""

    with open('database/seeders/TvmAlumniSeeder.php', 'w', encoding='utf-8') as f:
        f.write(seeder)

    print(f'Created TvmAlumniSeeder.php with {len(entries)} records')

if __name__ == '__main__':
    main()

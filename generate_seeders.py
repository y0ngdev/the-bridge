"""
Generate Laravel Seeder files from JSON data.
Converts JSON alumni data to PHP seeder format matching the database structure.

Database alumni table structure:
- name: string
- email: string|null (unique)
- phones: json|null (array of phone strings)
- department: string|null
- gender: enum('M', 'F')|null
- is_futa_staff: boolean (default false)
- birth_date: date|null
- past_exco_office: string|null
- current_exco_office: string|null
- unit: string|null
- state: string|null (NigerianState enum value)
- address: text|null
- tenure_id: foreign key
"""

import json
import os
import re
from datetime import datetime

# Nigerian states for matching (from NigerianState enum)
NIGERIAN_STATES = [
    'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno',
    'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT', 'Gombe', 'Imo',
    'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos',
    'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau', 'Rivers',
    'Sokoto', 'Taraba', 'Yobe', 'Zamfara'
]

# State name variations mapping
STATE_VARIATIONS = {
    'akwa ibom': 'Akwa Ibom',
    'akwa-ibom': 'Akwa Ibom',
    'cross river': 'Cross River',
    'cross-river': 'Cross River',
    'fct': 'FCT',
    'abuja': 'FCT',
    'federal capital territory': 'FCT',
}

# Unit mapping based on Unit enum (app/Enums/Unit.php)
UNIT_MAPPINGS = {
    # Academic Unit
    'academic': 'Academic Unit',
    'academi': 'Academic Unit',
    # Alumni Relations Unit
    'alumni': 'Alumni Relations Unit',
    'alumni relations': 'Alumni Relations Unit',
    # Bible Study Unit
    'bible study': 'Bible Study Unit',
    'b/study': 'Bible Study Unit',
    'b/ study': 'Bible Study Unit',
    # Choir Unit (TVM = Technical Video Ministry was the old name)
    'choir': 'Choir Unit',
    'tvm': 'Choir Unit',
    'technical video ministry': 'Choir Unit',
    # Drama Unit (DDF = Drama and Dance Fellowship was the old name)
    'ddf': 'Drama Unit',
    'drama and dance fellowship': 'Drama Unit',
    'drama': 'Drama Unit',
    # Editorial Unit
    'editorial': 'Editorial Unit',
    # Evangelism Unit
    'evangelism': 'Evangelism Unit',
    'evang': 'Evangelism Unit',
    # Follow up/Counselling Unit
    'follow-up': 'Follow up/Counselling Unit',
    'follow-': 'Follow up/Counselling Unit',
    'follow up': 'Follow up/Counselling Unit',
    'counselling': 'Follow up/Counselling Unit',
    # Library Unit
    'library': 'Library Unit',
    # Media and Ambience Unit
    'media': 'Media and Ambience Unit',
    'media and ambience': 'Media and Ambience Unit',
    'publicity': 'Media and Ambience Unit',
    'publicit': 'Media and Ambience Unit',
    'puplicit': 'Media and Ambience Unit',
    # Organising Unit
    'organizing': 'Organising Unit',
    'organising': 'Organising Unit',
    'organis': 'Organising Unit',
    # Prayer Unit
    'prayer': 'Prayer Unit',
    # President's Unit
    "president's unit": "President's Unit",
    'presidents unit': "President's Unit",
    # Sanctuary Keeping Unit
    'sanctuary': 'Sanctuary Keeping Unit',
    'sanctuary keeping': 'Sanctuary Keeping Unit',
    'santuary keepers': 'Sanctuary Keeping Unit',
    'santuary': 'Sanctuary Keeping Unit',
    # Ushering Unit
    'ushering': 'Ushering Unit',
    'usher': 'Ushering Unit',
    'usherin': 'Ushering Unit',
    # Welfare Unit
    'welfare': 'Welfare Unit',
}

# Month name to number mapping
MONTH_MAP = {
    'jan': 1, 'january': 1,
    'feb': 2, 'february': 2,
    'mar': 3, 'march': 3,
    'apr': 4, 'april': 4,
    'may': 5,
    'jun': 6, 'june': 6,
    'jul': 7, 'july': 7,
    'aug': 8, 'august': 8,
    'sep': 9, 'sept': 9, 'september': 9,
    'oct': 10, 'october': 10,
    'nov': 11, 'november': 11,
    'dec': 12, 'december': 12,
}

# Column mappings for different sheet formats
# Each mapping specifies which numeric key corresponds to which field
COLUMN_MAPPINGS = {
    # Format: "1": NO., "2": NAME, "3": PHONE, "4": DEPT, "5": GENDER, "6": DOB
    '2017_2018': {'name': '2', 'phone': '3', 'department': '4', 'gender': '5', 'birth_date': '6'},
    '2015_2016': {'name': '1', 'department': '2', 'address': '3', 'birth_date': '4', 'phone': '5', 'gender': '6'},
    # 2008_2009: "1": S/N, "2": NAME, "3": SEX, "4": DEPT., "5": PHONE NO, "6": D.O.B
    '2008_2009': {'name': '2', 'gender': '3', 'department': '4', 'phone': '5', 'birth_date': '6'},
    # 2012_2013: "2": S/N, "5": NAME, "6": SEX, "7": DEPT, "9": PHONE NO., "10": D.O.B
    '2012_2013': {'name': '5', 'gender': '6', 'department': '7', 'phone': '9', 'birth_date': '10'},
    '2004_2005': {'name': '2', 'department': '3', 'gender': '4'},
    # Default fallback for sheets with named columns
    'default': {
        'name': 'name', 
        'department': 'department', 
        'phone': 'phone', 
        'gender': 'gender', 
        'email': 'email', 
        'address': 'address', 
        'state': 'extracted_state',
        'birth_date': 'birth_date',
        'birth_month': 'birth_month',
    }
}

def extract_state_from_address(address):
    """Extract Nigerian state from address string."""
    if not address:
        return None
    
    address_lower = str(address).lower()
    
    # Check for exact state matches first
    for state in NIGERIAN_STATES:
        if state.lower() in address_lower:
            return state
    
    # Check for variations
    for variation, state in STATE_VARIATIONS.items():
        if variation in address_lower:
            return state
    
    return None

def parse_phones(phone_value):
    """Parse phone numbers from various formats and return array of strings."""
    if phone_value is None:
        return None
    
    if isinstance(phone_value, list):
        return [str(p).strip() for p in phone_value if p]
    
    phone_str = str(phone_value)
    
    # If it's just a number, convert to string with leading zero if needed
    if isinstance(phone_value, (int, float)):
        phone_str = str(int(phone_value))
        if len(phone_str) == 10 and not phone_str.startswith('0'):
            phone_str = '0' + phone_str
        return [phone_str] if phone_str else None
    
    # Split by comma, space, or other separators
    phones = re.split(r'[,\s]+', phone_str)
    phones = [p.strip() for p in phones if p.strip()]
    
    # Clean and format phone numbers
    cleaned = []
    for p in phones:
        # Remove non-numeric characters except for leading +
        p_clean = re.sub(r'[^0-9]', '', p)
        if len(p_clean) == 10 and not p_clean.startswith('0'):
            p_clean = '0' + p_clean
        if p_clean and len(p_clean) >= 10:
            cleaned.append(p_clean)
    
    return cleaned if cleaned else None

def normalize_gender(gender):
    """Normalize gender to 'M' or 'F' or None."""
    if not gender:
        return None
    
    g = str(gender).strip().upper()
    if g in ['M', 'MALE']:
        return 'M'
    elif g in ['F', 'FEMALE']:
        return 'F'
    return None

def normalize_unit(unit):
    """Normalize unit to match Unit enum values."""
    if not unit:
        return None
    
    unit_lower = str(unit).strip().lower()
    
    # Check for exact or partial matches in the mapping
    for key, value in UNIT_MAPPINGS.items():
        if key in unit_lower or unit_lower in key:
            return value
    
    return None

def parse_birth_date(birth_date_value, birth_month_value=None):
    """
    Parse birth date from various formats.
    Returns date string in 'Y-m-d' format or None.
    
    Formats handled:
    - "1ST JAN", "21ST JAN", "9TH FEB" (day + month in one string)
    - Separate day and month values
    - "2001-06-01 00:00:00" (datetime string)
    """
    if not birth_date_value:
        return None
    
    birth_str = str(birth_date_value).strip()
    
    # Check if it's already a datetime string like "2001-06-01 00:00:00"
    if re.match(r'^\d{4}-\d{2}-\d{2}', birth_str):
        try:
            # Parse and return just the date part
            dt = datetime.strptime(birth_str[:10], '%Y-%m-%d')
            # Use a reasonable year range check (birth years shouldn't be in future or too far past)
            if dt.year > 2000:
                # Likely a formatting error - we can only use month and day
                return f"2000-{dt.month:02d}-{dt.day:02d}"
            return dt.strftime('%Y-%m-%d')
        except:
            pass
    
    # Try to extract day and month from combined string like "1ST JAN" or "21ST JANUARY"
    day = None
    month = None
    
    # Pattern for day like "1ST", "2ND", "3RD", "21ST", etc.
    day_match = re.search(r'(\d{1,2})(?:ST|ND|RD|TH)?', birth_str, re.IGNORECASE)
    if day_match:
        day = int(day_match.group(1))
    
    # Find month in the string
    for month_name, month_num in MONTH_MAP.items():
        if month_name in birth_str.lower():
            month = month_num
            break
    
    # If no month found in birth_date_value but we have birth_month_value
    if not month and birth_month_value:
        month_str = str(birth_month_value).strip().lower()
        month = MONTH_MAP.get(month_str)
    
    # If we have day and month, construct a date
    # We don't have year, so we'll use a placeholder year (2000)
    if day and month:
        try:
            # Validate the date
            dt = datetime(2000, month, day)
            return f"2000-{month:02d}-{day:02d}"
        except ValueError:
            # Invalid date like Feb 30
            pass
    
    return None

def escape_php_string(s):
    """Escape a string for PHP single-quoted string."""
    if s is None:
        return 'null'
    s = str(s)
    s = s.replace("\\", "\\\\")
    s = s.replace("'", "\\'")
    return f"'{s}'"

def format_php_array(phones):
    """Format phone array for PHP."""
    if not phones:
        return 'null'
    formatted = [escape_php_string(p) for p in phones]
    return '[' + ', '.join(formatted) + ']'

def is_header_or_title_row(record):
    """Check if this record is likely a header or title row."""
    values = list(record.values())
    
    # Header keywords that indicate this is a header row
    header_keywords = ['name', 'names', 's/n', 'no.', 'dept', 'dept.', 'sex', 'gender', 'phone', 'phone number', 'dob', 'd.o.b', 'date of birth', 'lodge']
    
    for val in values:
        if val and str(val).lower().strip() in header_keywords:
            return True
    
    # Check if it's a title row (single value with certain keywords)
    if len(values) == 1:
        val = str(values[0]).lower()
        if any(kw in val for kw in ['redeemed', 'alumni', 'generation', 'family', 'chapter', 'arm of', 'data base']):
            return True
    
    return False

def get_field_mapping(year_code):
    """Get the column mapping for a specific year."""
    return COLUMN_MAPPINGS.get(year_code, COLUMN_MAPPINGS['default'])

def normalize_record(record, year_code):
    """Normalize a record with numeric or string keys to standard format."""
    # Skip header/title rows
    if is_header_or_title_row(record):
        return None
    
    mapping = get_field_mapping(year_code)
    
    result = {
        'name': None,
        'email': None,
        'phone': None,
        'department': None,
        'gender': None,
        'address': None,
        'state': None,
        'unit': None,
        'birth_date': None,
        'birth_month': None,
    }
    
    # Check if this is a numeric-keyed record or named-keyed record
    has_numeric_keys = any(key.isdigit() for key in record.keys())
    
    if has_numeric_keys:
        # Use year-specific mapping for numeric keys
        for field, key in mapping.items():
            if key in record:
                result[field] = record.get(key)
    else:
        # Use standard named keys
        result['name'] = (
            record.get('name') or 
            record.get('names') or 
            None
        )
        result['department'] = (
            record.get('department') or 
            record.get('dept') or 
            None
        )
        result['phone'] = (
            record.get('phone') or 
            record.get('phones') or 
            None
        )
        result['gender'] = (
            record.get('gender') or 
            record.get('sex') or 
            None
        )
        result['email'] = (
            record.get('email') or 
            record.get('e_mail_add') or 
            record.get('mail') or 
            None
        )
        result['address'] = (
            record.get('address') or 
            record.get('home_address') or 
            record.get('house_address') or 
            None
        )
        result['state'] = record.get('extracted_state')
        result['unit'] = record.get('unit')
        result['birth_date'] = record.get('birth_date')
        result['birth_month'] = record.get('birth_month')
    
    return result

def convert_record(record, year_code):
    """Convert a JSON record to match database structure."""
    normalized = normalize_record(record, year_code)
    if not normalized:
        return None
    
    # Get name - skip if no name
    name = normalized.get('name')
    if not name or str(name).strip() == '':
        return None
    
    # Skip if name looks like a header or is a number
    name_str = str(name).strip()
    if name_str.lower() in ['name', 'names', 's/n', 'sn', 'no.', '']:
        return None
    
    # Skip if name is purely numeric (serial number was picked up instead of name)
    if name_str.isdigit():
        return None
    
    # Get email
    email = normalized.get('email')
    if email:
        email = str(email).strip()
        if '@' not in email or '.' not in email:
            email = None
    
    # Get and parse phones
    phones = parse_phones(normalized.get('phone'))
    
    # Get department
    department = normalized.get('department')
    if department:
        department = str(department).strip().upper()
        if len(department) > 10 or department.isdigit():
            department = None
    
    # Get and normalize gender
    gender = normalize_gender(normalized.get('gender'))
    
    # Get address
    address = normalized.get('address')
    if address:
        address = str(address).strip()
    
    # Get state
    state = normalized.get('state')
    if not state and address:
        state = extract_state_from_address(address)
    
    # Get and normalize unit
    unit = normalize_unit(normalized.get('unit'))
    
    # Get birth_date
    birth_date = parse_birth_date(
        normalized.get('birth_date'), 
        normalized.get('birth_month')
    )
    
    return {
        'name': name_str,
        'email': email,
        'phones': phones,
        'department': department,
        'gender': gender,
        'state': state,
        'address': address,
        'unit': unit,
        'birth_date': birth_date,
    }

def generate_seeder_content(year_code, year_display, records):
    """Generate PHP seeder file content."""
    
    alumni_entries = []
    for record in records:
        converted = convert_record(record, year_code)
        if not converted:
            continue
        
        entry = f"""            [
                'name' => {escape_php_string(converted['name'])},
                'email' => {escape_php_string(converted['email']) if converted['email'] else 'null'},
                'phones' => {format_php_array(converted['phones'])},
                'department' => {escape_php_string(converted['department']) if converted['department'] else 'null'},
                'gender' => {escape_php_string(converted['gender']) if converted['gender'] else 'null'},
                'birth_date' => {escape_php_string(converted['birth_date']) if converted['birth_date'] else 'null'},
                'unit' => {escape_php_string(converted['unit']) if converted['unit'] else 'null'},
                'state' => {escape_php_string(converted['state']) if converted['state'] else 'null'},
                'address' => {escape_php_string(converted['address']) if converted['address'] else 'null'},
            ],"""
        alumni_entries.append(entry)
    
    alumni_array = "\n".join(alumni_entries)
    
    content = f"""<?php

namespace Database\\Seeders;

use App\\Models\\Alumnus;
use App\\Models\\Tenure;
use Illuminate\\Database\\Seeder;

class Alumni{year_code}Seeder extends Seeder
{{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {{
        $tenure = Tenure::firstOrCreate(
            ['year' => '{year_display}'],
            ['name' => '{year_display} Set']
        );

        $alumni = [
{alumni_array}
        ];

        foreach ($alumni as $data) {{
            if (empty($data['name'])) {{
                continue;
            }}

            Alumnus::updateOrCreate(
                ['name' => $data['name'], 'tenure_id' => $tenure->id],
                array_merge($data, ['tenure_id' => $tenure->id])
            );
        }}

        $this->command->info("Seeded {{$tenure->year}} alumni: " . count($alumni) . " records");
    }}
}}
"""
    return content

def main():
    json_dir = 'app/data'
    output_dir = 'database/seeders'
    
    years = [
        '2000_2001', '2001_2002', '2002_2003', '2003_2004', '2004_2005',
        '2005_2006', '2006_2007', '2007_2008', '2008_2009', '2009_2010',
        '2010_2011', '2011_2012', '2012_2013', '2013_2014', '2014_2015',
        '2015_2016', '2016_2017', '2017_2018', '2018_2019'
    ]
    
    os.makedirs(output_dir, exist_ok=True)
    
    total_records = 0
    
    for year_code in years:
        json_file = os.path.join(json_dir, f'{year_code}.json')
        
        if not os.path.exists(json_file):
            print(f"Skipping {year_code}: JSON file not found")
            continue
        
        with open(json_file, 'r', encoding='utf-8') as f:
            records = json.load(f)
        
        year_display = year_code.replace('_', '-')
        
        content = generate_seeder_content(year_code, year_display, records)
        
        valid_count = sum(1 for r in records if convert_record(r, year_code) is not None)
        total_records += valid_count
        
        output_file = os.path.join(output_dir, f'Alumni{year_code}Seeder.php')
        with open(output_file, 'w', encoding='utf-8') as f:
            f.write(content)
        
        print(f"Generated: Alumni{year_code}Seeder.php ({valid_count} valid records from {len(records)} total)")
    
    # Generate master seeder
    master_content = """<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;

class AlumniDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder calls all individual year seeders.
     */
    public function run(): void
    {
        $this->call([
"""
    
    for year_code in years:
        master_content += f"            Alumni{year_code}Seeder::class,\n"
    
    master_content += """        ]);
    }
}
"""
    
    with open(os.path.join(output_dir, 'AlumniDataSeeder.php'), 'w', encoding='utf-8') as f:
        f.write(master_content)
    
    print("\nGenerated: AlumniDataSeeder.php (master seeder)")
    print(f"\nTotal: {len(years)} seeder files created with {total_records} total records")

if __name__ == '__main__':
    main()

"""
Validate that no data was lost during the conversion from JSON to seeders.
This script compares the source JSON files with the generated PHP seeders.
"""

import json
import os
import re

# Column mappings (same as in generate_seeders.py)
COLUMN_MAPPINGS = {
    '2017_2018': {'name': '2', 'phone': '3', 'department': '4', 'gender': '5', 'birth_date': '6'},
    '2015_2016': {'name': '1', 'department': '2', 'address': '3', 'birth_date': '4', 'phone': '5', 'gender': '6'},
    '2004_2005': {'name': '2', 'department': '3', 'gender': '4'},
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

def is_header_or_title_row(record):
    """Check if this record is likely a header or title row."""
    values = list(record.values())
    
    header_keywords = ['name', 'names', 's/n', 'no.', 'dept', 'dept.', 'sex', 'gender', 'phone', 'phone number', 'dob', 'd.o.b', 'date of birth', 'lodge']
    
    for val in values:
        if val and str(val).lower().strip() in header_keywords:
            return True
    
    if len(values) == 1:
        val = str(values[0]).lower()
        if any(kw in val for kw in ['redeemed', 'alumni', 'generation', 'family', 'chapter', 'arm of', 'data base']):
            return True
    
    return False

def get_name_from_record(record, year_code):
    """Extract name from a record based on the year's column mapping."""
    mapping = COLUMN_MAPPINGS.get(year_code, COLUMN_MAPPINGS['default'])
    
    has_numeric_keys = any(key.isdigit() for key in record.keys())
    
    if has_numeric_keys:
        name_key = mapping.get('name', '2')
        return record.get(name_key)
    else:
        return record.get('name') or record.get('names')

def validate_year(year_code):
    """Validate a single year's data conversion."""
    json_file = f'app/data/{year_code}.json'
    seeder_file = f'database/seeders/Alumni{year_code}Seeder.php'
    
    if not os.path.exists(json_file):
        return {'error': f'JSON file not found: {json_file}'}
    
    if not os.path.exists(seeder_file):
        return {'error': f'Seeder file not found: {seeder_file}'}
    
    # Load JSON data
    with open(json_file, 'r', encoding='utf-8') as f:
        json_records = json.load(f)
    
    # Load seeder and extract names
    with open(seeder_file, 'r', encoding='utf-8') as f:
        seeder_content = f.read()
    
    # Extract all names from seeder using regex
    seeder_names = set()
    name_pattern = re.compile(r"'name'\s*=>\s*'([^']+)'")
    for match in name_pattern.finditer(seeder_content):
        name = match.group(1).replace("\\'", "'")
        seeder_names.add(name.lower().strip())
    
    # Analyze JSON records
    total_json = len(json_records)
    skipped_header = 0
    skipped_no_name = 0
    skipped_numeric_name = 0
    valid_json_names = []
    skipped_records = []
    
    for i, record in enumerate(json_records):
        if is_header_or_title_row(record):
            skipped_header += 1
            skipped_records.append({
                'index': i,
                'reason': 'Header/title row',
                'data': record
            })
            continue
        
        name = get_name_from_record(record, year_code)
        
        if not name or str(name).strip() == '':
            skipped_no_name += 1
            skipped_records.append({
                'index': i,
                'reason': 'No name found',
                'data': record
            })
            continue
        
        name_str = str(name).strip()
        
        if name_str.isdigit():
            skipped_numeric_name += 1
            skipped_records.append({
                'index': i,
                'reason': f'Numeric name (likely serial number): {name_str}',
                'data': record
            })
            continue
        
        if name_str.lower() in ['name', 'names', 's/n', 'sn', 'no.']:
            skipped_header += 1
            skipped_records.append({
                'index': i,
                'reason': f'Header keyword as name: {name_str}',
                'data': record
            })
            continue
        
        valid_json_names.append(name_str)
    
    # Check for missing names (in JSON but not in seeder)
    missing_in_seeder = []
    for name in valid_json_names:
        if name.lower().strip() not in seeder_names:
            missing_in_seeder.append(name)
    
    return {
        'year': year_code,
        'json_total': total_json,
        'json_valid': len(valid_json_names),
        'seeder_count': len(seeder_names),
        'skipped_header': skipped_header,
        'skipped_no_name': skipped_no_name,
        'skipped_numeric_name': skipped_numeric_name,
        'missing_in_seeder': missing_in_seeder,
        'skipped_records': skipped_records[:10],  # First 10 skipped records for review
        'match': len(valid_json_names) == len(seeder_names) and len(missing_in_seeder) == 0
    }

def main():
    years = [
        '2000_2001', '2001_2002', '2002_2003', '2003_2004', '2004_2005',
        '2005_2006', '2006_2007', '2007_2008', '2008_2009', '2009_2010',
        '2010_2011', '2011_2012', '2012_2013', '2013_2014', '2014_2015',
        '2015_2016', '2016_2017', '2017_2018', '2018_2019'
    ]
    
    print("=" * 80)
    print("DATA VALIDATION REPORT")
    print("=" * 80)
    
    total_json = 0
    total_seeder = 0
    issues = []
    
    for year_code in years:
        result = validate_year(year_code)
        
        if 'error' in result:
            print(f"\n{year_code}: ERROR - {result['error']}")
            continue
        
        total_json += result['json_valid']
        total_seeder += result['seeder_count']
        
        status = "✓ OK" if result['match'] else "⚠ MISMATCH"
        
        print(f"\n{result['year'].replace('_', '-')}:")
        print(f"  JSON total records:    {result['json_total']}")
        print(f"  JSON valid records:    {result['json_valid']}")
        print(f"  Seeder records:        {result['seeder_count']}")
        print(f"  Skipped (headers):     {result['skipped_header']}")
        print(f"  Skipped (no name):     {result['skipped_no_name']}")
        print(f"  Skipped (numeric):     {result['skipped_numeric_name']}")
        print(f"  Status: {status}")
        
        if result['missing_in_seeder']:
            print(f"  ⚠ MISSING IN SEEDER ({len(result['missing_in_seeder'])}):")
            for name in result['missing_in_seeder'][:5]:
                print(f"    - {name}")
            if len(result['missing_in_seeder']) > 5:
                print(f"    ... and {len(result['missing_in_seeder']) - 5} more")
            issues.append({
                'year': year_code,
                'missing': result['missing_in_seeder']
            })
        
        # Show sample of skipped records if there are issues
        if not result['match'] and result['skipped_records']:
            print(f"  Sample skipped records:")
            for sr in result['skipped_records'][:3]:
                print(f"    Index {sr['index']}: {sr['reason']}")
    
    print("\n" + "=" * 80)
    print("SUMMARY")
    print("=" * 80)
    print(f"Total valid JSON records:  {total_json}")
    print(f"Total seeder records:      {total_seeder}")
    print(f"Difference:                {total_json - total_seeder}")
    
    if issues:
        print(f"\n⚠ {len(issues)} year(s) have missing records in seeders!")
        print("\nTo investigate, check the 'missing_in_seeder' names above.")
    else:
        print(f"\n✓ All data validated successfully!")
    
    # Save detailed report to file
    report_file = 'validation_report.json'
    full_results = []
    for year_code in years:
        result = validate_year(year_code)
        if 'error' not in result:
            full_results.append(result)
    
    with open(report_file, 'w', encoding='utf-8') as f:
        json.dump(full_results, f, indent=2, ensure_ascii=False)
    
    print(f"\nDetailed report saved to: {report_file}")

if __name__ == '__main__':
    main()

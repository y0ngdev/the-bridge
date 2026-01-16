import pandas as pd
import json
import re
import os

# Nigerian states for matching
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

# Common column name mappings (lowercase -> standard name)
COLUMN_MAPPINGS = {
    's/n': 'sn',
    's.n': 'sn',
    'no': 'sn',
    'no.': 'sn',
    'name': 'name',
    'names': 'name',
    'full name': 'name',
    'fullname': 'name',
    'dept': 'department',
    'dept.': 'department',
    'department': 'department',
    'sex': 'gender',
    'gender': 'gender',
    'm/f': 'gender',
    'phone no': 'phone',
    'phone no.': 'phone',
    'pnone no': 'phone',
    'phone': 'phone',
    'phone number': 'phone',
    'tel': 'phone',
    'tel no': 'phone',
    'telephone': 'phone',
    'dob': 'birth_date',
    'd.o.b': 'birth_date',
    'd.o.b.': 'birth_date',
    'date of birth': 'birth_date',
    'month': 'birth_month',
    'email': 'email',
    'mail': 'email',
    'e-mail': 'email',
    'address': 'address',
    'home address': 'address',
    'house address': 'address',
    'current address': 'address',
    'location': 'address',
    'next of kin no': 'next_of_kin_phone',
    'next of kin': 'next_of_kin_phone',
    'office held/unit': 'unit',
    'office held': 'past_exco_office',
    'unit': 'unit',
    'state': 'state',
}

def extract_state_from_address(address):
    """Extract Nigerian state from address string."""
    if not address or pd.isna(address):
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

def clean_value(value):
    """Clean a cell value for JSON serialization."""
    if pd.isna(value):
        return None
    if isinstance(value, (int, float)):
        if pd.isna(value):
            return None
        return value
    return str(value).strip() if str(value).strip() else None

def normalize_column_name(col):
    """Normalize column name to a standard format."""
    if not col or pd.isna(col):
        return None
    col_lower = str(col).lower().strip()
    return COLUMN_MAPPINGS.get(col_lower, col_lower.replace(' ', '_').replace('.', ''))

def find_header_row(df):
    """Find the row that contains column headers."""
    # Look for rows containing typical header keywords
    header_keywords = ['name', 'names', 's/n', 'dept', 'sex', 'gender', 'phone', 'dob']
    
    for idx in range(min(10, len(df))):  # Check first 10 rows
        row = df.iloc[idx]
        row_values = [str(v).lower().strip() for v in row.values if not pd.isna(v)]
        matches = sum(1 for kw in header_keywords if any(kw in val for val in row_values))
        if matches >= 2:  # If at least 2 header keywords found
            return idx
    
    return None

def process_sheet(df, sheet_name):
    """Process a DataFrame sheet and return list of records."""
    # Find the actual header row
    header_row_idx = find_header_row(df)
    
    if header_row_idx is not None:
        # Create new column names from the header row
        new_columns = []
        for col in df.iloc[header_row_idx].values:
            if pd.isna(col) or str(col).strip() == '':
                new_columns.append(None)
            else:
                new_columns.append(normalize_column_name(col))
        
        # Skip rows up to and including header
        df = df.iloc[header_row_idx + 1:].copy()
        df.columns = new_columns if len(new_columns) == len(df.columns) else df.columns
    
    records = []
    # Get non-null columns
    valid_columns = [col for col in df.columns if col is not None]
    
    print(f"\nProcessing sheet: {sheet_name}")
    print(f"Header found at row: {header_row_idx}")
    print(f"Columns: {valid_columns}")
    
    for idx, row in df.iterrows():
        record = {}
        has_data = False
        
        for col in valid_columns:
            value = clean_value(row[col])
            if value is not None:
                has_data = True
                record[col] = value
        
        # Skip rows without meaningful data
        if not has_data:
            continue
        
        # Skip if this looks like a header row (contains header keywords)
        if 'name' in record and record.get('name', '').lower() in ['name', 'names', 's/n']:
            continue
        
        # Try to extract state from address column
        if 'address' in record:
            extracted_state = extract_state_from_address(record['address'])
            if extracted_state:
                record['extracted_state'] = extracted_state
        
        records.append(record)
    
    return records

def main():
    excel_file = 'RCF ALUMNI LIST update 2019.xlsx'
    output_dir = 'app/data'
    
    # Create output directory if it doesn't exist
    os.makedirs(output_dir, exist_ok=True)
    
    # Read all sheets from Excel file
    print(f"Reading Excel file: {excel_file}")
    excel_data = pd.ExcelFile(excel_file)
    sheet_names = excel_data.sheet_names
    
    print(f"Found {len(sheet_names)} sheets: {sheet_names}")
    
    for sheet_name in sheet_names:
        print(f"\n{'='*50}")
        print(f"Processing sheet: {sheet_name}")
        
        # Read the sheet without using first row as header
        df = pd.read_excel(excel_file, sheet_name=sheet_name, header=None)
        
        # Skip completely empty sheets
        if df.empty:
            print(f"Sheet '{sheet_name}' is empty, skipping...")
            continue
        
        # Process the sheet data
        records = process_sheet(df, sheet_name)
        
        if not records:
            print(f"No valid records found in sheet '{sheet_name}', skipping...")
            continue
        
        # Create a safe filename from sheet name
        safe_name = sheet_name.strip().replace(' ', '_').replace('-', '_')
        safe_name = re.sub(r'[^\w]', '', safe_name)
        output_file = os.path.join(output_dir, f"{safe_name}.json")
        
        # Write to JSON file
        with open(output_file, 'w', encoding='utf-8') as f:
            json.dump(records, f, indent=2, ensure_ascii=False, default=str)
        
        print(f"Saved {len(records)} records to {output_file}")
        
        # Show sample of extracted states
        states_found = [r.get('extracted_state') for r in records if r.get('extracted_state')]
        if states_found:
            unique_states = list(set(states_found))
            print(f"States extracted: {unique_states[:10]}{'...' if len(unique_states) > 10 else ''}")
        
        # Show sample record
        if records:
            print(f"Sample record: {records[0]}")

if __name__ == '__main__':
    main()

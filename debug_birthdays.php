<?php

use App\Models\Alumnus;
use Illuminate\Support\Facades\Log;

try {
    echo "Querying alumni...\n";
    $alumni = Alumnus::whereNotNull('birth_date')->take(5)->get();
    echo "Found " . $alumni->count() . " alumni.\n";

    echo "Testing appends...\n";
    foreach ($alumni as $a) {
        echo "ID: {$a->id}, Name: {$a->name}, Photo URL: {$a->photo_url}, Initials: {$a->initials}\n";
    }

    echo "Testing complex query...\n";
    $grouped = Alumnus::whereNotNull('birth_date')
        ->orderByRaw("strftime('%m', birth_date), strftime('%d', birth_date)")
        ->get()
        ->groupBy(fn($alumnus) => $alumnus->birth_date->format('F'));

    echo "Grouped count: " . $grouped->count() . "\n";
    echo "DONE\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

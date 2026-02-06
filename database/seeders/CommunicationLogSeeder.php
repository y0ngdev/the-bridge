<?php

namespace Database\Seeders;

use App\Models\CommunicationLog;
use Illuminate\Database\Seeder;

class CommunicationLogSeeder extends Seeder
{
    /**
     * Seed the communication logs table.
     */
    public function run(): void
    {
        $logs = [
            [
                'alumnus_id' => 2184,
                'user_id' => 1,
                'type' => 'call',
                'outcome' => 'successful',
                'notes' => 'birthday celebration',
                'occurred_at' => '2026-01-16 19:27:00',
                'session_id' => 38,
            ],
            [
                'alumnus_id' => 1641,
                'user_id' => 1,
                'type' => 'call',
                'outcome' => 'successful',
                'notes' => 'birthday celebration',
                'occurred_at' => '2026-01-16 19:28:00',
                'session_id' => 38,
            ],
            [
                'alumnus_id' => 1639,
                'user_id' => 1,
                'type' => 'call',
                'outcome' => 'successful',
                'notes' => 'birthday celebration',
                'occurred_at' => '2026-01-16 19:29:00',
                'session_id' => 38,
            ],
            [
                'alumnus_id' => 1364,
                'user_id' => 1,
                'type' => 'call',
                'outcome' => 'unsuccessful',
                'notes' => 'number cannot receive calls',
                'occurred_at' => '2026-01-18 08:42:00',
                'session_id' => null,
            ],
            [
                'alumnus_id' => 1365,
                'user_id' => 1,
                'type' => 'call',
                'outcome' => 'unsuccessful',
                'notes' => 'number cannot receive calls',
                'occurred_at' => '2026-01-18 08:42:00',
                'session_id' => null,
            ],
            [
                'alumnus_id' => 1366,
                'user_id' => 1,
                'type' => 'call',
                'outcome' => 'successful',
                'notes' => null,
                'occurred_at' => '2026-01-18 08:43:00',
                'session_id' => null,
            ],
        ];

        foreach ($logs as $data) {
            CommunicationLog::create($data);
        }

        $this->command->info('Seeded '.count($logs).' communication logs');
    }
}

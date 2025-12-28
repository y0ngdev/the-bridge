<?php

use App\Enums\RedemptionWeekDay;
use App\Models\Alumnus;
use App\Models\RedemptionWeekAttendance;
use App\Models\RedemptionWeekSession;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false]);
    $this->session = RedemptionWeekSession::factory()->create([
        'name' => "RW'25",
        'year' => 2025,
        'is_active' => true,
    ]);
    $this->alumnus = Alumnus::factory()->create();
});

describe('Redemption Week Sessions', function () {
    it('allows users to view session index', function () {
        $this->actingAs($this->user)
            ->get(route('redemption-week.index'))
            ->assertOk()
            ->assertSee("RW'25");
    });

    it('allows users to view session details', function () {
        $this->actingAs($this->user)
            ->get(route('redemption-week.show', $this->session))
            ->assertOk()
            ->assertSee("RW'25");
    });

    it('allows admins to create sessions', function () {
        $this->actingAs($this->admin)
            ->post(route('redemption-week.store'), [
                'name' => "RW'26",
                'year' => 2026,
                'is_active' => false,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('redemption_week_sessions', [
            'name' => "RW'26",
            'year' => 2026,
        ]);
    });

    it('prevents non-admins from creating sessions', function () {
        $this->actingAs($this->user)
            ->post(route('redemption-week.store'), [
                'name' => "RW'26",
                'year' => 2026,
            ])
            ->assertRedirect(); // Admin middleware redirects
    });

    it('allows admins to update sessions', function () {
        $this->actingAs($this->admin)
            ->put(route('redemption-week.update', $this->session), [
                'name' => 'Updated Name',
                'year' => 2025,
                'is_active' => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('redemption_week_sessions', [
            'id' => $this->session->id,
            'name' => 'Updated Name',
        ]);
    });

    it('allows admins to delete sessions', function () {
        $this->actingAs($this->admin)
            ->delete(route('redemption-week.destroy', $this->session))
            ->assertRedirect();

        $this->assertDatabaseMissing('redemption_week_sessions', [
            'id' => $this->session->id,
        ]);
    });

    it('validates session creation', function () {
        $this->actingAs($this->admin)
            ->post(route('redemption-week.store'), [])
            ->assertSessionHasErrors(['name', 'year']);
    });
});

describe('Redemption Week Attendance', function () {
    it('allows users to mark attendance', function () {
        $this->actingAs($this->user)
            ->post(route('redemption-week.attendance.store', $this->session), [
                'alumnus_ids' => [$this->alumnus->id],
                'event_day' => RedemptionWeekDay::CulturalDay->value,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('redemption_week_attendances', [
            'session_id' => $this->session->id,
            'alumnus_id' => $this->alumnus->id,
            'event_day' => RedemptionWeekDay::CulturalDay->value,
        ]);
    });

    it('allows bulk attendance marking', function () {
        $alumni = Alumnus::factory()->count(3)->create();

        $this->actingAs($this->user)
            ->post(route('redemption-week.attendance.store', $this->session), [
                'alumnus_ids' => $alumni->pluck('id')->toArray(),
                'event_day' => RedemptionWeekDay::WordNight->value,
            ])
            ->assertRedirect();

        expect(RedemptionWeekAttendance::where('session_id', $this->session->id)->count())->toBe(3);
    });

    it('prevents duplicate attendance', function () {
        // First attendance
        RedemptionWeekAttendance::create([
            'session_id' => $this->session->id,
            'alumnus_id' => $this->alumnus->id,
            'event_day' => RedemptionWeekDay::CulturalDay->value,
            'marked_by' => $this->user->id,
        ]);

        // Try to mark again - should not create duplicate
        $this->actingAs($this->user)
            ->post(route('redemption-week.attendance.store', $this->session), [
                'alumnus_ids' => [$this->alumnus->id],
                'event_day' => RedemptionWeekDay::CulturalDay->value,
            ])
            ->assertRedirect();

        expect(RedemptionWeekAttendance::where([
            'session_id' => $this->session->id,
            'alumnus_id' => $this->alumnus->id,
            'event_day' => RedemptionWeekDay::CulturalDay->value,
        ])->count())->toBe(1);
    });

    it('allows admins to remove attendance', function () {
        $attendance = RedemptionWeekAttendance::create([
            'session_id' => $this->session->id,
            'alumnus_id' => $this->alumnus->id,
            'event_day' => RedemptionWeekDay::DramaNight->value,
            'marked_by' => $this->user->id,
        ]);

        $this->actingAs($this->admin)
            ->delete(route('redemption-week.attendance.destroy', [
                'session' => $this->session->id,
                'attendance' => $attendance->id,
            ]))
            ->assertRedirect();

        $this->assertDatabaseMissing('redemption_week_attendances', [
            'id' => $attendance->id,
        ]);
    });

    it('prevents non-admins from removing attendance', function () {
        $attendance = RedemptionWeekAttendance::create([
            'session_id' => $this->session->id,
            'alumnus_id' => $this->alumnus->id,
            'event_day' => RedemptionWeekDay::PowerNight->value,
            'marked_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->delete(route('redemption-week.attendance.destroy', [
                'session' => $this->session->id,
                'attendance' => $attendance->id,
            ]))
            ->assertRedirect(); // Admin middleware redirects
    });

    it('validates attendance marking', function () {
        $this->actingAs($this->user)
            ->post(route('redemption-week.attendance.store', $this->session), [])
            ->assertSessionHasErrors(['alumnus_ids', 'event_day']);
    });
});

describe('Session Analytics', function () {
    it('calculates attendance statistics', function () {
        // Mark attendance for multiple days
        $alumni = Alumnus::factory()->count(5)->create();

        foreach (RedemptionWeekDay::cases() as $day) {
            foreach ($alumni as $alumnus) {
                RedemptionWeekAttendance::create([
                    'session_id' => $this->session->id,
                    'alumnus_id' => $alumnus->id,
                    'event_day' => $day->value,
                ]);
            }
        }

        $stats = $this->session->getAttendanceStats();

        expect($stats)->toHaveCount(7);
        expect($stats[RedemptionWeekDay::CulturalDay->value]['count'])->toBe(5);
    });

    it('tracks perfect attendance', function () {
        // Create alumnus with all 7 days
        $perfectAlumnus = Alumnus::factory()->create();

        foreach (RedemptionWeekDay::cases() as $day) {
            RedemptionWeekAttendance::create([
                'session_id' => $this->session->id,
                'alumnus_id' => $perfectAlumnus->id,
                'event_day' => $day->value,
            ]);
        }

        expect($this->session->perfect_attendance_count)->toBe(1);
    });

    it('counts unique attendees', function () {
        $alumni = Alumnus::factory()->count(3)->create();

        // Mark some for multiple days
        foreach ($alumni as $alumnus) {
            RedemptionWeekAttendance::create([
                'session_id' => $this->session->id,
                'alumnus_id' => $alumnus->id,
                'event_day' => RedemptionWeekDay::CulturalDay->value,
            ]);
        }

        expect($this->session->total_attendees)->toBe(3);
    });
});

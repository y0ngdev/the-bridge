<?php

namespace App\Console\Commands;

use App\Mail\DailyBirthdayMail;
use App\Mail\MonthlyBirthdayMail;
use App\Mail\WeeklyBirthdayMail;
use App\Models\Alumnus;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class SendBirthdayNotifications extends Command
{
    protected $signature = 'app:send-birthday-notifications
                            {--daily : Send today\'s birthdays}
                            {--weekly : Send this week\'s birthdays}
                            {--monthly : Send this month\'s birthdays}
                            {--email= : Override recipient email}';

    protected $description = 'Send birthday notification emails to admins';

    public function handle(): int
    {
        $email = $this->option('email') ?? config('notifications.birthday_notification_email') ?? config('mail.from.address');

        if (! $email) {
            $this->error('No email address specified. Set BIRTHDAY_NOTIFICATION_EMAIL in .env');

            return self::FAILURE;
        }

        $today = Carbon::today();

        if ($this->option('daily')) {
            $this->sendDailyNotification($email, $today);
        }

        if ($this->option('weekly')) {
            $this->sendWeeklyNotification($email, $today);
        }

        if ($this->option('monthly')) {
            $this->sendMonthlyNotification($email, $today);
        }

        if (! $this->option('daily') && ! $this->option('weekly') && ! $this->option('monthly')) {
            $this->error('Please specify at least one option: --daily, --weekly, or --monthly');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function sendDailyNotification(string $email, Carbon $today): void
    {
        $birthdays = Alumnus::whereMonth('birth_date', $today->month)
            ->whereDay('birth_date', $today->day)
            ->get();

        $this->info("Found {$birthdays->count()} birthday(s) today.");

        if ($birthdays->isEmpty()) {
            $this->info('No daily notification sent (no birthdays).');

            return;
        }

        Mail::to($email)->send(new DailyBirthdayMail($birthdays));
        $this->info("Daily birthday notification sent to {$email}");
    }

    private function sendWeeklyNotification(string $email, Carbon $today): void
    {
        $startOfWeek = $today->copy()->startOfWeek();
        $endOfWeek = $today->copy()->endOfWeek();

        $birthdays = Alumnus::whereNotNull('birth_date')
            ->get()
            ->filter(function ($alumnus) use ($startOfWeek, $endOfWeek, $today) {
                if (! $alumnus->birth_date) {
                    return false;
                }
                $birthdayThisYear = $alumnus->birth_date->copy()->year($today->year);

                return $birthdayThisYear->between($startOfWeek, $endOfWeek);
            });

        $this->info("Found {$birthdays->count()} birthday(s) this week.");

        if ($birthdays->isEmpty()) {
            $this->info('No weekly notification sent (no birthdays).');

            return;
        }

        Mail::to($email)->send(new WeeklyBirthdayMail($birthdays));
        $this->info("Weekly birthday notification sent to {$email}");
    }

    private function sendMonthlyNotification(string $email, Carbon $today): void
    {
        $birthdays = Alumnus::whereMonth('birth_date', $today->month)->get();

        $this->info("Found {$birthdays->count()} birthday(s) this month.");

        if ($birthdays->isEmpty()) {
            $this->info('No monthly notification sent (no birthdays).');

            return;
        }

        Mail::to($email)->send(new MonthlyBirthdayMail($birthdays));
        $this->info("Monthly birthday notification sent to {$email}");
    }
}

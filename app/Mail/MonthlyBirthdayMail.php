<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class MonthlyBirthdayMail extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $birthdays;

    public function __construct(Collection $birthdays)
    {
        $this->birthdays = $birthdays;
    }

    public function build(): self
    {
        $count = $this->birthdays->count();
        $month = now()->format('F Y');

        return $this->subject(config('app.name')." - {$count} Birthday(s) in {$month}")
            ->view('emails.birthdays-monthly');
    }
}

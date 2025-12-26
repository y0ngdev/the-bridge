<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BackupMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $backupPath;

    public string $backupName;

    /**
     * Create a new message instance.
     */
    public function __construct(string $backupPath, string $backupName)
    {
        $this->backupPath = $backupPath;
        $this->backupName = $backupName;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject(config('app.name').' - Database Backup '.now()->format('Y-m-d'))
            ->markdown('emails.backup')
            ->attach($this->backupPath, [
                'as' => $this->backupName,
                'mime' => 'application/zip',
            ]);
    }
}

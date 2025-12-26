<?php

namespace App\Console\Commands;

use App\Mail\BackupMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupDatabase extends Command
{
    protected $signature = 'app:backup-database {--email= : Email address to send the backup to}';

    protected $description = 'Backup the database and send via email';

    public function handle(): int
    {
        $this->info('Starting database backup...');

        $email = $this->option('email') ?? env('BACKUP_MAIL_TO') ?? config('mail.from.address');

        if (! $email) {
            $this->error('No email address specified. Use --email=your@email.com or set MAIL_FROM_ADDRESS in .env');

            return self::FAILURE;
        }

        $appName = config('app.name', 'backup');
        $timestamp = now()->format('Y-m-d-H-i-s');
        $zipName = "{$appName}-{$timestamp}.zip";
        $tempZip = storage_path("app/{$zipName}");

        // Create backup
        if (! $this->createBackup($tempZip)) {
            return self::FAILURE;
        }

        // Save to local storage for history
        $localPath = "{$appName}/{$zipName}";
        Storage::disk('local')->put($localPath, file_get_contents($tempZip));
        $this->info('✓ Saved to local storage.');

        // Send email
        try {
            Mail::to($email)->send(new BackupMail($tempZip, $zipName));
            $this->info("✓ Backup sent to {$email}");
        } catch (\Exception $e) {
            $this->error('Email failed: '.$e->getMessage());

            // Clean up
            if (file_exists($tempZip)) {
                unlink($tempZip);
            }

            return self::FAILURE;
        }

        // Clean up temp file
        if (file_exists($tempZip)) {
            unlink($tempZip);
        }

        $this->info('Backup completed successfully!');

        return self::SUCCESS;
    }

    /**
     * Create a backup zip file.
     */
    private function createBackup(string $tempZip): bool
    {
        $dbPath = database_path('database.sqlite');

        if (! file_exists($dbPath)) {
            $this->error('SQLite database file not found!');

            return false;
        }

        $zip = new ZipArchive;
        if ($zip->open($tempZip, ZipArchive::CREATE) !== true) {
            $this->error('Could not create zip file!');

            return false;
        }

        $zip->addFile($dbPath, 'database.sqlite');
        $zip->close();

        $this->info('✓ Database backup created.');

        return true;
    }
}

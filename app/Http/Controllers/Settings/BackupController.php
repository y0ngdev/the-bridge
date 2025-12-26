<?php

namespace App\Http\Controllers\Settings;

use App\Mail\BackupMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use ZipArchive;

class BackupController
{
    /**
     * Display backup settings page.
     */
    public function index(): Response
    {
        $backups = $this->getBackupList();
        $backupEmail = env('BACKUP_MAIL_TO', config('mail.from.address'));

        return Inertia::render('settings/Backup', [
            'backups' => $backups,
            'backupEmail' => $backupEmail,
        ]);
    }

    /**
     * Run backup manually and send via email.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $result = $this->createBackup();

            if (!$result) {
                return back()->with('error', 'Failed to create backup file.');
            }

            // Verify file exists before sending
            if (!file_exists($result['path'])) {
                return back()->with('error', 'Backup file was not created properly.');
            }

            // Send email with attachment
            Mail::to($request->email)->send(new BackupMail($result['path'], $result['name']));

            // Clean up temp file after sending
            if (file_exists($result['path'])) {
                unlink($result['path']);
            }

            return back()->with('success', 'Backup created and sent to ' . $request->email . '!');
        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Create a backup zip file.
     */
    private function createBackup(): ?array
    {
        $dbPath = database_path('database.sqlite');

        if (!file_exists($dbPath)) {
            return null;
        }

        $appName = config('app.name', 'backup');
        $timestamp = now()->format('Y-m-d-H-i-s');
        $zipName = "{$appName}-{$timestamp}.zip";
        $tempZip = storage_path("app/{$zipName}");

        $zip = new ZipArchive;
        if ($zip->open($tempZip, ZipArchive::CREATE) !== true) {
            return null;
        }

        $zip->addFile($dbPath, 'database.sqlite');
        $zip->close();

        // Also save to local storage for history
        $localPath = "{$appName}/{$zipName}";
        Storage::disk('local')->put($localPath, file_get_contents($tempZip));

        return [
            'path' => $tempZip,
            'name' => $zipName,
        ];
    }

    /**
     * Download a backup file.
     */
    public function download(string $filename)
    {
        $appName = config('app.name', 'laravel-backup');
        $path = "{$appName}/{$filename}";

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Backup file not found.');
        }

        return Storage::disk('local')->download($path, $filename);
    }

    /**
     * Get list of local backups.
     */
    private function getBackupList(): array
    {
        $backups = [];
        $appName = config('app.name', 'laravel-backup');
        $backupPath = $appName;

        if (Storage::disk('local')->exists($backupPath)) {
            $files = Storage::disk('local')->files($backupPath);

            foreach ($files as $file) {
                if (str_ends_with($file, '.zip')) {
                    $backups[] = [
                        'name' => basename($file),
                        'size' => $this->formatBytes(Storage::disk('local')->size($file)),
                        'date' => date('Y-m-d H:i:s', Storage::disk('local')->lastModified($file)),
                    ];
                }
            }
        }

        // Sort by date descending
        usort($backups, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));

        return $backups;
    }

    /**
     * Format bytes to human readable.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}

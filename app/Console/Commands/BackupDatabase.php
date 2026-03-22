<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BackupService;

class BackupDatabase extends Command
{
    protected $signature = 'backup:create
                            {--force : Force backup creation even if one was recently created}';

    protected $description = 'Create a backup of the database and uploaded files, then sync to AWS S3';

    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        parent::__construct();
        $this->backupService = $backupService;
    }

    public function handle()
    {
        $this->info('Starting backup process...');
        $this->newLine();

        $this->info('Creating backup of database and files...');
        
        $result = $this->backupService->createBackup();

        if ($result['success']) {
            $this->newLine();
            $this->info('✓ Backup created successfully!');
            $this->info('  Filename: ' . $result['filename']);
            $this->info('  Size: ' . $this->formatBytes($result['size']));
            $this->info('  Location: Local storage + AWS S3');
            $this->newLine();

            $stats = $this->backupService->getBackupStats();
            $this->info('Backup Statistics:');
            $this->info('  Total backups: ' . $stats['total_backups']);
            $this->info('  Local backups: ' . $stats['local_backups']);
            $this->info('  S3 backups: ' . $stats['s3_backups']);
            $this->info('  Total size: ' . $stats['total_size_formatted']);

            return Command::SUCCESS;
        } else {
            $this->newLine();
            $this->error('✗ Backup failed!');
            $this->error('  Error: ' . $result['message']);
            
            return Command::FAILURE;
        }
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

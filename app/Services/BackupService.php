<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use Exception;

class BackupService
{
    protected $backupDisk = 'backups';
    protected $s3Disk = 's3';

    public function createBackup(): array
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "sacco_backup_{$timestamp}.zip";
            $tempDir = storage_path('app/temp_backup_' . $timestamp);
            
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $sqlFile = $tempDir . '/database.sql';
            $this->exportDatabase($sqlFile);

            $zipPath = storage_path('app/backups/' . $filename);
            if (!file_exists(dirname($zipPath))) {
                mkdir(dirname($zipPath), 0755, true);
            }

            $this->createZipArchive($zipPath, $tempDir);

            $this->uploadToS3($filename, $zipPath);

            $this->cleanupTempFiles($tempDir);

            $fileSize = filesize($zipPath);

            return [
                'success' => true,
                'filename' => $filename,
                'size' => $fileSize,
                'path' => $zipPath,
                'message' => 'Backup created successfully and uploaded to S3'
            ];

        } catch (Exception $e) {
            Log::error('Backup creation failed: ' . $e->getMessage());
            
            if (isset($tempDir) && file_exists($tempDir)) {
                $this->cleanupTempFiles($tempDir);
            }

            return [
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ];
        }
    }

    protected function exportDatabase(string $sqlFile): void
    {
        $dbHost = config('database.connections.mysql.host');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $command = sprintf(
            'mysqldump --host=%s --user=%s --password=%s %s > %s 2>&1',
            escapeshellarg($dbHost),
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbName),
            escapeshellarg($sqlFile)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new Exception('Database export failed: ' . implode("\n", $output));
        }

        if (!file_exists($sqlFile) || filesize($sqlFile) === 0) {
            throw new Exception('Database export file is empty or was not created');
        }
    }

    protected function createZipArchive(string $zipPath, string $tempDir): void
    {
        $zip = new ZipArchive();
        
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception('Could not create ZIP archive');
        }

        $zip->addFile($tempDir . '/database.sql', 'database.sql');

        $uploadsPath = storage_path('app/public/documents');
        if (file_exists($uploadsPath)) {
            $this->addDirectoryToZip($zip, $uploadsPath, 'documents');
        }

        $formsPath = storage_path('app/public/uploaded_forms');
        if (file_exists($formsPath)) {
            $this->addDirectoryToZip($zip, $formsPath, 'uploaded_forms');
        }

        $zip->close();
    }

    protected function addDirectoryToZip(ZipArchive $zip, string $directory, string $zipPath): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $zipPath . '/' . substr($filePath, strlen($directory) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }

    protected function uploadToS3(string $filename, string $localPath): void
    {
        if (!config('filesystems.disks.s3.key')) {
            Log::warning('S3 credentials not configured. Backup stored locally only.');
            return;
        }

        try {
            $fileStream = fopen($localPath, 'r');
            
            Storage::disk($this->s3Disk)->put(
                'backups/' . $filename,
                $fileStream,
                'private'
            );

            if (is_resource($fileStream)) {
                fclose($fileStream);
            }

            Log::info("Backup uploaded to S3: backups/{$filename}");
        } catch (Exception $e) {
            Log::error('S3 upload failed: ' . $e->getMessage());
            throw new Exception('Failed to upload backup to S3: ' . $e->getMessage());
        }
    }

    public function listBackups(): array
    {
        $backups = [];

        $localFiles = Storage::disk($this->backupDisk)->files();
        
        foreach ($localFiles as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                $backups[] = [
                    'filename' => basename($file),
                    'size' => Storage::disk($this->backupDisk)->size($file),
                    'modified' => Storage::disk($this->backupDisk)->lastModified($file),
                    'location' => 'local',
                    'path' => Storage::disk($this->backupDisk)->path($file)
                ];
            }
        }

        try {
            if (config('filesystems.disks.s3.key')) {
                $s3Files = Storage::disk($this->s3Disk)->files('backups');
                
                foreach ($s3Files as $file) {
                    $filename = basename($file);
                    
                    $existingIndex = array_search($filename, array_column($backups, 'filename'));
                    
                    if ($existingIndex !== false) {
                        $backups[$existingIndex]['location'] = 'both';
                    } else {
                        $backups[] = [
                            'filename' => $filename,
                            'size' => Storage::disk($this->s3Disk)->size($file),
                            'modified' => Storage::disk($this->s3Disk)->lastModified($file),
                            'location' => 's3',
                            'path' => $file
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            Log::warning('Could not list S3 backups: ' . $e->getMessage());
        }

        usort($backups, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });

        return $backups;
    }

    public function deleteBackup(string $filename): array
    {
        try {
            $deleted = [];

            if (Storage::disk($this->backupDisk)->exists($filename)) {
                Storage::disk($this->backupDisk)->delete($filename);
                $deleted[] = 'local';
            }

            try {
                if (config('filesystems.disks.s3.key') && Storage::disk($this->s3Disk)->exists('backups/' . $filename)) {
                    Storage::disk($this->s3Disk)->delete('backups/' . $filename);
                    $deleted[] = 's3';
                }
            } catch (Exception $e) {
                Log::warning('Could not delete from S3: ' . $e->getMessage());
            }

            return [
                'success' => true,
                'message' => 'Backup deleted from: ' . implode(', ', $deleted),
                'deleted_from' => $deleted
            ];

        } catch (Exception $e) {
            Log::error('Backup deletion failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to delete backup: ' . $e->getMessage()
            ];
        }
    }

    public function downloadFromS3(string $filename): ?string
    {
        try {
            if (!config('filesystems.disks.s3.key')) {
                return null;
            }

            $localPath = storage_path('app/backups/' . $filename);
            
            if (!file_exists(dirname($localPath))) {
                mkdir(dirname($localPath), 0755, true);
            }

            $s3Path = 'backups/' . $filename;
            
            if (Storage::disk($this->s3Disk)->exists($s3Path)) {
                $contents = Storage::disk($this->s3Disk)->get($s3Path);
                file_put_contents($localPath, $contents);
                
                return $localPath;
            }

            return null;

        } catch (Exception $e) {
            Log::error('S3 download failed: ' . $e->getMessage());
            return null;
        }
    }

    public function restoreBackup(string $filename): array
    {
        try {
            $backupPath = Storage::disk($this->backupDisk)->path($filename);

            if (!file_exists($backupPath)) {
                $backupPath = $this->downloadFromS3($filename);
                
                if (!$backupPath) {
                    throw new Exception('Backup file not found locally or in S3');
                }
            }

            $preRestoreBackup = $this->createBackup();
            if (!$preRestoreBackup['success']) {
                throw new Exception('Failed to create pre-restore backup');
            }

            $extractPath = storage_path('app/restore_temp_' . time());
            mkdir($extractPath, 0755, true);

            $zip = new ZipArchive();
            if ($zip->open($backupPath) !== true) {
                throw new Exception('Could not open backup ZIP file');
            }

            $zip->extractTo($extractPath);
            $zip->close();

            $sqlFile = $extractPath . '/database.sql';
            if (!file_exists($sqlFile)) {
                throw new Exception('Database SQL file not found in backup');
            }

            $this->restoreDatabase($sqlFile);

            if (file_exists($extractPath . '/documents')) {
                $this->restoreDirectory($extractPath . '/documents', storage_path('app/public/documents'));
            }

            if (file_exists($extractPath . '/uploaded_forms')) {
                $this->restoreDirectory($extractPath . '/uploaded_forms', storage_path('app/public/uploaded_forms'));
            }

            $this->cleanupTempFiles($extractPath);

            return [
                'success' => true,
                'message' => 'Backup restored successfully',
                'pre_restore_backup' => $preRestoreBackup['filename']
            ];

        } catch (Exception $e) {
            Log::error('Backup restoration failed: ' . $e->getMessage());

            if (isset($extractPath) && file_exists($extractPath)) {
                $this->cleanupTempFiles($extractPath);
            }

            return [
                'success' => false,
                'message' => 'Restore failed: ' . $e->getMessage()
            ];
        }
    }

    protected function restoreDatabase(string $sqlFile): void
    {
        $dbHost = config('database.connections.mysql.host');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $command = sprintf(
            'mysql --host=%s --user=%s --password=%s %s < %s 2>&1',
            escapeshellarg($dbHost),
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbName),
            escapeshellarg($sqlFile)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new Exception('Database restore failed: ' . implode("\n", $output));
        }
    }

    protected function restoreDirectory(string $source, string $destination): void
    {
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            $targetPath = $destination . '/' . substr($file, strlen($source) + 1);
            
            if ($file->isDir()) {
                if (!file_exists($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }
            } else {
                copy($file, $targetPath);
            }
        }
    }

    protected function cleanupTempFiles(string $directory): void
    {
        if (!file_exists($directory)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($directory);
    }

    public function getBackupStats(): array
    {
        $backups = $this->listBackups();
        
        $totalSize = array_sum(array_column($backups, 'size'));
        $localCount = count(array_filter($backups, fn($b) => in_array($b['location'], ['local', 'both'])));
        $s3Count = count(array_filter($backups, fn($b) => in_array($b['location'], ['s3', 'both'])));

        return [
            'total_backups' => count($backups),
            'local_backups' => $localCount,
            's3_backups' => $s3Count,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'latest_backup' => !empty($backups) ? $backups[0] : null
        ];
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

<?php

namespace App\Services;

use App\Repositories\CheckFileRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class CheckFileService
{
    protected $checkFileRepository;

    public function __construct(CheckFileRepository $checkFileRepository)
    {
        $this->checkFileRepository = $checkFileRepository;
    }

    public function checkInFiles(array $fileIds, string $description = 'description')
    {
        Log::info('checkInFiles');
        DB::beginTransaction();

        try {
            foreach ($fileIds as $fileId) {
                $backupPath = $this->processFileCheckIn($fileId, $description);
            }

            DB::commit();

            return ['success' => true, 'message' => 'All files checked in successfully.' , 'path'=> $backupPath];
        } catch (Exception $e) {
            DB::rollBack();

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected function processFileCheckIn(int $fileId, string $description)
    {
        // Lock the file for update to prevent concurrent operations
        $file = $this->checkFileRepository->lockFileForUpdate($fileId);

        if (!$file) {
            throw new Exception("File with ID {$fileId} not found.");
        }

        if ($file->status !== 'free') {
            throw new Exception("File '{$file->name}' is not free for check-in.");
        }

        // Backup the file
        $backupPath = $this->createBackupForFile($file);

        // Update the file status to checked_in
        $this->checkFileRepository->updateFileStatus($file, 'checked_in');

        // Log changes in the audit trail
        $this->checkFileRepository->createAuditTrail([
            'file_id' => $file->id,
            'user_id' => Auth::id(),
            'change_type' => 'modified',
            'description' => $description,
        ]);
        return $backupPath;
    }

    protected function createBackupForFile($file): string
    {
        $sourcePath = storage_path('app/private/' . $file->path);

        // Check if the source file exists
        if (!file_exists($sourcePath)) {
            throw new Exception("Source file does not exist: " . $sourcePath);
        }

        // Define the backup directory
        $backupDir = storage_path('backups');

        // Check if the backup directory exists; create it if it doesn't
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true); // Create the directory if it doesn't exist
        }

        // Create the backup path
        $backupPath = $backupDir . '/' . $file->name . '_' . now()->format('Y-m-d-H-i-s') . '.' . pathinfo($file->path, PATHINFO_EXTENSION);

        // Attempt to copy the file
        if (!copy($sourcePath, $backupPath)) {
            throw new Exception("Failed to create backup copy. Source: $sourcePath, Destination: $backupPath");
        }

        // Record the backup (assuming this method exists)
        $this->checkFileRepository->createBackup($file->id, $backupPath);

        return $backupPath;
    }
}

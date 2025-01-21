<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Repositories\CheckFileRepository;
use Exception;
use Illuminate\Support\Facades\Log;
class CheckInFileJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected int $fileId;
    protected string $description;
    protected int $userId;

    public function __construct(int $fileId, string $description , int $userId)
    {
        $this->fileId = $fileId;
        $this->description = $description;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     * @throws Exception
     */
    public function handle(CheckFileRepository $checkFileRepository): string
    {
        
        // Lock the file for update to prevent concurrent operations
        $file = $checkFileRepository->lockFileForUpdate($this->fileId);

        if (!$file) {
            throw new Exception("File with ID $this->fileId not found.");
        }

        if ($file->status !== 'free') {
            throw new Exception("File '$file->name' is not free for check-in.");
        }

        // Backup the file
        $backupPath = $this->createBackupForFile($file, $checkFileRepository);

        Log::info('job');
        // Update the file status to checked_in
        $checkFileRepository->updateFileStatus($file, 'checked_in');

        // Log changes in the audit trail
        $checkFileRepository->createAuditTrail([
            'file_id' => $file->id,
            'user_id' => $this->userId,
            'change_type' => 'modified',
            'description' => $this->description,
        ]);

        return $backupPath;
    }

    /**
     *
     * @throws Exception
     */
    protected function createBackupForFile($file, CheckFileRepository $checkFileRepository): string
    {
        $sourcePath = storage_path('app/private/' . $file->path);

        if (!file_exists($sourcePath)) {
            throw new Exception("Source file does not exist: " . $sourcePath);
        }

        $backupDir = storage_path('backups');

        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $backupPath = $backupDir . '/' . $file->name . '_' . now()->format('Y-m-d-H-i-s') . '.' . pathinfo($file->path, PATHINFO_EXTENSION);

        if (!copy($sourcePath, $backupPath)) {
            throw new Exception("Failed to create backup copy. Source: $sourcePath, Destination: $backupPath");
        }

        $checkFileRepository->createBackup($file->id, $backupPath);

        return $backupPath;
    }

}

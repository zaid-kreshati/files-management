<?php

namespace App\Repositories;


use App\Models\File;
use App\Models\Backup;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Models\Checkout;
use Illuminate\Support\Facades\Auth;
use function Laravel\Prompts\error;

class CheckOutRepository
{

    /**
     * @throws Exception
     */
    public function findFileInGroupByName(int $groupId, string $fileName): ?File
    {
        $file = File::whereHas('groups', function ($query) use ($groupId) {
            $query->where('groups.id', $groupId);
        })->where('name', $fileName)->first();
        if (!$file) {
            throw new Exception("The file $fileName does not exist in this group");
        }
        if($file->status == 'free'){
            throw new Exception("The file $fileName is already checked out.");
        }
        return $file;
    }

    public function findByNameAndGroup(string $fileName, int $groupId)
    {
        return File::where('name', $fileName)
            ->where('group_id', $groupId)
            ->first();
    }

    public function getFileContents(string $filePath): string
    {
        if (!Storage::exists($filePath)) {
            throw new Exception("File not found at path: {$filePath}");
        }

        return Storage::get($filePath);
    }

    public function updateFileWithUploadedVersion(int $fileId, string $uploadedFilePath): bool
    {
        $file = $this->lockFileForUpdate($fileId);

        if (!$file) {
            throw new Exception("File not found.");
        }

        // Backup original file
        $backupPath = 'backups/' . basename($file->path) . '-' . now()->timestamp;
        Storage::copy($file->path, $backupPath);

        Backup::create([
            'file_id' => $file->id,
            'backup_path' => $backupPath,
        ]);

        // Swap the file path with the uploaded version
        $file->path = $uploadedFilePath;
        return $file->save();
    }

    public function lockFileForUpdate(int $fileId): ?File
    {
        return File::where('id', $fileId)->lockForUpdate()->first();
    }

    public function checkOutFile(int $fileId, string $action): bool
    {
        $userId = Auth::id();
        $actionTime = now()->format('Y-m-d:H-m');

        return (bool)Checkout::create([
            'file_id' => $fileId,
            'user_id' => $userId,
            'action' => $action,
            'action_time' => $actionTime,
        ]);
    }
}

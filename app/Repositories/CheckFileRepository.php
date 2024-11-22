<?php

namespace App\Repositories;

use App\Models\File;
use App\Models\Backup;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;

class CheckFileRepository
{
    public function findById(int $id): ?File
    {
        return File::find($id);
    }

    public function lockFileForUpdate(int $fileId): ?File
    {
        return File::where('id', $fileId)->lockForUpdate()->first();
    }

    public function updateFileStatus(File $file, string $status): bool
    {
        $file->status = $status;
        $file->checked_in_by = Auth::id();
        $file->checked_in_at = now();
        return $file->save();
    }

    public function createBackup(int $fileId, string $backupPath): string
    {
        $backup = Backup::create([
            'file_id' => $fileId,
            'backup_path' => $backupPath,
        ]);
        return $backup;
    }

    public function createAuditTrail(array $data): bool
    {
        return AuditTrail::create($data) ? true : false;
    }
}

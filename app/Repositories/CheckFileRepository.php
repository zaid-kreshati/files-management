<?php

namespace App\Repositories;

use App\Models\File;
use App\Models\Backup;
use App\Models\AuditTrail;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
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

    /**
     * @throws Exception
     */
    public function ValidateCheckinOwner(int $fileID , $fileName): void
    {
        $checkedInByUser = $this->findUserByFileId($fileID);
        $user_id = Auth::id();
        if($checkedInByUser->id != $user_id){
            throw new Exception("You cannot check out the file $fileName because you are not the one who checked it in.($checkedInByUser->name)");
        }
    }

    public function findUserByFileId(int $fileId)
    {
        $checkInUser_id = AuditTrail::where('file_id', $fileId)->orderBy('created_at', 'desc')->first()->user_id;
        return User::find($checkInUser_id);
    }


}

<?php

namespace App\Repositories;


use App\Models\File;
use App\Models\Group;


class FileRepository
{
    
    public function create(array $data): File
    {
        return File::create($data);
    }

    public function find(int $fileId): File
    {
        return File::find($fileId);
    }

    public function updateApprovalStatus(int $fileId, string $status): bool
    {
        return File::where('id', $fileId)->update(['approval_status' => $status]);
    }


    public function getPendingFilesByGroupId(int $groupId)
    {
        return Group::find($groupId)
            ->files()
            ->where('approval_status', 'pending')
            ->get();
    }


    public function getApprovedFilesByGroupId(int $groupId)
    {
        return File::whereHas('groups', function ($query) use ($groupId) {
            $query->where('groups.id', $groupId);
        })
            ->where('approval_status', 'approved')
            ->get();
    }




}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Repositories\FileRepository;
use App\Repositories\GroupRepository;
use Exception;
use App\Models\File;
class FileService
{

    protected GroupRepository $groupRepository;
    protected FileRepository $fileRepository;

    public function __construct(GroupRepository $groupRepository, FileRepository $fileRepository)
    {
        $this->groupRepository = $groupRepository;
        $this->fileRepository = $fileRepository;
    }

    public function uploadFileWithPendingApproval(int $groupId, string $fileName, string $filePath)
    {
        $group = $this->groupRepository->findById($groupId);

        if (!$group) {
            throw new Exception("Group not found.");
        }

        if ($group->files()->where('name', $fileName)->exists()) {
            throw new Exception("A file with the same name already exists in this group.");
        }

        $file = $this->fileRepository->create([
            'name' => $fileName,
            'path' => $filePath,
            'status' => 'free',  // Default status
            'approval_status' => 'pending', // Awaiting approval
        ]);

        // Attach the file to the group using the pivot table
        $group->files()->attach($file->id);

        return $file;
    }


    public function approveFile(int $fileId, string $approvalStatus): File
    {
        $file = $this->fileRepository->find($fileId);

        if (!$file) {
            throw new Exception("File not found.");
        }

        $group = $file->groups()->first();

        if (Auth::id() !== $group->owner_id) {
            throw new Exception("Only the group owner can approve or reject files.");
        }

        $reaponse=$this->fileRepository->updateApprovalStatus($fileId, $approvalStatus);
        return $file;
    }

    public function getPendingFiles(int $groupId)
    {
        $group = $this->groupRepository->findById($groupId);

        if (!$group) {
            throw new Exception("Group not found.");
        }

        if (Auth::id() !== $group->owner_id) {
            throw new Exception("Only the group owner can view pending files.");
        }

        return $this->fileRepository->getPendingFilesByGroupId($groupId);
    }


    public function getApprovedFiles(int $groupId)
    {
        $group = $this->groupRepository->findById($groupId);

        if (!$group) {
            throw new Exception("Group not found.");
        }

        return $this->fileRepository->getApprovedFilesByGroupId($groupId);
    }




}

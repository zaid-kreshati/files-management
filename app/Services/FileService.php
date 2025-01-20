<?php

namespace App\Services;

use App\Traits\JsonResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\FileRepository;
use App\Repositories\GroupRepository;
use Exception;
use App\Models\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileService
{

    protected GroupRepository $groupRepository;
    protected FileRepository $fileRepository;

    use JsonResponseTrait;
    public function __construct(GroupRepository $groupRepository, FileRepository $fileRepository)
    {
        $this->groupRepository = $groupRepository;
        $this->fileRepository = $fileRepository;
    }

    /**
     * @throws Exception
     */
    public function uploadFileWithPendingApproval(int $groupId, string $fileName, string $filePath): File
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

    /**
     * @throws Exception
     */
    public function validateFilesExist(array $fileNames): void
    {
        foreach ($fileNames as $fileName) {
            if (!Storage::disk('private')->exists("group_files/$fileName")) {
                throw new Exception("File not found: $fileName");
            }
        }
    }

    /**
     * @throws Exception
     */
    public function approveFile(int $fileId, string $approvalStatus): File
    {
        $file = $this->fileRepository->find($fileId);
        $group = $file->groups()->first();

        if (Auth::id() !== $group->owner_id) {
            throw new Exception("Only the group owner can approve or reject files.");
        }
        $this->fileRepository->updateApprovalStatus($fileId, $approvalStatus);
        return $file;
    }

    /**
     * @throws Exception
     */
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


    /**
     * @throws Exception
     */
    public function getApprovedFiles(int $groupId)
    {
        $group = $this->groupRepository->findById($groupId);

        if (!$group) {
            throw new Exception("Group not found.");
        }

        return $this->fileRepository->getApprovedFilesByGroupId($groupId);
    }


    public function validateUploadRequest(Request $request): array
    {
        return $request->validate([
            'file' => 'required|file|mimes:txt|max:2048',
        ]);
    }

    public function storeUploadedFile($file, string $fileName): string
    {
        return $file->storeAs('group_files', $fileName);
    }
    /**
     * @throws Exception
     */
    public function processApproval($group, $file, int $userId): string
    {
        if ($group->owner_id == $userId) {
            $this->approveFile($file->id, 'approved');
            $file->approval_status = 'approved';
            return 'File uploaded successfully.';
        }
        return 'File uploaded successfully and waiting for approval.';
    }

    /**
     * @throws Exception
     */
    public function deleteFile($fileId)
    {
        $file = File::find($fileId);
        $file->delete();
        Storage::delete($file->path);
        return $this->successResponse($file, 'File deleted successfully.');
    }

}

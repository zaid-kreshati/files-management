<?php

namespace App\Http\Controllers\web;

use App\Services\FileService;
use App\Services\GroupService;
use App\Services\OpenDownloadFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Traits\JsonResponseTrait;
use App\Models\User;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;


class FileController extends Controller
{
    use JsonResponseTrait;

    protected GroupService $groupService;
    protected FileService $fileService;
    protected OpenDownloadFileService $openDownloadFileService;

    public function __construct(GroupService $groupService, FileService $fileService, OpenDownloadFileService $openDownloadFileService)
    {
        $this->groupService = $groupService;
        $this->fileService = $fileService;
        $this->openDownloadFileService = $openDownloadFileService;
    }

    public function uploadFile(Request $request, $groupId): JsonResponse
    {
        try {
            $data = $this->fileService->validateUploadRequest($request);

            $group = $this->groupService->getGroupById($groupId);

            $fileName = $data['file']->getClientOriginalName();
            $path = $this->fileService->storeUploadedFile($data['file'], $fileName);

            $file = $this->fileService->uploadFileWithPendingApproval($groupId, $fileName, $path);

            $message = $this->fileService->processApproval($group, $file, Auth::id());

            return $this->successResponse($file, $message);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 'File upload failed.');
        }
    }

    public function deleteFile(int $fileId): JsonResponse
    {
        try {
            $result = $this->fileService->deleteFile($fileId);
            return $this->successResponse($result, 'File deleted successfully.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 'File deletion failed.');
        }
    }

    public function approveFile(Request $request, $fileId): JsonResponse
    {
        try {
            $file = $this->fileService->approveFile($fileId, $request['approval_status']);
            return $this->successResponse($file, 'File approval updated successfully.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 'File approval updated failed.');
        }
    }

    public function getFilesForApproval($groupId): JsonResponse
    {
        try {
            $files = $this->fileService->getPendingFiles($groupId);
            return $this->successResponse($files, 'Files for approval fetched successfully.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 'Files for approval fetch failed.');
        }
    }

    /**
     * @throws Exception
     */
    public function getApprovedFiles($groupId): View
    {
        $group = $this->groupService->getGroupById($groupId);
        $owner = $group->owner_id === Auth::id();

        $files = $this->fileService->getApprovedFiles($groupId);

        // Get pending files only if the user is the owner
        $pendingFiles = $owner ? $this->fileService->getPendingFiles($groupId) : null;

        // Retrieve users who are not group members
        $memberIds = $group->members->pluck('id'); // Retrieve member IDs
        $users = User::whereNotIn('id', $memberIds)->get();

        return view('home', [
            'files' => $files,
            'groups' => null,
            'groupId' => $groupId,
            'users' => $users,
            'group' => $group,
            'status' => 'files',
            'pendingFiles' => $pendingFiles,
            'owner' => $owner,
        ]);
    }


    // Make New Service : OpenDownloadFileService
    public function openFile($fileId)
    {
        $response = $this->openDownloadFileService->openFile($fileId);
        if ($response['success']) {
            return redirect()->back();
        }
        return $this->errorResponse($response['message']);
    }

    public function openBackup(Request $request): void
    {
        $this->openDownloadFileService->openBackup($request);
    }

    public function downloadFile($fileId): \Symfony\Component\HttpFoundation\BinaryFileResponse|array
    {
        return $this->openDownloadFileService->downloadFile($fileId);
    }

    public function restoreBackup(string $backupPath, string $filePath): array
    {
        return $this->openDownloadFileService->restoreBackup($backupPath, $filePath);
    }

    public function export($fileId, Request $request)
    {
        return $this->openDownloadFileService->export($fileId, $request);
    }

}

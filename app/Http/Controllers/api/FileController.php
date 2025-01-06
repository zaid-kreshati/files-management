<?php

namespace App\Http\Controllers\api;

use App\Services\FileService;
use App\Services\GroupService;
use Illuminate\Http\Request;
use Exception;
use App\Http\Controllers\Controller;


class FileController extends Controller
{

    protected GroupService $groupService;
    protected FileService $fileService;

    public function __construct(GroupService $groupService, FileService $fileService)
    {
        $this->groupService = $groupService;
        $this->fileService = $fileService;
    }


    public function uploadFile(Request $request, $groupId)
    {
        $data = $request->validate([
            'file' => 'required|file|mimes:txt|max:2048',
        ]);

        try {
            $fileName = $data['file']->getClientOriginalName();
            $path = $data['file']->storeAs('group_files',  $fileName);

            $this->fileService->uploadFileWithPendingApproval($groupId, $fileName, $path);

            return response()->json(['success' => true, 'message' => 'File uploaded and awaiting approval.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }




    public function approveFile(Request $request, $fileId)
    {
        $data = $request->validate([
            'approval_status' => 'required|in:approved,rejected',
        ]);

        try {
            $this->fileService->approveFile($fileId, $data['approval_status']);
            return response()->json(['success' => true, 'message' => 'File approval updated successfully.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }


    public function getFilesForApproval($groupId)
    {
        try {
            $files = $this->fileService->getFilesForApproval($groupId);
            return response()->json(['success' => true, 'files' => $files]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        }
    }

    public function getApprovedFiles($groupId)
    {
        try {
            $files = $this->fileService->getApprovedFiles($groupId);
            return response()->json(['success' => true, 'files' => $files]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}

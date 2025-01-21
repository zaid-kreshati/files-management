<?php

namespace App\Http\Controllers\web;

use App\Services\FileService;
use App\Services\GroupService;
use Illuminate\Http\Request;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Traits\JsonResponseTrait;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AuditTrail;

class FileController extends Controller
{
    use JsonResponseTrait;

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
            $userId = Auth::user()->id;
            $group = $this->groupService->getGroupById($groupId);
            $owner_id = $group->owner_id;

            $fileName = $data['file']->getClientOriginalName();
            $path = $data['file']->storeAs('group_files',  $fileName);
            $file = $this->fileService->uploadFileWithPendingApproval($groupId, $fileName, $path);
            if ($owner_id == $userId) {
                $this->fileService->approveFile($file->id, 'approved');
                $file->approval_status = 'approved';
                $message = 'File uploaded successfully.';
            } else {
                $message = 'File uploaded successfully and waiting for approval.';
            }
            return $this->successResponse($file, $message);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 'File uploaded failed.');
        }
    }

    public function deleteFile($fileId)
    {
        $file = File::find($fileId);
        $file->delete();
        Storage::delete($file->path);
        return $this->successResponse($file, 'File deleted successfully.');
    }


    public function approveFile(Request $request, $fileId)
    {
        $data = $request->validate([
            'approval_status' => 'required|in:approved,rejected',
        ]);
        Log::info('approve file');
        Log::info($data);


        try {
            $file = $this->fileService->approveFile($fileId, $data['approval_status']);
            return $this->successResponse($file, 'File approval updated successfully.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 'File approval updated failed.');
        }
    }




    public function getFilesForApproval($groupId)
    {
        try {
            $files = $this->fileService->getPendingFiles($groupId);
            return $this->successResponse($files, 'Files for approval fetched successfully.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 'Files for approval fetch failed.');
        }
    }

    public function getApprovedFiles($groupId)
    {
        $userId = Auth::user()->id;
        $files = $this->fileService->getApprovedFiles($groupId);
        Log::info($files);
        $groups = null;
        $group = $this->groupService->getGroupById($groupId);
        if ($group->owner_id == $userId) {
            $owner = true;
            $pendingFiles = $this->fileService->getPendingFiles($groupId);
        } else {
            $owner = false;
            $pendingFiles = null;
        }

        $memberIds = $group->members->pluck('id'); // Retrieve member IDs
        $users = User::whereNotIn('id', $memberIds)->get();
                $status = 'files';
        return view('home', compact('files', 'groups', 'groupId', 'users', 'group', 'status', 'pendingFiles', 'owner'));
    }

    public function openFile($fileId)
    {
        try {
            $file = File::find($fileId);
            if (!$file) {
                throw new Exception('File not found');
            }
            $filePath = storage_path("app/private/" . $file->path); // Ensure full path
            if (!file_exists($filePath)) {
                throw new Exception('File does not exist');
            }
            if ($file->status == 'free') {

                // File is free, open it in read-only mode
                $command = "open -a 'Google Chrome' " . escapeshellarg($filePath);
            } else {
                $checkedInBy = $file->checked_in_by; // Assuming a 'checked_in_by' field exists in the file model
                $currentUser = Auth::id();
                if ($checkedInBy == $currentUser) {
                    $command = escapeshellcmd("open -a TextEdit $filePath");
                } else {
                    $command = null;
                }
            }

            shell_exec($command);
            return redirect()->back();
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 'File not found');
        }
    }

    public function openBackup(Request $request)
    {
        $path = $request->input('path');
        Log::info($path);
        $command = escapeshellcmd("open -a TextEdit $path");
        // Execute the command
        shell_exec($command);
        return redirect()->back();
    }

    public function downloadFile($fileId)
    {
        $file = File::find($fileId);
        if ($file->status == 'free') {
            return response()->download(storage_path("app/private/" . $file->path), $file->name);
        } else {
            return $this->errorResponse('File not found', 404);
        }
    }

    public function restoreBackup(Request $request)
    {
        $backupPath = $request->input('backup_path');
        $filePath = storage_path('app/private/'.$request->input('file_path'));
        Log::info($backupPath);
        Log::info($filePath);
        copy($backupPath, $filePath);
        return $this->successResponse('Backup restored successfully');
    }

    public function export($fileId, Request $request)
    {
        // Retrieve audit trails related to the file
        $audit = AuditTrail::where('file_id', $fileId)->get();



        $file_name = $request->input('file_name');
        // Load a view and pass the audit data
        $pdf = Pdf::loadView('pdf_report', ['audit' => $audit, 'file_name' => $file_name]);

        // Generate a meaningful file name
        $fileName = 'audit_report_file_' . $fileId . '.pdf';

        // Download the PDF
        return $pdf->download($fileName);
    }


}


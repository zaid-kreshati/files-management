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
            $userId=Auth::user()->id;
            $group=$this->groupService->getGroupById($groupId);
            $owner_id=$group->owner_id;

            $fileName = $data['file']->getClientOriginalName();
            $path = $data['file']->storeAs('group_files',  $fileName);
            $file=$this->fileService->uploadFileWithPendingApproval($groupId, $fileName, $path);
            if($owner_id==$userId){
                $this->fileService->approveFile($file->id, 'approved');
                $file->approval_status='approved';
            }
            return $this->successResponse($file, 'File uploaded successfully.');

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
            $file=$this->fileService->approveFile($fileId, $data['approval_status']);
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
        $userId=Auth::user()->id;
        $files = $this->fileService->getApprovedFiles($groupId);
        Log::info($files);
        $groups=null;
        $group = $this->groupService->getGroupById($groupId);
        if($group->owner_id==$userId){
            $owner=true;
            $pendingFiles=$this->fileService->getPendingFiles($groupId);
        }else{
            $owner=false;
            $pendingFiles=null;
        }
        $users=User::all()->except($userId);
        $status='files';

        return view('home', compact('files', 'groups', 'groupId', 'users', 'group','status','pendingFiles','owner'));
    }

    public function openFile($fileId)
    {
        $file = File::find($fileId);
        if (!$file) {
            return response()->json(['error' => 'File not found'], 404);
        }
        $filePath = storage_path("app/private/" . $file->path); // Ensure full path
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File does not exist'], 404);
        }
        $command = escapeshellcmd("open -a TextEdit $filePath");
        // Execute the command
        shell_exec($command);
        return redirect()->back();
    }

    public function downloadFile($fileId)
    {
        $file = File::find($fileId);
        return response()->download(storage_path("app/private/" . $file->path), $file->name);
    }


}

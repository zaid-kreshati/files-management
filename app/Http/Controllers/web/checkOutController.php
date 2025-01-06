<?php

namespace App\Http\Controllers\web;

use App\Services\CheckOutService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\FileService;
use App\Traits\JsonResponseTrait;

class checkOutController extends Controller
{
    protected CheckOutService $checkOutService;
    protected FileService $fileService;
    use JsonResponseTrait;
    public function __construct(CheckOutService $checkOutService,FileService $fileService)
    {
        $this->checkOutService = $checkOutService;
        $this->fileService = $fileService;
    }
    public function replaceFile(Request $request, int $groupId)
    {

        Log::info('Request data:', $request->all());
        // $data = $request->validate([
        //     'file' => 'required|file|mimes:txt|max:2048', // Only text files are allowed
        // ]);

        try {
            foreach ($request->files_names as $fileName) {
                // Check if the file exists
                if (Storage::disk('private')->exists("group_files/$fileName")) {
                    // Get the file contents
                    Log::info('fileNamex');
                    Log::info( $fileName);
                    // Pass the file object to the service
                    $result = $this->checkOutService->uploadAndReplaceFileInGroup($groupId, $fileName);
                } else {
                    return $this->errorResponse('File not found', 404);
                }
            }
            if ($result['success']) {
                $files = $this->fileService->getApprovedFiles($groupId);
                $html = view('partials.files_section', ['files' => $files,'groupId'=>$groupId])->render();
                return $this->successResponse($html, 'Files checked out successfully', 200);
            }
            return $this->errorResponse('Error checking out files', 400);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}

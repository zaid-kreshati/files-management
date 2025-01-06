<?php

namespace App\Http\Controllers\web;

use App\Services\CheckFileService;
use Illuminate\Http\Request;
use Symfony\Component\Mime\Header\Headers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Traits\JsonResponseTrait;
use App\Models\File;
use App\Models\Group;
use App\Services\FileService;
class ChecksFilesController extends Controller
{
    use JsonResponseTrait;

    protected $checkFileService;
    protected $fileService;

    public function __construct(CheckFileService $checkFileService,FileService $fileService)
    {
        $this->checkFileService = $checkFileService;
        $this->fileService = $fileService;
    }

    public function checkInFiles(Request $request)
    {
        $data = $request->validate([
            'file_ids' => 'required', // Ensure file_ids is an array
            'description' => 'string|max:255|nullable', // Make description nullable if not provided
        ]);
        Log::info("locust");

        // If file_ids is not an array, wrap it in an array
        $fileIds = isset($data['file_ids']) && !is_array($data['file_ids'])
        ? [$data['file_ids']]
        : $data['file_ids'];

        //dd($fileIds);
        $result = $this->checkFileService->checkInFiles($fileIds, $data['description'] = 'description');
        Log::info("checkInFiles");
        Log::info($result);
        $groupId=$request->group_id;
        Log::info("group id");
        Log::info($groupId);

        $files = $this->fileService->getApprovedFiles($groupId);
        $html = view('partials.files_section', ['files' => $files,'groupId'=>$groupId])->render();
        Log::info($html);
        if ($result['success']) {
            // Check if a path is provided in the response and exists
            if (isset($result['path']) && file_exists($result['path'])) {
                return $this->successResponse($html, 'Files checked in successfully', 200);
                //response()->download($result['path'], basename($result['path']));
            }

            return $this->successResponse($html, 'Files checked in successfully', 200);
        }

        return $this->errorResponse('Error checking in files', 400);
    }
}

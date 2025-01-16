<?php

namespace App\Http\Controllers\web;

use App\Services\CheckOutService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\FileService;
use App\Traits\JsonResponseTrait;
use App\Http\Requests\CheckOutRequest;
class checkOutController extends Controller
{
    protected $checkOutService,$fileService;
    use JsonResponseTrait;

    public function __construct(CheckOutService $checkOutService,FileService $fileService)
    {
        $this->checkOutService = $checkOutService;
        $this->fileService = $fileService;
    }
    public function checkOut(CheckOutRequest $request, int $groupId)
    {
        try {
            foreach ($request->files_names as $fileName) {
                if (Storage::disk('private')->exists("group_files/$fileName")) {
                    $result = $this->checkOutService->checkOutFileInGroup($groupId, $fileName);
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

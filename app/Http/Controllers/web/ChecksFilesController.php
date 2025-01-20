<?php

namespace App\Http\Controllers\web;

use App\Services\CheckFileService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\JsonResponseTrait;
use App\Services\FileService;

class ChecksFilesController extends Controller
{
    use JsonResponseTrait;

    protected CheckFileService $checkFileService;
    protected FileService $fileService;

    public function __construct(CheckFileService $checkFileService, FileService $fileService)
    {
        $this->checkFileService = $checkFileService;
        $this->fileService = $fileService;
    }

    public function checkInFiles(Request $request)
    {
        try {
            $data = $this->checkFileService->validateCheckInRequest($request);

            $fileIds = $this->checkFileService->normalizeFileIds($data['file_ids']);

            $result = $this->checkFileService->checkInFiles($fileIds, $data['description'] ?? 'description');

            $html = $this->renderFilesSection($request->group_id);

            return $this->handleCheckInResponse($result, $html);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * @throws Exception
     */
    private function renderFilesSection(int $groupId): string
    {
        $files = $this->fileService->getApprovedFiles($groupId);
        return view('partials.files_section', ['files' => $files, 'groupId' => $groupId])->render();
    }

    private function handleCheckInResponse(array $result, string $html): JsonResponse
    {
        if ($result['success']) {
            // Handle optional download path
            return $this->successResponse($html, 'Files checked in successfully', 200);
        }
        return $this->errorResponse('Error checking in files', 400);
    }
}

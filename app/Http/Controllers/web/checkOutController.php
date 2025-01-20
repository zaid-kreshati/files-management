<?php

namespace App\Http\Controllers\web;

use App\Services\CheckOutService;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\FileService;
use App\Traits\JsonResponseTrait;
use App\Http\Requests\CheckOutRequest;

class checkOutController extends Controller
{
    protected FileService $fileService;
    protected CheckOutService $checkOutService;
    use JsonResponseTrait;

    public function __construct(CheckOutService $checkOutService, FileService $fileService)
    {
        $this->checkOutService = $checkOutService;
        $this->fileService = $fileService;
    }

    public function checkOut(CheckOutRequest $request, int $groupId): JsonResponse
    {
        try {
            $this->fileService->validateFilesExist($request->files_names);

            $this->processFileCheckOut($request->files_names, $groupId);

            $html = $this->renderFilesSection($groupId);

            return $this->successResponse($html, 'Files checked out successfully');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Process check-out for each file.
     * @throws Exception
     */
    private function processFileCheckOut(array $fileNames, int $groupId): void
    {
        foreach ($fileNames as $fileName) {
            $this->checkOutService->checkOutFileInGroup($groupId, $fileName);
        }
    }

    /**
     * Render the files section for the given group.
     * @throws Exception
     */
    private function renderFilesSection(int $groupId): string
    {
        $files = $this->fileService->getApprovedFiles($groupId);
        return view('partials.files_section', ['files' => $files, 'groupId' => $groupId])->render();
    }
}

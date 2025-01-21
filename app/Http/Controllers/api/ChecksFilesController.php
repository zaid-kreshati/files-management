<?php

namespace App\Http\Controllers\api;

use App\Services\CheckFileService;
use Illuminate\Http\Request;
use Symfony\Component\Mime\Header\Headers;
use App\Http\Controllers\Controller;


class ChecksFilesController extends Controller
{

    protected $checkFileService;

    public function __construct(CheckFileService $checkFileService)
    {
        $this->checkFileService = $checkFileService;
    }

    public function checkInFiles(Request $request)
    {
        $data = $request->validate([
            'file_ids' => 'required', // Ensure file_ids is an array
            'description' => 'string|max:255|nullable', // Make description nullable if not provided
        ]);

        // If file_ids is not an array, wrap it in an array
        $fileIds = isset($data['file_ids']) && !is_array($data['file_ids'])
        ? [$data['file_ids']]
        : $data['file_ids'];


        $result = $this->checkFileService->checkInFiles($fileIds, $data['description'] = 'description');

        if ($result['success']) {
            // Check if a path is provided in the response and exists
            if (isset($result['path']) && file_exists($result['path'])) {
                return
                response()->download($result['path'], basename($result['path']));
            }

            return response()->json($result, 200);
        }

        return response()->json($result, 400);
    }
}

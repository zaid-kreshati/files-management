<?php

namespace App\Http\Controllers\api;

use App\Services\CheckOutService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class checkOutController extends Controller
{
    protected CheckOutService $checkOutService;

    public function __construct(CheckOutService $checkOutService)
    {
        $this->checkOutService = $checkOutService;
    }
    public function replaceFile(Request $request, int $groupId)
    {
        $data = $request->validate([
            'uploaded_file' => 'required|file|mimes:txt|max:2048', // Only text files are allowed
        ]);

        try {

            $uploadedFile = $request->file('uploaded_file');

            // Pass the file object to the service
            $result = $this->checkOutService->uploadAndReplaceFileInGroup($groupId, $uploadedFile);

            if ($result['success']) {
                return response()->json([
                    'message' => $result['message'],
                    'differences' => $result['differences']
                ], 200);
            }
            $files=
            $html=view('partials.files', ['files' => $result['files'],'groupId'=>$groupId])->render();

            return response()->json(['message' => $result['message'],'data'=>$html], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}

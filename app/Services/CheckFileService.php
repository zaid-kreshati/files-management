<?php

namespace App\Services;

use App\Jobs\CheckInFileJob;
use App\Repositories\CheckFileRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckFileService
{
    protected $checkFileRepository;

    public function __construct(CheckFileRepository $checkFileRepository)
    {
        $this->checkFileRepository = $checkFileRepository;
    }

    public function checkInFiles(array $fileIds, string $description = 'description'): array
    {
        DB::beginTransaction();

        try {
            foreach ($fileIds as $fileId) {
                CheckInFileJob::dispatch($fileId, $description ,  Auth::id());
            }
            DB::commit();

            return [
                'success' => true,
                'message' => 'All files have been queued for check-in.',
            ];
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


     public function validateCheckInRequest(Request $request): array
    {
        return $request->validate([
            'file_ids' => 'required',
            'description' => 'string|max:255|nullable',
        ]);
    }


    /**
     * Normalize file IDs into an array.
     */
    public function normalizeFileIds($fileIds): array
    {
        return is_array($fileIds) ? $fileIds : [$fileIds];
    }


}

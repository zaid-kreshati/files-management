<?php

namespace App\Services;

use App\Models\File;
use App\Repositories\CheckFileRepository;
use App\Repositories\CheckOutRepository;
use App\Repositories\GroupRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;


class CheckOutService
{

    protected CheckOutRepository $checkOutRepository;
    protected CheckFileRepository $checkFileRepository;
    protected GroupRepository $groupRepository;

    public function __construct(CheckOutRepository $checkOutRepository, CheckFileRepository $checkFileRepository, GroupRepository $groupRepository)
    {
        $this->checkOutRepository = $checkOutRepository;
        $this->checkFileRepository = $checkFileRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @throws Exception
     */
    public function checkOutFileInGroup(int $groupId, string $fileName): array
    {
        DB::beginTransaction();
        try {
            // Validate Group and File Existence
            $existingFile = $this->validateGroupAndFile($groupId, $fileName);

            // Perform Check-Out Process
            $this->performCheckOut($groupId, $fileName);

            // Compare Files and Backup
            $differences = $this->handleFileComparisonAndBackup($fileName, $existingFile);

            DB::commit();

            return $this->generateSuccessResponse($differences);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Validate the group and file existence.
     * @throws Exception
     */
    private function validateGroupAndFile(int $groupId, string $fileName): ?File
    {
        $this->groupRepository->validateGroupExists($groupId);
        $existingFile = $this->checkOutRepository->findFileInGroupByName($groupId, $fileName);
        $this->checkFileRepository->ValidateCheckinOwner($existingFile->id, $fileName);
        return $existingFile;
    }

    /**
     * Perform the check-out process.
     * @throws Exception
     */
    private function performCheckOut(int $groupId, string $fileName): void
    {
        $existingFile = $this->checkOutRepository->findFileInGroupByName($groupId, $fileName);
        $this->checkOutRepository->checkOutFile($existingFile->id, 'checkout');
        $existingFile->update(['status' => 'free']);
    }

    /**
     * Handle file comparison and backup.
     */
    private function handleFileComparisonAndBackup(string $fileName, $existingFile): string
    {
        $path = Storage::disk('private')->path("group_files/$fileName");
        $uploadedFileContent = file_get_contents($path);
        $existingFileContent = Storage::get($existingFile->path);
        $differences = $this->compareFiles11($existingFileContent, $uploadedFileContent);

        // Backup original file
        $this->createFileBackup($fileName, $existingFile);

        return $differences;
    }

    /**
     * Create a backup of the file.
     */
    private function createFileBackup(string $fileName, $existingFile): void
    {
        $backupPath = storage_path('backups/' . $fileName . '_' . now()->format('Y-m-d-H-i-s') . '.' . pathinfo($existingFile->path, PATHINFO_EXTENSION));
        $oldPath = storage_path('app/private/' . $existingFile->path);
        copy($oldPath, $backupPath);

        $this->checkFileRepository->createBackup($existingFile->id, $backupPath);
    }

    /**
     * Generate a success response.
     */
    private function generateSuccessResponse(string $differences): array
    {
        return [
            'success' => true,
            'message' => "File successfully replaced.",
            'differences' => $differences,
        ];
    }

    private function compareFiles11(string $existingContent, string $uploadedContent): string
    {
        $differ = new Differ(new UnifiedDiffOutputBuilder);
        // Generate the diff as a string
        return $differ->diff($existingContent, $uploadedContent);
    }
}

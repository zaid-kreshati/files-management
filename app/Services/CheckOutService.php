<?php

namespace App\Services;

use App\Events\FileTraced;
use App\Repositories\CheckFileRepository;
use App\Repositories\CheckOutRepository;
use App\Repositories\GroupRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

class CheckOutService
{

    protected CheckOutRepository $checkOutRepository;
    protected CheckFileRepository $chekFileRepository;
    protected GroupRepository $groupRepository;

    public function __construct(CheckOutRepository $checkOutRepository, CheckFileRepository $chekFileRepository, GroupRepository $groupRepository)
    {
        $this->checkOutRepository = $checkOutRepository;
        $this->chekFileRepository = $chekFileRepository;
        $this->groupRepository = $groupRepository;
    }

    public function uploadAndReplaceFileInGroup(int $groupId, $uploadedFile)
    {
        $group = $this->groupRepository->findById($groupId);
        if (!$group) {
            throw new Exception("Group not found.");
        }

        $fileName = $uploadedFile->getClientOriginalName();

        // Check if the file exists in the group
        $existingFile = $this->checkOutRepository->findFileInGroupByName($groupId, $fileName);
        if (!$existingFile) {
            throw new Exception("The specified file does not exist in this group.");
        }

        // Register checkouts 
        $this->checkOutRepository->checkOutFile($existingFile->id, Auth::id(), 'checkout');

        // Read contents of the uploaded file
        $uploadedFileContent = file_get_contents($uploadedFile->getRealPath());

        // Read contents of the existing file
        $existingFileContent = Storage::get($existingFile->path);

        // Compare the files
        $differences = $this->compareFiles11($existingFileContent, $uploadedFileContent);

        $backupPath = "backups/{$fileName}_" . now()->timestamp;
        Storage::copy($existingFile->path, $backupPath);
        $this->chekFileRepository->createBackup(
            $existingFile->id,
            $backupPath,
        );

        // Replace the file (overwrite the existing path)
        $newPath = $uploadedFile->storeAs('files', $fileName);
        $existingFile->update(['path' => $newPath, 'status' => 'free']);


        // Log changes in the audit trail
        $this->chekFileRepository->createAuditTrail([
            'file_id' => $existingFile->id,
            'user_id' => Auth::id(),
            'change_type' => 'modified',
            'description' => "File replaced by user: " . Auth::user()->name . " \n " . $differences,
        ]);

        // ! Tracing -----

        // Dispatch the tracing event with before & after
        event(new FileTraced(
            $existingFile,
            Auth::user(),
            'edit',
            $differences,
            $existingFileContent,       // Before
            $uploadedFileContent       // After
        ));


        // !--------------

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
        $diff = $differ->diff($existingContent, $uploadedContent);

        return $diff;
    }
}

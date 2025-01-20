<?php

namespace App\Services;

use App\Repositories\CheckFileRepository;
use App\Repositories\CheckOutRepository;
use App\Repositories\GroupRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Illuminate\Support\Facades\Log;

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

    public function checkOutFileInGroup(int $groupId, string $fileName)
    {
        DB::beginTransaction();
        try {
        $group = $this->groupRepository->findById($groupId);
        if (!$group) {
            throw new Exception("Group not found.");
        }

        // Check if the file exists in the group
        $existingFile = $this->checkOutRepository->findFileInGroupByName($groupId, $fileName);
        if (!$existingFile) {
            throw new Exception("The file $fileName does not exist in this group.");
        }

        if($existingFile->status == 'free'){
            throw new Exception("The file $fileName is already checked out.");
        }

        $checkInUser = $this->chekFileRepository->findUserByFileId($existingFile->id);
        $user_id = Auth::id();
        if($checkInUser->id != $user_id){
            throw new Exception("You cannot check out the file $fileName because you are not the one who checked it in.($checkInUser->name)");
        }

        // Register checkouts
        $this->checkOutRepository->checkOutFile($existingFile->id  , 'checkout');
        $path = Storage::disk('private')->path("group_files/$fileName");


        // Read contents of the uploaded file
        $uploadedFileContent = file_get_contents($path);


        // Read contents of the existing file
        //$existingFileContent = Storage::get($existingFile->path);
        $lastVersion = $this->checkOutRepository->getLastVersion($existingFile->id);
        $existingFileContent = file_get_contents($lastVersion->backup_path);


        // Compare the files
        $differences = $this->compareFiles11($existingFileContent, $uploadedFileContent);

        $backupPath = storage_path('backups/'.$fileName.'_'.now()->format('Y-m-d-H-i-s').'.'.pathinfo($existingFile->path, PATHINFO_EXTENSION));
        $oldPath = storage_path('app/private/'.$existingFile->path);

        //backup the file
         //copy($oldPath, $backupPath);
        $this->chekFileRepository->createBackup(
            $existingFile->id,
            $backupPath,
        );

        $existingFile->update([ 'status' => 'free']);


        // Log changes in the audit trail
        $this->chekFileRepository->createAuditTrail([
            'file_id' => $existingFile->id,
            'user_id' => Auth::id(),
            'change_type' => 'check_out',
            'description' => "File replaced by user: " . Auth::user()->name . " \n " . $differences,
        ]);
        DB::commit();


        return [
            'success' => true,
            'message' => "File successfully replaced.",
            'differences' => $differences,
        ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;

            // return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function compareFiles11(string $existingContent, string $uploadedContent): string
    {
        Log::info('existingContent');
        Log::info($existingContent);
        Log::info('uploadedContent');
        Log::info($uploadedContent);

        $differ = new Differ(new UnifiedDiffOutputBuilder);

        // Generate the diff as a string
        $diff = $differ->diff($existingContent, $uploadedContent);

        return $diff;
    }
}

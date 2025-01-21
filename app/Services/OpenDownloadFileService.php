<?php

namespace App\Services;

use App\Models\AuditTrail;
use App\Models\File;
use App\Traits\JsonResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpenDownloadFileService
{

    use JsonResponseTrait;


    public function openFile($fileId)
    {
        try {
            $file = File::find($fileId);
            if (!$file) {
                throw new Exception('File not found');
            }
            $filePath = storage_path("app/private/" . $file->path); // Ensure full path
            if (!file_exists($filePath)) {
                throw new Exception('File does not exist');
            }
            if ($file->status == 'free') {

                // File is free, open it in read-only mode
                $command = "open -a 'Google Chrome' " . escapeshellarg($filePath);
            } else {
                $checkedInBy = $file->checked_in_by; // Assuming a 'checked_in_by' field exists in the file model
                $currentUser = Auth::id();
                if ($checkedInBy == $currentUser) {
                    $command = escapeshellcmd("open -a TextEdit $filePath");
                } else {
                    $command = null;
                }
            }

            shell_exec($command);
            return redirect()->back();
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 'File not found');
        }
    }

    /**
     * Open a backup file in the default text editor.
     */
    public function openBackup(string $path): array
    {
        try {
            if (!file_exists($path)) {
                throw new Exception('Backup file not found');
            }

            $command = "open -a TextEdit " . escapeshellarg($path);
            shell_exec($command);

            return ['success' => true, 'message' => 'Backup file opened successfully.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Download a file.
     */
    public function downloadFile(int $fileId): \Symfony\Component\HttpFoundation\BinaryFileResponse|array
    {
        try {
            $file = File::findOrFail($fileId);

            if ($file->status === 'free') {
                $filePath = storage_path("app/private/" . $file->path);

                if (!file_exists($filePath)) {
                    throw new Exception('File not found.');
                }

                return response()->download($filePath, $file->name);
            }

            throw new Exception('File is currently not available for download.');
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Restore a file from its backup.
     */
    public function restoreBackup(string $backupPath, string $filePath): array
    {
        try {
            if (!file_exists($backupPath)) {
                throw new Exception('Backup file does not exist.');
            }

            $targetPath = storage_path('app/private/' . $filePath);
            copy($backupPath, $targetPath);

            return ['success' => true, 'message' => 'Backup restored successfully.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    public function export($fileId, Request $request)
    {
        // Retrieve audit trails related to the file
        $audit = AuditTrail::where('file_id', $fileId)->get();


        $file_name = $request->input('file_name');
        // Load a view and pass the audit data
        $pdf = Pdf::loadView('pdf_report', ['audit' => $audit, 'file_name' => $file_name]);

        // Generate a meaningful file name
        $fileName = 'audit_report_file_' . $fileId . '.pdf';

        // Download the PDF
        return $pdf->download($fileName);
    }
}

<?php

namespace App\Http\Controllers\web;

use App\Services\InvitationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Traits\JsonResponseTrait;
use App\Models\Invitation;

use Illuminate\Support\Facades\Log;
use App\Services\FileService;

class InvitationController extends Controller
{
    protected InvitationService $invitationService;
    use JsonResponseTrait;
    protected $fileService;


    public function __construct(InvitationService $invitationService,FileService $fileService)

    {
        $this->invitationService = $invitationService;
        $this->fileService = $fileService;
    }

    public function sendInvitation(Request $request): JsonResponse
    {
        $data = $this->validateInvitationData($request);
        try {
            $this->invitationService->inviteUser($data['group_id'], $data['user_id']);
            $user = User::find($data['user_id']);

            return $this->successResponse($user->name, 'Invitation sent successfully to ');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 'some thing went wrong', 500);
        }
    }

    public function sendBulkInvitations($groupId): JsonResponse
    {
        try {
            $userIds = User::where('id', '!=', Auth::id())->pluck('id')->toArray();
            $this->invitationService->inviteAllUsers($groupId, $userIds);

            return $this->successResponse(null, 'Group created successfully', 201);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 'some thing went wrong', 500);
        }
    }

    public function respondToInvitation(Request $request, $invitationId): JsonResponse
    {
        $data = $request->validate([
            'response' => 'required|in:accepted,rejected',
        ]);

        try {
            $this->invitationService->respondToInvitation($invitationId, $data['response']);
            $message = $data['response'] === 'accepted'
                ? 'Invitation accepted and you have been added to the group.'
                : 'Invitation rejected.';
            $status = $request['response'];
            $invitation = Invitation::find($invitationId);
            $group = $invitation->group;

            $pendingFiles = $this->fileService->getPendingFiles($group->id);

            $response = [
                'status' => $status,
                'group' => $group,
                'pendingFiles' => $pendingFiles,
            ];


            return $this->successResponse($response, $message);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 'some thing went wrong', 400);
        }
    }

    public function getUserInvitations(): JsonResponse
    {
        try {
            $invitations = $this->invitationService->getUserInvitations(Auth::id());
            return response()->json(['success' => true, 'invitations' => $invitations]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function validateInvitationData(Request $request): array
    {
        return $request->validate([
            'group_id' => 'required|exists:groups,id',
            'user_id' => 'required|exists:users,id',
        ]);
    }
}

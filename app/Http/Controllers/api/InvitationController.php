<?php

namespace App\Http\Controllers\api;

use App\Services\InvitationService;
use Illuminate\Http\Request;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;


class InvitationController extends Controller
{
    protected $invitationService;

    public function __construct(InvitationService $invitationService)
    {
        $this->invitationService = $invitationService;
    }

    // Send invitation to a specific user
    public function sendInvitation($groupId , $user_id)
    {
        try {
            $success = $this->invitationService->inviteUser($groupId, $user_id);
            return response()->json(['success' => $success, 'message' => 'Invitation sent successfully']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        }
    }

    // Send invitations to all users
    public function sendBulkInvitations($groupId)
    {
        try {
            $userIds = User::where('id', '!=', Auth::id())->pluck('id')->toArray();
            $this->invitationService->inviteAllUsers($groupId, $userIds);

            return response()->json(['success' => true, 'message' => 'Bulk invitations sent successfully']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        }
    }


    public function respondToInvitation(Request $request, $invitationId)
    {
        $data = $request->validate([
            'response' => 'required|in:accepted,rejected',
        ]);

        try {
            $success = $this->invitationService->respondToInvitation($invitationId, $data['response']);
            $message = $data['response'] === 'accepted'
            ? 'Invitation accepted and you have been added to the group.'
            : 'Invitation rejected.';

            return response()->json(['success' => $success, 'message' => $message]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function getUserInvitations()
    {
        try {
            $userId = Auth::id();
            $invitations = $this->invitationService->getUserInvitations($userId);
            return response()->json(['success' => true, 'invitations' => $invitations]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }


}

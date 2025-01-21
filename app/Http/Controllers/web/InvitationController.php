<?php

namespace App\Http\Controllers\web;

use App\Services\InvitationService;
use Illuminate\Http\Request;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Traits\JsonResponseTrait;
use App\Models\Invitation;
use Illuminate\Support\Facades\Log;

class InvitationController extends Controller
{
    protected $invitationService;
    use JsonResponseTrait;


    public function __construct(InvitationService $invitationService)
    {
        $this->invitationService = $invitationService;
    }

    // Send invitation to a specific user
    public function sendInvitation(Request $request)
    {
        try {
            $data = $request->validate([
                'group_id' => 'required|exists:groups,id',
                'user_id' => 'required|exists:users,id',
            ]);
            $success = $this->invitationService->inviteUser($data['group_id'], $data['user_id']);
            $user=User::find($data['user_id']);
            $name=$user->name;
            return $this->successResponse($name, 'Invitation sent successfully to ');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 'some thing went wrong', 500);
        }
    }

    // Send invitations to all users
    public function sendBulkInvitations($groupId)
    {
        try {
            $userIds = User::where('id', '!=', Auth::id())->pluck('id')->toArray();
            $this->invitationService->inviteAllUsers($groupId, $userIds);

            return $this->successResponse(null, 'Group created successfully', 201);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 'some thing went wrong', 500);
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
            $status = $request['response'];
            $invitation = Invitation::find($invitationId);
            $group = $invitation->group;

            $response = [
                'status' => $status,
                'group' => $group,
            ];


            return $this->successResponse($response,$message);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 'some thing went wrong', 400);
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

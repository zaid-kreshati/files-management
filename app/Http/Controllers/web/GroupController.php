<?php

namespace App\Http\Controllers\web;

use App\Services\GroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Traits\JsonResponseTrait;
use App\Services\InvitationService;

class GroupController extends Controller
{
    use JsonResponseTrait;

    protected GroupService $groupService;
    protected InvitationService $invitationService;

    public function __construct(GroupService $groupService, InvitationService $invitationService)
    {
        $this->groupService = $groupService;
        $this->invitationService = $invitationService;
    }


    public function store(Request $request): JsonResponse
    {
        try {

            $data = $this->validateRequest($request);
            $group = $this->groupService->createGroup($data);
            $groups = $this->groupService->getAllWithPagination(1);
            $invitationRequests = $this->invitationService->getUserInvitations(Auth::id());
            $pagination = $groups->links('pagination::bootstrap-5')->render();

            $html = view('partials.groups', ['groups' => $groups, 'invitationRequests' => $invitationRequests])->render();
            $response = [
                'group' => $group,
                'html' => $html,
                'pagination' => $pagination
            ];

            return $this->successResponse($response, 'Group created successfully', 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while creating the group.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $group = $this->groupService->getGroupById($id);
            if ($group->owner_id == Auth::id()) {
                $success = $this->groupService->deleteGroup($id);
                return $this->successResponse(['success' => $success]);
            } else {
                $message = 'You are not the owner of this group';
                return $this->errorResponse($message, 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 403);
        }
    }

    public function index(): JsonResponse
    {
        $groups = $this->groupService->getAll();
        $html = view('partials.groups', ['groups' => $groups])->render();
        return $this->successResponse($html);
    }

    public function getGroupMembers($groupId): JsonResponse
    {
        try {
            $members = $this->groupService->getGroupMembers($groupId);
            return response()->json(['success' => true, 'Members' => $members]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function update(Request $request, $groupId): JsonResponse
    {
        $group = $this->groupService->getGroupById($groupId);
        if ($group->owner_id == Auth::id()) {
            $this->groupService->updateGroup($groupId, $request->all());
            return $this->successResponse(['success' => true]);
        } else {
            $message = 'You are not the owner of this group';
            return $this->errorResponse($message, 403);
        }
    }

    public function searchGroups(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $groups = $this->groupService->searchGroups($search);
        $invitationRequests = $this->invitationService->getUserInvitations(Auth::id());
        $html = view('partials.groups', ['groups' => $groups, 'invitationRequests' => $invitationRequests])->render();
        return $this->successResponse($html);
    }

    public function getAllGroups(): JsonResponse
    {
        $groups = $this->groupService->getAll();
        $html = view('partials.groups', ['groups' => $groups])->render();
        return $this->successResponse($html);
    }

    public function getAllGroupsWithPagination(Request $request): JsonResponse
    {
        $groups = $this->groupService->getAllWithPagination($request->page);
        $pagination = $groups->links('pagination::bootstrap-5')->render();
        $response = [
            'groups' => $groups,
            'pagination' => $pagination
        ];
        return $this->successResponse($response);
    }

    public function checkOwner($groupId): JsonResponse
    {
        $group = $this->groupService->getGroupById($groupId);
        if ($group->owner_id == Auth::user()) {
            return $this->successResponse(['success' => true]);
        } else {
            return $this->errorResponse('You are not the owner of this group2', 403);
        }
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
        ]);
    }
}

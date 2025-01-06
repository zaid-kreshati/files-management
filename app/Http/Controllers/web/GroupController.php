<?php

namespace App\Http\Controllers\web;

use App\Services\GroupService;
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
    protected $groupService;
    protected $invitationService;

    public function __construct(GroupService $groupService, InvitationService $invitationService)
    {
        $this->groupService = $groupService;
        $this->invitationService = $invitationService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $page=1;
        $userId=Auth::user()->id;
        $group = $this->groupService->createGroup($data);
        $groups = $this->groupService->getAllWithPagination($page);
        $invitationRequests = $this->invitationService->getUserInvitations($userId);
        Log::info("groups");
        Log::info($groups);
        $pagination=$groups->links('pagination::bootstrap-5')->render();

        $html = view('partials.groups', ['groups' => $groups,'invitationRequests'=>$invitationRequests])->render();
        $response=[
            'group'=>$group,
            'html'=>$html,
            'pagination'=>$pagination
        ];
        return $this->successResponse($response, 'Group created successfully', 201);
    }

    public function destroy($id)
    {
        try {
            $userId=Auth::user()->id;
            $group=$this->groupService->getGroupById($id);
            if($group->owner_id==$userId){
                $success = $this->groupService->deleteGroup($id);
                return $this->successResponse(['success' => $success]);
            }else{
                $message='You are not the owner of this group';
                return $this->errorResponse($message,403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(),403);
        }
    }


    public function index()
    {
        $groups = $this->groupService->getAll();
        $html = view('partials.groups', ['groups' => $groups])->render();
        return $this->successResponse($html);
    }

    public function getGroupMembers($groupId)
    {
        try {
            $members = $this->groupService->getGroupMembers($groupId);
            return response()->json(['success' => true, 'Members' => $members]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function update(Request $request, $groupId)
    {
        $userId=Auth::user()->id;
        $group=$this->groupService->getGroupById($groupId);
        if($group->owner_id==$userId){
            $this->groupService->updateGroup($groupId, $request->all());
            return $this->successResponse(['success' => true]);
        }else{
            $message='You are not the owner of this group';
            return $this->errorResponse($message,403);
        }
    }

    public function searchGroups(Request $request)
    {
        $userId=Auth::user()->id;
        $search = $request->input('search');
        $groups = $this->groupService->searchGroups($search);
        $invitationRequests = $this->invitationService->getUserInvitations($userId);
        $html = view('partials.groups', ['groups' => $groups,'invitationRequests'=>$invitationRequests])->render();
        return $this->successResponse($html);
    }

    public function getAllGroups()
    {
        $groups = $this->groupService->getAll();
        Log::info("getAllGroups");
        Log::info($groups);
        $html = view('partials.groups', ['groups' => $groups])->render();
        return $this->successResponse($html);
    }

    public function getAllGroupsWithPagination(Request $request)
    {
        $page = $request->page;
        $groups = $this->groupService->getAllWithPagination($page);
        $pagination=$groups->links('pagination::bootstrap-5')->render();
        $response=[
            'groups'=>$groups,
            'pagination'=>$pagination
        ];
        return $this->successResponse($response);
    }

    public function checkOwner($groupId)
    {
        $userId=Auth::user()->id;
        $group=$this->groupService->getGroupById($groupId);
        if($group->owner_id==$userId){
            return $this->successResponse(['success' => true]);
        }else{
            return $this->errorResponse('You are not the owner of this group2',403);
        }
    }
}

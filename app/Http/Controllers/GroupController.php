<?php

namespace App\Http\Controllers;

use App\Services\GroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
class GroupController extends Controller
{
    protected $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $group = $this->groupService->createGroup($data);
        return response()->json(['group' => $group], 201);
    }

    public function destroy($id)
    {
        try {
            $success = $this->groupService->deleteGroup($id);
            return response()->json(['success' => $success]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }

    public function show($id)
    {
        $group = $this->groupService->getGroupById($id);
        return response()->json(['group' => $group]);
    }

    public function index()
    {
        $groups = $this->groupService->getAllGroupsByUser(Auth::id());
        return response()->json(['groups' => $groups]);
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
}

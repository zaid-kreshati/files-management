<?php

namespace App\Services;

use App\Repositories\Interfaces\GroupRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class GroupService
{
    protected $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function createGroup(array $data): Group
    {
        $data['owner_id'] = Auth::id();
        return $this->groupRepository->create($data);
    }

    public function deleteGroup(int $id): bool
    {
        $group = $this->groupRepository->findById($id);

        if (!$group) {
            throw new \Exception("Group not found");
        }

        if ($group->owner_id !== Auth::id()) {
            throw new \Exception("Unauthorized action: you are not the owner of this group");
        }
        return $this->groupRepository->delete($id);
    }

    public function getGroupById(int $id): ?Group
    {
        return $this->groupRepository->findById($id);
    }

    public function getAllGroupsByUser(int $userId): array
    {
        return $this->groupRepository->getAllByUserId($userId);
    }


    public function getGroupMembers(int $groupId) 
    {

        $group = $this->groupRepository->findById($groupId);
        if (!$group) {
            throw new Exception("Group not found");
        }

        return $this->groupRepository->getAllMembers($group)->map(function ($member) {
            return $member->name;
        });
    }
}

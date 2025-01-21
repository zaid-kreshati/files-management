<?php

namespace App\Services;

use App\Repositories\Interfaces\GroupRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
        $userId = Auth::user()->id;
        $data['owner_id'] = $userId;
        $group = $this->groupRepository->create($data);
        $this->groupRepository->addMember($group->id, $userId);
        return $group;
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

    public function getAll(): Collection
    {
        return $this->groupRepository->getAll();
    }

    public function updateGroup(int $id, array $data): void
    {
        $this->groupRepository->update($id, $data);
    }

    public function searchGroups(string $search)
    {
        return $this->groupRepository->search($search);
    }

    public function getAllWithPagination(int $page): LengthAwarePaginator
    {
        return $this->groupRepository->getAllWithPagination($page);
    }

    public function getGroupById(int $id): Group
    {
        return $this->groupRepository->findById($id);
    }
    
}

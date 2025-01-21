<?php

namespace App\Repositories;


use App\Models\Group;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\UserGroup;
class GroupRepository implements GroupRepositoryInterface
{

    public function create(array $data): Group
    {
        return Group::create($data);
    }

    public function delete(int $id): bool
    {
        $group = Group::find($id);
        return $group ? $group->delete() : false;
    }

    public function findById(int $id): ?Group
    {
        return Group::find($id);
    }

    public function getAllByUserId(int $userId): array
    {
        $groups = Group::where('owner_id', $userId)->get();
        $groups = $groups->merge(UserGroup::where('user_id', $userId)->get()->toArray());
        return $groups;
    }

    public function getAllMembers($group): Collection
    {
        return $group->members;
    }

    public function getAll(): Collection
    {
        $page = 1;
        $user = Auth::user();
        $groupIds = UserGroup::where('user_id', $user->id)
        ->pluck('group_id'); // Retrieve only the group IDs

    // Fetch the actual groups and paginate them
    $groups = Group::whereIn('id', $groupIds)
        ->get();

        return $groups;
    }

    public function addMember(int $groupId, int $userId): void
    {
        $group = Group::find($groupId);
        $status = 'accepted';
        $group->members()->attach($userId, ['status' => $status]);
    }

    public function update(int $id, array $data): void
    {
        $group = Group::find($id);
        $group->name = $data['name'];
        $group->save();
        Log::info($group);
    }

    public function search(string $search): Collection
    {
        $user = Auth::user();
        $groupIds = UserGroup::where('user_id', $user->id)->pluck('group_id');
        $groups = Group::whereIn('id', $groupIds)->where('name', 'like', '%' . $search . '%') ->get();
        return $groups;
    }

    public function getAllWithPagination(int $page): LengthAwarePaginator
    {
        $user = Auth::user();
        $groupIds = UserGroup::where('user_id', $user->id)
        ->pluck('group_id'); // Retrieve only the group IDs

    // Fetch the actual groups and paginate them
    $groups = Group::whereIn('id', $groupIds)
        ->paginate(3, ['*'], 'page', $page);
        return $groups;
    }
}

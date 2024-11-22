<?php

namespace App\Repositories;


use App\Models\Group;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

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
        return Group::where('owner_id', $userId)->get()->toArray();
    }

    public function getAllMembers($group): Collection
    {
        return $group->members; 
    }
}
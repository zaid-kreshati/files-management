<?php

namespace App\Repositories\Interfaces;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
interface GroupRepositoryInterface
{
    public function create(array $data): Group;
    public function delete(int $id): bool;
    public function findById(int $id): ?Group;
    public function getAllByUserId(int $userId): array;
    public function getAllMembers($group): Collection;
    public function getAll(): Collection;
    public function getAllWithPagination(int $page): LengthAwarePaginator;
    public function addMember(int $groupId, int $userId): void;
    public function update(int $id, array $data): void;
    public function search(string $search): Collection;
    


}

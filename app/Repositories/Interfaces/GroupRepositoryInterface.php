<?php

namespace App\Repositories\Interfaces;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;

interface GroupRepositoryInterface
{
    public function create(array $data): Group;
    public function delete(int $id): bool;
    public function findById(int $id): ?Group;
    public function getAllByUserId(int $userId): array;
    public function getAllMembers($group): Collection;
    
}

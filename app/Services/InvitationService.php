<?php

namespace App\Services;

use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\InvitationRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Invitation;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class InvitationService
{
    protected $invitationRepository;
    protected $groupRepository;
    protected $invitation;

    public function __construct(
        InvitationRepository $invitationRepository,
        GroupRepositoryInterface $groupRepository,
        Invitation $invitation
    ) {
        $this->invitationRepository = $invitationRepository;
        $this->groupRepository = $groupRepository;
        $this->invitation = $invitation;
    }

    public function inviteUser(int $groupId, int $userId): bool
    {

        $existing = $this->invitation->where('group_id', $groupId)->where('receiver_id', $userId)->first();
        if ($existing) {
            throw new Exception("The invitaiton already exist");
        }
        $group = $this->groupRepository->findById($groupId);

        if (!$group) {
            throw new Exception("Group not found");
        }

        if ($group->owner_id !== Auth::id()) {
            throw new Exception("You are not the owner of this group");
        }

        return $this->invitationRepository->sendInvitation($groupId, $userId);
    }


    public function inviteAllUsers(int $groupId, array $userIds): bool
    {
        $group = $this->groupRepository->findById($groupId);
        
        if (!$group) {
            throw new Exception("Group not found");
        }

        if ($group->owner_id !== Auth::id()) {
            throw new Exception("You are not the owner of this group");
        }

        return $this->invitationRepository->sendBulkInvitations($groupId, $userIds);
    }


    public function respondToInvitation(int $invitationId, string $response): bool
    {

        $invitation = $this->invitationRepository->find($invitationId);

        if (!$invitation) {
            throw new Exception("Invitation not found");
        }

        $validResponses = ['accepted', 'rejected'];
        if (!in_array($response, $validResponses)) {
            throw new Exception("Invalid response");
        }

        $this->invitationRepository->updateInvitationStatus($invitation, $response);

        // Handle association if response is "accepted"
        if ($response === 'accepted') {
            $group = $invitation->group;
            if (!$group->members()->where('user_id', $invitation->receiver_id)->exists()) {
                $group->members()->attach($invitation->receiver_id ,['status' => 'accepted']);
            }
        }

        return true;
    }

    public function getUserInvitations(int $userId): Collection
    {
        return $this->invitationRepository->getUserInvitations($userId);
    }
}

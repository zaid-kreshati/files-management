<?php

namespace App\Repositories;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
class InvitationRepository
{
    protected $invitation;

    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function sendInvitation(int $groupId, int $userId): bool
    {
        return (bool) $this->invitation->create([
            'group_id' => $groupId,
            'receiver_id' => $userId,
            'sender_id'=> Auth::id(),
            'status' => 'pending',
        ]);
    }

    public function sendBulkInvitations(int $groupId, array $userIds): bool
    {
        foreach ($userIds as $userId) {
            $this->sendInvitation($groupId, $userId);
        }
        return true;
    }

    public function find(int $invitationId)
    {
        return $this->invitation->find($invitationId);
    }

    public function updateInvitationStatus($invitation, string $status): bool
    {
        $invitation->status = $status;
        return $invitation->save();
    }

    public function getUserInvitations(int $userId): Collection
    {
        return $this->invitation->where('receiver_id', $userId)->where('status', 'pending')->get();
    }
}

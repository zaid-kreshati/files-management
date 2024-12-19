<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\File;
use App\Models\User;

class FileTraced implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $file;
    public $user;
    public $action;
    public $changes;
    public $before;
    public $after;

    public function __construct(File $file, User $user, string $action, string $changes, string $before, string $after)
    {
        $this->file = $file;
        $this->user = $user;
        $this->action = $action;
        $this->changes = $changes;
        $this->before = $before;
        $this->after = $after;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}

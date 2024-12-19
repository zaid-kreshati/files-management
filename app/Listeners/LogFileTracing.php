<?php

namespace App\Listeners;

use App\Events\FileTraced;
use App\Models\Tracing;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogFileTracing implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FileTraced $event)
    {
        Tracing::create([
            'file_id' => $event->file->id,
            'user_id' => $event->user->id,
            'action' => $event->action,
            'changes' => $event->changes, // The diff between `before` and `after`
            'before' => $event->before,   // File content before the change
            'after' => $event->after,     // File content after the change
        ]);
    }
}

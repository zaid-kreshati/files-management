<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\File;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        File::where('status', 'checked_in')
            ->where('checked_in_until', '<', now())
            ->update([
                'status' => 'free',
                'checked_in_until' => null,
            ]);
    })->everyMinute(); // Runs every minute to unlock expired check-ins
}
}

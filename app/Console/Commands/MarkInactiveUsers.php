<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MarkInactiveUsers extends Command
{
    protected $signature   = 'kutip:mark-inactive';
    protected $description = 'Mark users inactive if they have not visited in 30 days';

    public function handle(): void
    {
        $count = User::where('is_active', true)
            ->where('last_active_at', '<', now()->subDays(30))
            ->update(['is_active' => false]);

        $this->info("Marked {$count} user(s) as inactive.");
    }
}

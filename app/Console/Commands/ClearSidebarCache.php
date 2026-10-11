<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearSidebarCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sidebar:clear-cache 
                            {--user= : Clear cache for a specific user ID}
                            {--all : Clear all sidebar caches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear sidebar menu cache for users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = $this->option('user');
        $clearAll = $this->option('all');

        if ($userId) {
            $cacheKey = 'sidebar_menu_' . $userId;
            Cache::forget($cacheKey);
            $this->info("Cleared sidebar cache for user ID: {$userId}");
            return Command::SUCCESS;
        }

        if ($clearAll) {
            // Note: This flushes ALL cache, not just sidebar
            // For production, use tags or separate cache store
            $this->warn('This will clear ALL application cache, not just sidebar.');
            
            if ($this->confirm('Are you sure you want to clear all cache?', false)) {
                Cache::flush();
                $this->info('All application cache has been cleared.');
                return Command::SUCCESS;
            }
            
            $this->info('Operation cancelled.');
            return Command::FAILURE;
        }

        $this->error('Please specify either --user=<id> or --all flag.');
        $this->line('');
        $this->info('Usage:');
        $this->line('  php artisan sidebar:clear-cache --user=1');
        $this->line('  php artisan sidebar:clear-cache --all');
        
        return Command::FAILURE;
    }
}
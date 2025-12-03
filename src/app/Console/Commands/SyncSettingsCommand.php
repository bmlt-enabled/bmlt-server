<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class SyncSettingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:sync 
                            {--force : Overwrite existing settings}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync settings from environment variables to database';

    /**
     * Execute the console command.
     * Syncs settings from environment variables (and legacy config file) to database.
     *
     * @return int
     */
    public function handle()
    {
        $force = $this->option('force');
        
        $results = Setting::syncFromEnvironment($force);
        
        foreach ($results['synced'] as $key => $value) {
            $displayValue = is_array($value) ? json_encode($value) : (is_bool($value) ? ($value ? 'true' : 'false') : $value);
            $this->info("{$results['actions'][$key]} {$key} = {$displayValue}");
        }
        
        foreach ($results['skipped'] as $key => $value) {
            $this->info("Skipped {$key} (already exists, use --force to overwrite)");
        }
        
        $this->newLine();
        $this->info("Synced {$results['synced_count']} setting(s), skipped {$results['skipped_count']}");
        
        return Command::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DeleteOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-old-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes logs from the database based on the days setting in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Assume you have a 'settings' table with a 'key' and 'value' columns
        // For example, a row with key='log_delete_days' and value='3'
        $daysSetting = DB::table('gpt_keys')->first();
        $days = $daysSetting ? (int) $daysSetting->log_delete_days : 1; // Default to 1 day if not set

        // Calculate the date N days ago
        $date = Carbon::today()->subDays($days);

        // Delete logs older than N days
        DB::table('logs')->whereDate('created_at', '<', $date)->delete();

        $this->info("Logs older than {$days} day(s) have been deleted successfully.");
    }
}

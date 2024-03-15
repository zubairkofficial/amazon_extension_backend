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
    protected $description = 'Deletes logs from the database that are one day old';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = Carbon::yesterday();
        DB::table('logs')->whereDate('created_at', '<', $date)->delete();

        $this->info('Old logs have been deleted successfully.');
    }
}

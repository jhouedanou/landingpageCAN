<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class CleanOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clean {--days=7 : Number of days to keep logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old log files older than specified days';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $logsPath = storage_path('logs');

        if (!File::exists($logsPath)) {
            $this->error('Logs directory does not exist.');
            return Command::FAILURE;
        }

        $files = File::files($logsPath);
        $deletedCount = 0;
        $freedSpace = 0;

        $cutoffDate = Carbon::now()->subDays($days);

        foreach ($files as $file) {
            $fileTime = Carbon::createFromTimestamp(File::lastModified($file));

            if ($fileTime->lt($cutoffDate)) {
                $size = File::size($file);
                File::delete($file);
                $deletedCount++;
                $freedSpace += $size;

                $this->info("Deleted: {$file->getFilename()} (" . $this->formatBytes($size) . ")");
            }
        }

        if ($deletedCount > 0) {
            $this->info("\nCleaned {$deletedCount} log file(s).");
            $this->info("Freed up " . $this->formatBytes($freedSpace) . " of disk space.");
        } else {
            $this->info('No old log files to clean.');
        }

        return Command::SUCCESS;
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' KB';
        } elseif ($bytes < 1073741824) {
            return round($bytes / 1048576, 2) . ' MB';
        }

        return round($bytes / 1073741824, 2) . ' GB';
    }
}

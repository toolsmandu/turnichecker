<?php

namespace App\Console\Commands;

use App\Models\Submission;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupSubmissionFiles extends Command
{
    protected $signature = 'submissions:cleanup';

    protected $description = 'Delete files for completed submissions older than 72 hours';

    public function handle(): int
    {
        $cutoff = CarbonImmutable::now()->subHours(72);

        Submission::where('status', 'completed')
            ->where('updated_at', '<=', $cutoff)
            ->lazyById()
            ->each(function (Submission $submission) {
                $this->deleteFile($submission->file_path);
                $this->deleteFile($submission->similarity_report_path);
                $this->deleteFile($submission->ai_report_path);
                $submission->delete();
            });

        return self::SUCCESS;
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}

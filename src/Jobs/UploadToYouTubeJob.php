<?php

namespace Eduka\Services\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Nova\Notifications\NovaNotification;

class UploadToYouTubeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $episodeId;

    public $courseId;

    public $studentId;

    public function __construct(int $episodeId, int $courseId, int $studentId)
    {
        $this->episodeId = $episodeId;
        $this->courseId = $courseId;
        $this->studentId = $studentId;
    }

    public function handle(): void
    {
        try {
            //
        } catch (Exception $e) {
            $message = 'Upload to YouTube error: '.$e->getMessage().' on file '.$e->getFile().' on line '.$e->getLine();
            User::firstWhere('id', $this->studentId)->notify(
                NovaNotification::make()
                    ->message($message)
                    ->icon('download')
                    ->type('error')
            );
        }
    }
}

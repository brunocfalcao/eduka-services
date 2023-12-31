<?php

namespace Eduka\Services\Jobs;

use Eduka\Cube\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Nova\Notifications\NovaNotification;

class UploadToYouTubeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $videoId;

    public $courseId;

    public $userId;

    public function __construct(int $videoId, int $courseId, int $userId)
    {
        $this->videoId = $videoId;
        $this->courseId = $courseId;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        try {
            //
        } catch (Exception $e) {
            $message = 'Upload to YouTube error: '.$e->getMessage().' on file '.$e->getFile().' on line '.$e->getLine();
            User::firstWhere('id', $this->userId)->notify(
                NovaNotification::make()
                ->message($message)
                ->icon('download')
                ->type('error')
            );
        }
    }
}

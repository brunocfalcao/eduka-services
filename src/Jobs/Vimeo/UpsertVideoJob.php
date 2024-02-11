<?php

namespace Eduka\Services\Jobs\Vimeo;

use Eduka\Cube\Models\User;
use Eduka\Cube\Models\Video;
use Eduka\Services\External\Vimeo\VimeoClient;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Nova\Notifications\NovaNotification;

class UpsertVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video;

    public function __construct(int $videoId)
    {
        $this->video = Video::firstWhere('id', $videoId);
    }

    public function handle(): void
    {
        try {
            // Update Vimeo video name.
            $vimeoClient = new VimeoClient();

            $vimeoClient->updateVideoDetails(
                $this->video->vimeo_id,
                ['name' => $this->video->name]
            );
        } catch (Exception $e) {
            $message = 'Name change Vimeo error: '.$e->getMessage().' on file '.$e->getFile().' on line '.$e->getLine();
            User::firstWhere('id', $this->video->createdBy)->notify(
                NovaNotification::make()
                    ->message($message)
                    ->icon('download')
                    ->type('error')
            );
        }
    }
}

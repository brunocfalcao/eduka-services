<?php

namespace Eduka\Services\Jobs\Vimeo;

use Eduka\Cube\Models\Video;
use Eduka\Services\External\Vimeo\VimeoClient;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Nova\Notifications\NovaNotification;

class UplsertVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    public function handle(): void
    {
        try {
            $data = VimeoClient::upload($filename, $video->getVimeoMetadata([
                'folder_uri' => $video->getUploadVimeoFolderURI(),
            ]));
        } catch (Exception $e) {
            $message = 'Upload to Vimeo error: '.$e->getMessage().' on file '.$e->getFile().' on line '.$e->getLine();
            $this->video->course->admin->notify(
                NovaNotification::make()
                    ->message($message)
                    ->icon('download')
                    ->type('error')
            );
        }
    }
}

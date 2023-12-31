<?php

namespace Eduka\Services\Jobs;

use Eduka\Cube\Actions\Video\SaveVimeoId;
use Eduka\Cube\Models\User;
use Eduka\Cube\Shared\Processor\VimeoUploaderValidator;
use Eduka\Services\External\Vimeo\VimeoClient;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Nova\Notifications\NovaNotification;

class UploadToVimeoJob implements ShouldQueue
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
            $validator = VimeoUploaderValidator::findUsingVideoId($this->videoId, $this->courseId);

            $vimeoClient = new VimeoClient();

            $validator->ensureDataExistsInDatabase()
                      ->ensureVideoExistsOnDisk();

            $newVimeoProjectId = $vimeoClient->ensureProjectExists($validator->getVimeoProjectId(), $validator->getCourseName());

            if ($newVimeoProjectId !== $validator->getVimeoProjectId()) {
                $validator->getCourse()->update([
                    'vimeo_project_id' => $newVimeoProjectId,
                ]);
                $validator = $validator->refreshCourse();
            }

            $vimeoUrl = $vimeoClient->upload($validator->getVideoFilePathFromDisk(), $validator->getVideoMetadata());

            $vimeoId = $vimeoClient->getIdFromResponse($vimeoUrl);

            SaveVimeoId::save(
                $validator->getVideo(),
                $validator->getVideoStorage(),
                $vimeoId
            );

            $vimeoClient->moveVideoToFolder($validator->getVimeoProjectId(), $vimeoId);
        } catch (Exception $e) {
            $message = 'Upload to Vimeo error: '.$e->getMessage().' on file '.$e->getFile().' on line '.$e->getLine();
            User::firstWhere('id', $this->userId)->notify(
                NovaNotification::make()
                ->message($message)
                ->icon('download')
                ->type('error')
            );
        }
    }
}

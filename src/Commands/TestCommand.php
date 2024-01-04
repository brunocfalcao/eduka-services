<?php

namespace Eduka\Services\Commands;

use Brunocfalcao\VimeoClient\Facades\VimeoClient;
use Eduka\Abstracts\Classes\EdukaCommand;
use Eduka\Cube\Models\Course;

class TestCommand extends EdukaCommand
{
    protected $signature = 'eduka:test';

    protected $description = 'Tests a custom code command';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        /*
        $videoId = VimeoClient::upload(
            resource_path('file-1.mp4'),
            [
                'name' => 'This is a name',
                'description' => 'This is a description',
                'privacy' => [
                    'comments' => 'nobody',
                    'embed' => 'whitelist',
                    'view' => 'unlisted',
                    'download' => true
                 ],
                'embed_domains' => [
                    'brunofalcao.dev', 'masteringnova.com'
                ]
            ]
        );
        */

        //dd(VimeoClient::request('/users/{user_id}/folders'));

        $course = Course::find(1)->first();

        $folder = VimeoClient::createFolder($course->name);

        $course->update(['vimeo_folder_uri' => $folder['body']['uri']]);

        //dd(array_keys($folder['body']['uri']));

        //$this->info(json_encode(VimeoClient::getAllFolders()));

        //$uri = VimeoClient::createPath('roger/that');

        //$this->paragraph('video id: ' . $videoId);

        $this->info('All good.');

        return 0;
    }
}

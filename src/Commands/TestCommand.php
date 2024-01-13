<?php

namespace Eduka\Services\Commands;

use Brunocfalcao\VimeoClient\Facades\VimeoClient;
use Eduka\Abstracts\Classes\EdukaCommand;
use Eduka\Cube\Models\Chapter;
use Eduka\Cube\Models\Course;
use Illuminate\Support\Str;

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
        $course = Course::all()->first();
        $course->name = Str::random(20);
        $course->save();
        */

        $chapter = Chapter::all()->first();
        $chapter->name = Str::random(20);
        $chapter->save();

        $this->info('Ok.');

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

        //$course = Course::find(1)->first();

        //dd(VimeoClient::upsertFolder('Laraning new videos', null, '47f269a6af138a7fad2d4962596c3407479c7bc9'));

        /**
        $folder = VimeoClient::upsertFolder('test');

        foreach ($folder['body']['data'] as $item) {
            $this->info($item['name'] . ' - ' . $item['resource_key']);
        }

        VimeoClient::upsertFolder('New Laraning Videos', '47f269a6af138a7fad2d4962596c3407479c7bc9');
         **/
        //dd($folder['body']['data'][0]);

        //$course->update(['vimeo_folder_uri' => $folder['body']['uri']]);

        //dd(array_keys($folder['body']['uri']));

        //$this->info(json_encode(VimeoClient::getAllFolders()));

        //$uri = VimeoClient::createPath('roger/that');

        //$this->paragraph('video id: ' . $videoId);

        //$this->info('All good.');

        return 0;
    }
}

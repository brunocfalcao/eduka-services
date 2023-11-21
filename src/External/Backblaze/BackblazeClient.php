<?php

namespace Eduka\Services\External\Backblaze;

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Illuminate\Support\Facades\Storage;

class BackblazeClient
{
    private S3Client $client;
    private string $rootPath;

    public function __construct(string $rootPathInBucket = 'videos')
    {
        $applicationKeyId = env('BACKBLAZE_KEY_ID');
        $applicationKey = env('BACKBLAZE_APP_KEY');
        $region = env('BACKBLAZE_REGION');

        $credentials = new Credentials($applicationKeyId, $applicationKey);

        $this->client = new S3Client([
            'version' => 'latest',
            'region' => $region,
            'endpoint' => 'https://s3.' . $region . '.backblazeb2.com',
            'credentials' => $credentials,
        ]);

        $this->rootPath = $rootPathInBucket;
    }

    public function createBucket(string $courseName)
    {
        $courseName = str($courseName)->lower()->replace(' ', '')->trim()->toString();

        try {
            $response = $this->client->createBucket([
                'Bucket' => $courseName,
            ]);

            return $response;
            // return new CreateBucketResponse($response);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function uploadTo(string $filePath, string $bucket, string $saveAs)
    {
        $fileContents = Storage::get($filePath);

        $extension = Storage::mimeType($filePath);

        $pathOnBackblaze = $this->rootPath . '/' . $saveAs . '.' . str($extension)->afterLast('/')->toString();

        return $this->client->putObject([
            'Bucket' => $bucket,
            'Key'    => $pathOnBackblaze,
            'Body'   => $fileContents,
        ]);
    }
}

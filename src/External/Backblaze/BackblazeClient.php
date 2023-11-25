<?php

namespace Eduka\Services\External\Backblaze;

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Exception;
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

    public function createBucket(string $bucketName)
    {
        $bucketName = $this->transformToProperBucketName($bucketName);

        try {
            $this->client->createBucket([
                'Bucket' => $bucketName,
            ]);

            return $bucketName;
            // return new CreateBucketResponse($response);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function transformToProperBucketName(string $name)
    {
        return str($name)->lower()->replace(' ', '')->trim()->toString();
    }

    public function bucketExists(string $name) : bool
    {
        try {
            return $this->client->doesBucketExistV2($name);

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Checks if the bucket exists or not.
     * If not, creates one
     *
     * Returns bucket name
     *
     * The returned bucket name might be different from
     * the one passed as parameter if the bucket was newly created.
     * Because not all names are supported. For example
     * Course 01 would not be accpeted, thus it is transformed to 'course01'.
     *
     * @param string $bucketName
     * @return string
     */
    public function ensureBucketExists(string $bucketName, string $createNewBucketUsing) : string
    {
        try {
            if($bucketName !== "" && $this->bucketExists($bucketName)) {
                return $bucketName;
            }

            // Bucket does not exist, create
            return $this->createBucket($createNewBucketUsing);

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

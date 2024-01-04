<?php

namespace Eduka\Services;

namespace Eduka\Services\External\Vimeo;

use Vimeo\Laravel\Facades\Vimeo;

class VimeoClient
{
    protected $videoId;

    public function withVideo($videoInput)
    {
        if (is_int($videoInput)) {
            // Assume the input is a Vimeo video ID.
            $this->videoId = $videoInput;
        } elseif ($videoInput instanceof \Illuminate\Http\UploadedFile) {
            // The input is an UploadedFile instance, upload it to Vimeo.
            $filePath = $videoInput->getPathName();
            $fileName = $videoInput->getClientOriginalName();
            $this->upload($filePath, $fileName);
        } elseif (is_string($videoInput)) {
            // The input is a file path, upload the file to Vimeo.
            $this->upload($videoInput, basename($videoInput));
        } else {
            throw new \InvalidArgumentException('Invalid video input provided.');
        }

        return $this;
    }

    public function addToFolder($folderPath)
    {
        if (! $this->videoId) {
            throw new \LogicException('No video ID is set.');
        }

        $folder = $this->findOrCreateFolder($folderPath);

        if ($folder) {
            Vimeo::request("/projects/{$folder['id']}/videos/{$this->videoId}", [], 'PUT');
        }

        return $this;
    }

    private function upload($filePath, $name, $description = null)
    {
        try {
            $response = Vimeo::upload($filePath, [
                'name' => $name,
                'description' => $description ?? 'Uploaded via Laravel',
            ]);

            $this->videoId = Vimeo::request($response, [], 'GET')['body']['uri'];
        } catch (\Exception $e) {
            // Handle the exception.
            throw new \RuntimeException('Failed to upload video: '.$e->getMessage());
        }
    }

    private function findOrCreateFolder($folderPath)
    {
        $folders = explode(' / ', $folderPath);
        $parentId = null;

        foreach ($folders as $folderName) {
            $folder = $this->getFolderByName($folderName, $parentId);

            if (! $folder) {
                $folder = $this->createFolder($folderName, $parentId);
            }

            $parentId = $folder['id'];
        }

        return $folder;
    }

    private function getFolderByName($folderName, $parentId = null)
    {
        // Search for the folder by name.
        $response = Vimeo::request('/me/projects', ['per_page' => 10], 'GET');

        foreach ($response['body']['data'] as $folder) {
            if ($folder['name'] === $folderName && ($parentId === null || $folder['parent']['uri'] === $parentId)) {
                return $folder;
            }
        }

        return null;
    }

    private function createFolder($folderName, $parentId = null)
    {
        // Create a new folder.
        $params = ['name' => $folderName];
        if ($parentId) {
            $params['parent_uri'] = $parentId;
        }

        $response = Vimeo::request('/me/projects', $params, 'POST');

        return $response['body'];
    }
}

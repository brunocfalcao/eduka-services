<?php

namespace Eduka\Services\External\Vimeo;

use Eduka\Cube\Models\Course;
use Symfony\Component\HttpFoundation\Response;
use Vimeo\Vimeo;

class VimeoClient
{
    private Vimeo $client;

    private const VIMEO_URL_CREATE_NEW_PROJECT_URL = '/me/projects';
    private const VIMEO_URL_GET_PROJECT_URL = '/me/projects/%s';

    private const HTTP_POST = 'POST';
    private const HTTP_GET = 'GET';

    private const HTTP_HEADER = [
        'Content-Type' => 'application/json'
    ];

    private const HTTP_JSON = true;

    public function __construct()
    {
        $this->client = new Vimeo(env('VIMEO_CLIENT_ID'), env('VIMEO_CLIENT_SECRET'), env('VIMEO_PERSONAL_ACCESS_TOKEN'));
    }

    public function upload(string $storagePath, array $metadata = [])
    {
        return $this->client->upload($storagePath, $metadata);
    }

    public function ensureProjectExists(string|null $vimeoProjectId, string $newProjectName)
    {
        if($vimeoProjectId && $this->checkIfProjectExists($vimeoProjectId)) {
            return;
        }

        // project does not exist. create a new one
        $newProjectResponse = $this->createProject($newProjectName);

        if($newProjectResponse['status'] !== Response::HTTP_CREATED) {
            throw new \Exception('Recevied api response from vimeo ' . $newProjectResponse['status'] . ' . Expecting 201');
        }

        return $this->getProjectIdFromVimeoPath($newProjectResponse['body']['uri']);
    }

    public function getIdFromResponse(string $vimeoPath) : string
    {
        return str($vimeoPath)->afterLast('/')->toString();
    }

    public function checkIfProjectExists(string $projectId) : bool
    {
        $url = sprintf(self::VIMEO_URL_GET_PROJECT_URL, $projectId);

        try {
            $response = $this->client->request($url, [], self::HTTP_GET, self::HTTP_JSON, self::HTTP_HEADER);

            return $response['status'] === Response::HTTP_OK;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function createProject(string $name) : array
    {
        $params = [ 'name' => $name, ];

        try {
            $response = $this->client->request( self::VIMEO_URL_CREATE_NEW_PROJECT_URL ,$params ,self::HTTP_POST, self::HTTP_JSON, self::HTTP_HEADER);

            if($response['status'] >= Response::HTTP_BAD_REQUEST) {
                throw new \Exception($response['body']['error']);
            }

            return $response;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}


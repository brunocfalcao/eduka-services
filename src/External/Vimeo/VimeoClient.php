<?php

namespace Eduka\Services\External\Vimeo;

use Symfony\Component\HttpFoundation\Response;
use Vimeo\Vimeo;

class VimeoClient
{
    private const VIMEO_URL_CREATE_NEW_PROJECT = '/me/projects';

    private const VIMEO_URL_GET_PROJECT = '/me/projects/%s';

    private const VIMEO_URL_UPDATE_VIDEO = '/videos/%s';

    private const VIMEO_URL_PUT_VIDEO_IN_PROJECT = '/users/%s/projects/%s/videos/%s';

    private const HTTP_POST = 'POST';

    private const HTTP_GET = 'GET';

    private const HTTP_PUT = 'PUT';

    private const HTTP_PATCH = 'PATCH';

    private const HTTP_HEADER = [
        'Content-Type' => 'application/json',
    ];

    private const HTTP_JSON = true;

    private Vimeo $client;

    private string $vimeoUserId;

    public function __construct()
    {
        $this->client = new Vimeo(env('VIMEO_CLIENT_ID'), env('VIMEO_CLIENT_SECRET'), env('VIMEO_PERSONAL_ACCESS_TOKEN'));
        $this->vimeoUserId = env('VIMEO_USER_ID');
    }

    public function upload(string $storagePath, array $metadata = [])
    {
        return $this->client->upload($storagePath, $metadata);
    }

    public function ensureProjectExists(?string $vimeoProjectId, string $newProjectName): string
    {
        if ($vimeoProjectId && $this->checkIfProjectExists($vimeoProjectId)) {
            return $vimeoProjectId;
        }

        // project does not exist. create a new one
        $newProjectResponse = $this->createProject($newProjectName);

        if ($newProjectResponse['status'] !== Response::HTTP_CREATED) {
            throw new \Exception('Recevied api response from vimeo '.$newProjectResponse['status'].' . Expecting 201');
        }

        return $this->getIdFromResponse($newProjectResponse['body']['uri']);
    }

    public function getIdFromResponse(string $vimeoPath): string
    {
        return str($vimeoPath)->afterLast('/')->toString();
    }

    public function checkIfProjectExists(string $projectId): bool
    {
        $url = sprintf(self::VIMEO_URL_GET_PROJECT, $projectId);

        $response = $this->makeRequest($url, [], self::HTTP_GET);

        return $response['status'] == Response::HTTP_OK;
    }

    public function createProject(string $name): array
    {
        $params = ['name' => $name];

        $response = $this->makeRequest(self::VIMEO_URL_CREATE_NEW_PROJECT, $params, self::HTTP_POST);

        if ($response['status'] >= Response::HTTP_BAD_REQUEST) {
            throw new \Exception($response['body']['error']);
        }

        return $response;
    }

    public function moveVideoToFolder(string $projectId, string $videoId)
    {
        $url = sprintf(self::VIMEO_URL_PUT_VIDEO_IN_PROJECT, $this->vimeoUserId, $projectId, $videoId);

        $response = $this->makeRequest($url, [], self::HTTP_PUT);

        if ($response['status'] >= Response::HTTP_BAD_REQUEST) {
            throw new \Exception(sprintf('could not move video %s to project/folder %s', $videoId, $projectId));
        }

        return $response;
    }

    private function makeRequest(string $url, array $params, string $method)
    {
        try {
            $response = $this->client->request($url, $params, $method, self::HTTP_JSON, self::HTTP_HEADER);

            return $response;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

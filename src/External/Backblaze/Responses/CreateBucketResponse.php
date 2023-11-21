<?php

namespace Eduka\Services\External\Backblaze\Responses;

class CreateBucketResponse
{
    public string $location;
    public array $metadata;

    public function __construct(string $jsonResponse)
    {
        $response = json_decode($jsonResponse);

        $this->location = $response['Location'];
        $this->metadata = $response['@metadata'];
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }
}

<?php

namespace OUTRIGHTVision\Support;

use OUTRIGHTVision\Exceptions\ApiException;
use Zttp\Zttp;

trait ApiCalls
{
    protected function getApiObject(int $id): array
    {
        $response = Zttp::withHeaders($this->getApiHeaders())
            ->get($this->getApiEndpoint(['id' => $id]), $this->getApiParameters());
        if (!$response->isOk()) {
            throw new ApiException;
        }
        return $response->json();
    }

    protected function getApiHeaders(): array
    {
        return [];
    }

    protected function getApiEndpoint(array $replacements = []): string
    {
        $replacements = collect($replacements)->mapWithKeys(function ($value, $key) {
            return ["{{$key}}" => $value];
        })->toArray();
        
        return strtr($this->endpoint, $replacements);
    }

    protected function getApiParameters(): array
    {
        return [];
    }
}

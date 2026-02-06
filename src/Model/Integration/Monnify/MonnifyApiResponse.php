<?php

namespace App\Model\Integration\Monnify;

class MonnifyApiResponse
{
    private bool $requestSuccessful = false;
    private string $responseMessage = '';
    private mixed $responseBody = null;

    public function isRequestSuccessful(): bool
    {
        return $this->requestSuccessful;
    }

    public function setRequestSuccessful(bool $requestSuccessful): self
    {
        $this->requestSuccessful = $requestSuccessful;
        return $this;
    }

    public function getResponseMessage(): string
    {
        return $this->responseMessage;
    }

    public function setResponseMessage(string $responseMessage): self
    {
        $this->responseMessage = $responseMessage;
        return $this;
    }

    public function getResponseBody(): mixed
    {
        return $this->responseBody;
    }

    public function setResponseBody(mixed $responseBody): self
    {
        $this->responseBody = $responseBody;
        return $this;
    }
}

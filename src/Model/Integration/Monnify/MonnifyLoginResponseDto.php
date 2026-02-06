<?php

namespace App\Model\Integration\Monnify;

class MonnifyLoginResponseDto
{
    private string $accessToken = '';
    private int $expiresIn = 0;

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function setExpiresIn(int $expiresIn): self
    {
        $this->expiresIn = $expiresIn;
        return $this;
    }
}

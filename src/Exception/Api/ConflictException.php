<?php

namespace App\Exception\Api;

use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\Response;

class ConflictException extends ApiException
{
    public function __construct(string $message)
    {
        parent::__construct($message, Response::HTTP_CONFLICT);
    }
}
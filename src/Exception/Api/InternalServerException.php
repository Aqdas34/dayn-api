<?php

namespace App\Exception\Api;

use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\Response;

class InternalServerException extends ApiException
{
    public function __construct(string $message)
    {
        parent::__construct($message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
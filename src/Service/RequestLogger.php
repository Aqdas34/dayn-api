<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class RequestLogger
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SerializerInterface $serializer
    )
    {
    }

    public function logRequest(Request $request): void
    {
        $logData = [
            'method' => $request->getMethod(),
            'path' => $request->getPathInfo(),
            'headers' => $request->headers->all(),
            'query' => $request->query->all(),
            'body' => $request->getContent(),
        ];
        $logDataAsJson = $this->serializer->serialize($logData, 'json');
        $this->logger->info('Request details...');
        $this->logger->info($logDataAsJson);
    }

    public function logResponse(Response $response): void
    {
        $logData = [
            'status_code' => $response->getStatusCode(),
            'body' => $response->getContent(),
        ];
        $logDataAsJson = $this->serializer->serialize($logData, 'json');
        $this->logger->info('Response details...');
        $this->logger->info($logDataAsJson);
    }
}

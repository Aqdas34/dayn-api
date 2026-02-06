<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AppLoggerService
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SerializerInterface $serializer
    )
    {
    }

    public function logObjectAsJson(mixed $object): void
    {
        try {
            $this->logger->info($this->serializer->serialize($object, 'json'));
        } catch (\Throwable $exception) {
            $this->logger->info("An exception occurred when attempting to log an object as JSON");
            $this->logger->error($exception->getMessage());
        }
    }
}
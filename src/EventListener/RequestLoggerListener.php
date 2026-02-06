<?php

namespace App\EventListener;

use App\Service\RequestLogger;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestLoggerListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestLogger $requestLogger,
        private readonly LoggerInterface $logger
    )
    {
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->logger->info("About to log request...");
        $request = $event->getRequest();
        $this->requestLogger->logRequest($request);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $this->logger->info("About to log response...");
        $response = $event->getResponse();
        // $this->requestLogger->logResponse($response);
    }
}

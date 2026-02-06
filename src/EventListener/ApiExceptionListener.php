<?php

namespace App\EventListener;

use App\Exception\ApiException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class ApiExceptionListener implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        $exception = $event->getThrowable();

        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = $exception->getMessage();

        // Print Stack Trace
	$this->logger->error($message);
        $this->logger->error($exception->getTraceAsString());

        $errors = [];

        if ($exception instanceof HttpExceptionInterface) {
            if ($exception instanceof UnprocessableEntityHttpException) {
                $errors = explode("\n", $exception->getMessage());
                $message = "One or more validation error(s) occurred!";
            }

            if ($exception instanceof ApiException) {
                // Handle our exceptions
                $status = $exception->getStatusCode();
            }

            $status = $exception->getStatusCode();
        }

        $response = new JsonResponse([
            'status' => $status,
            'message' => $message,
            'errors' => $errors,
        ], $status);

        $event->setResponse($response);
    }
}
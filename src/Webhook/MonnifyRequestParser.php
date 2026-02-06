<?php

namespace App\Webhook;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\ChainRequestMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher\MethodRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Webhook\Client\AbstractRequestParser;
use Symfony\Component\Webhook\Exception\RejectWebhookException;

final class MonnifyRequestParser extends AbstractRequestParser
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    const SIGNATURE_HEADER_KEY_MONNIFY = 'monnify-signature';

    protected function getRequestMatcher(): RequestMatcherInterface
    {
        return new ChainRequestMatcher([
            new MethodRequestMatcher('POST'),
        ]);
    }

    protected function doParse(Request $request, #[\SensitiveParameter] string $secret): ?RemoteEvent
    {
        $this->logger->info("Monnify Request parsing started");

        $signature = $request->headers->get(self::SIGNATURE_HEADER_KEY_MONNIFY);
        $payload = $request->getContent();

        if (!$signature || !$this->validateMonnifySignature($signature, $payload, $secret)) {
            $this->logger->error("Monnify Signature Validation Failed");
            throw new RejectWebhookException(Response::HTTP_UNAUTHORIZED, 'Invalid authentication token.');
        }

        $data = $request->getPayload();

        if (!$data->has('eventType') || !$data->has('eventData')) {
            throw new RejectWebhookException(Response::HTTP_BAD_REQUEST, 'Request payload does not contain required fields.');
        }

        return new RemoteEvent(
            $data->getString('eventType'),
            'monnify',
            $data->all(),
        );
    }

    private function validateMonnifySignature(string $signature, string $requestBody, string $clientSecret): bool
    {
        $calculatedSignature = hash_hmac('sha512', $requestBody, $clientSecret);
        return hash_equals($signature, $calculatedSignature);
    }
}

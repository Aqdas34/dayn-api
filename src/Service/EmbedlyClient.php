<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class EmbedlyClient
{
    private string $apiBase;
    private string $apiKey;
    private ?string $webhookSecret;
    public function __construct(private HttpClientInterface $http, LoggerInterface $logger, string $apiBase, string $apiKey, ?string $webhookSecret = null)
    {
        $this->apiBase = $apiBase;
        $this->apiKey = $apiKey;
        $this->webhookSecret = $webhookSecret;
    }

    public function createTransactionalAccount(string $transactionReference, int $amountInNaira, string $customerName, string $customerEmail): ?array
    {
        // amount expected by provider might be in kobo or naira; adjust accordingly
        $payload = [
            'reference' => $transactionReference,
            'amount' => $amountInNaira, // check embedly docs for units
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'metadata' => ['type' => 'wallet.funding'],
        ];

        $response = $this->http->request('POST', "{$this->apiBase}/virtual-account/create", [
            'json' => $payload,
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Accept' => 'application/json',
            ],
        ]);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return $response->toArray();
        }

        return null;
    }

    public function verifyTransaction(string $reference): ?array
    {
        $response = $this->http->request('GET', "{$this->apiBase}/transaction/verify/{$reference}", [
            'headers' => ['Authorization' => "Bearer {$this->apiKey}"],
        ]);
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return $response->toArray();
        }
        return null;
    }

    public function initiatePayout(array $payload): ?array
    {
        $response = $this->http->request('POST', "{$this->apiBase}/payout", [
            'json' => $payload,
            'headers' => ['Authorization' => "Bearer {$this->apiKey}"],
        ]);
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return $response->toArray();
        }
        return null;
    }

    public function verifyWebhookSignature(string $body, ?string $signatureHeader): bool
    {
        if (!$this->webhookSecret || !$signatureHeader) {
            return false;
        }
        // Embedly may use HMAC-SHA256; confirm in docs. Example:
        $computed = hash_hmac('sha256', $body, $this->webhookSecret);
        return hash_equals($computed, $signatureHeader);
    }
}

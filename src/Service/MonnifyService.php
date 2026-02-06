<?php

namespace App\Service;

use App\Model\Integration\Monnify\MonnifyApiResponse;
use App\Model\Integration\Monnify\MonnifyCreateReservedAccountRequestDto;
use App\Model\Integration\Monnify\MonnifyCreateReservedAccountResponseDto;
use App\Model\Integration\Monnify\MonnifyLoginResponseDto;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Value;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MonnifyService
{
    private ?string $accessToken = null;
    private ?\DateTimeImmutable $tokenExpiresAt = null;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly SerializerInterface $serializer,
        private readonly LoggerInterface $logger,
        #[Value('%monnify_api_key%')] private string $apiKey,
        #[Value('%monnify_secret_key%')] private string $secretKey,
        #[Value('%monnify_base_url%')] private string $baseUrl,
    ) {
    }

    private function authenticate(): bool
    {
        if ($this->accessToken && $this->tokenExpiresAt > new \DateTimeImmutable()) {
            return true;
        }

        try {
            $authHeader = 'Basic ' . base64_encode($this->apiKey . ':' . $this->secretKey);
            
            $response = $this->httpClient->request('POST', $this->baseUrl . '/api/v1/auth/login', [
                'headers' => [
                    'Authorization' => $authHeader,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                $content = $response->getContent(false);
                $this->logger->error('Monnify Auth Failed. Status Code: ' . $response->getStatusCode() . ' Response: ' . $content);
                return false;
            }

            /** @var MonnifyApiResponse $apiResponse */
            $apiResponse = $this->serializer->deserialize($response->getContent(), MonnifyApiResponse::class, 'json');

            if (!$apiResponse->isRequestSuccessful()) {
                $this->logger->error('Monnify Auth Failed. Request not successful. Message: ' . $apiResponse->getResponseMessage());
                return false;
            }

            /** @var MonnifyLoginResponseDto $loginData */
            $loginData = $this->serializer->denormalize($apiResponse->getResponseBody(), MonnifyLoginResponseDto::class);

            $this->accessToken = $loginData->getAccessToken();
            $this->tokenExpiresAt = (new \DateTimeImmutable())->modify('+' . ($loginData->getExpiresIn() - 60) . ' seconds');

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Monnify Auth Exception: ' . $e->getMessage());
            return false;
        }
    }

    public function createReservedAccount(MonnifyCreateReservedAccountRequestDto $requestDto): ?MonnifyCreateReservedAccountResponseDto
    {
        if (!$this->authenticate()) {
            return null;
        }

        try {
            $response = $this->httpClient->request('POST', $this->baseUrl . '/api/v1/bank-transfer/reserved-accounts', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'body' => $this->serializer->serialize($requestDto, 'json'),
            ]);

            if ($response->getStatusCode() !== 200) {
                $content = $response->getContent(false);
                $this->logger->error('Monnify Reserved Account Creation Failed. Status Code: ' . $response->getStatusCode() . ' Response: ' . $content);
                return null;
            }

            /** @var MonnifyApiResponse $apiResponse */
            $apiResponse = $this->serializer->deserialize($response->getContent(), MonnifyApiResponse::class, 'json');

            if (!$apiResponse->isRequestSuccessful()) {
                return null;
            }

            $body = $apiResponse->getResponseBody();

            return $this->serializer->denormalize($body, MonnifyCreateReservedAccountResponseDto::class);
        } catch (\Exception $e) {
            $this->logger->error('Monnify Reserved Account Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function getReservedAccountTransactions(string $accountReference): ?array
    {
        if (!$this->authenticate()) {
            return null;
        }

        try {
            $response = $this->httpClient->request('GET', $this->baseUrl . '/api/v1/bank-transfer/reserved-accounts/transactions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ],
                'query' => [
                    'accountReference' => $accountReference,
                    'page' => 0,
                    'size' => 10,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                $this->logger->error('Monnify Get Reserved Account Transactions Failed: ' . $response->getContent(false));
                return null;
            }

            /** @var MonnifyApiResponse $apiResponse */
            $apiResponse = $this->serializer->deserialize($response->getContent(), MonnifyApiResponse::class, 'json');

            if (!$apiResponse->isRequestSuccessful()) {
                return null;
            }

            return $apiResponse->getResponseBody();
        } catch (\Exception $e) {
            $this->logger->error('Monnify Get Reserved Account Transactions Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function verifyTransaction(string $transactionReference): ?array
    {
        if (!$this->authenticate()) {
            return null;
        }

        try {
            $response = $this->httpClient->request('GET', $this->baseUrl . '/api/v2/transactions/' . $transactionReference, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                $this->logger->error('Monnify Transaction Verification Failed: ' . $response->getContent(false));
                return null;
            }

            /** @var MonnifyApiResponse $apiResponse */
            $apiResponse = $this->serializer->deserialize($response->getContent(), MonnifyApiResponse::class, 'json');

            if (!$apiResponse->isRequestSuccessful()) {
                return null;
            }

            return $apiResponse->getResponseBody();
        } catch (\Exception $e) {
            $this->logger->error('Monnify Verify Transaction Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function getBankList(): ?array
    {
        if (!$this->authenticate()) {
            return null;
        }

        try {
            $response = $this->httpClient->request('GET', $this->baseUrl . '/api/v1/banks', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                $this->logger->error('Monnify Get Banks Failed: ' . $response->getContent(false));
                return null;
            }

            /** @var MonnifyApiResponse $apiResponse */
            $apiResponse = $this->serializer->deserialize($response->getContent(), MonnifyApiResponse::class, 'json');

            if (!$apiResponse->isRequestSuccessful()) {
                return null;
            }

            return $apiResponse->getResponseBody();
        } catch (\Exception $e) {
            $this->logger->error('Monnify Get Banks Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function validateBankAccount(string $accountNumber, string $bankCode): ?array
    {
        if (!$this->authenticate()) {
            return null;
        }

        try {
            $response = $this->httpClient->request('GET', $this->baseUrl . '/api/v1/disbursements/account/validate', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ],
                'query' => [
                    'accountNumber' => $accountNumber,
                    'bankCode' => $bankCode,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                $this->logger->error('Monnify Account Validation Failed: ' . $response->getContent(false));
                return null;
            }

            /** @var MonnifyApiResponse $apiResponse */
            $apiResponse = $this->serializer->deserialize($response->getContent(), MonnifyApiResponse::class, 'json');

            if (!$apiResponse->isRequestSuccessful()) {
                return null;
            }

            return $apiResponse->getResponseBody();
        } catch (\Exception $e) {
            $this->logger->error('Monnify Account Validation Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function initiateDisbursement(array $data): ?array
    {
        if (!$this->authenticate()) {
            return null;
        }

        try {
            $response = $this->httpClient->request('POST', $this->baseUrl . '/api/v1/disbursements/single', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($data),
            ]);

            if ($response->getStatusCode() !== 200) {
                $this->logger->error('Monnify Initiation Disbursement Failed: ' . $response->getContent(false));
                return null;
            }

            /** @var MonnifyApiResponse $apiResponse */
            $apiResponse = $this->serializer->deserialize($response->getContent(), MonnifyApiResponse::class, 'json');

            if (!$apiResponse->isRequestSuccessful()) {
                return null;
            }

            return $apiResponse->getResponseBody();
        } catch (\Exception $e) {
            $this->logger->error('Monnify Initiation Disbursement Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function verifyDisbursement(string $reference): ?array
    {
        if (!$this->authenticate()) {
            return null;
        }

        try {
            $response = $this->httpClient->request('GET', $this->baseUrl . '/api/v1/disbursements/single/summary', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ],
                'query' => [
                    'reference' => $reference,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                $this->logger->error('Monnify Verify Disbursement Failed: ' . $response->getContent(false));
                return null;
            }

            /** @var MonnifyApiResponse $apiResponse */
            $apiResponse = $this->serializer->deserialize($response->getContent(), MonnifyApiResponse::class, 'json');

            if (!$apiResponse->isRequestSuccessful()) {
                return null;
            }

            return $apiResponse->getResponseBody();
        } catch (\Exception $e) {
            $this->logger->error('Monnify Verify Disbursement Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function computeTransferFeeFromNairaAmount(float $amount): float
    {
        return 100.0; // Monnify flat fee for disbursements
    }
}

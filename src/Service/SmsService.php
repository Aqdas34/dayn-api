<?php

namespace App\Service;

use App\Util\UidUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class SmsService
{
    private Client $twilioClient;

    /**
     * @throws ConfigurationException
     */
    public function __construct(
        #[Autowire(param: 'twilio_account_sid')] private string $twilioAccountSid,
        #[Autowire(param: 'twilio_auth_token')] private string $twilioAuthToken,
        #[Autowire(param: 'twilio_number')] private string $twilioNumber,
        private readonly HttpClientInterface $httpClient,
        private readonly SerializerInterface $serializer,
        private readonly LoggerInterface $logger,
        private readonly AppLoggerService $appLoggerService
    )
    {
        $this->twilioClient = new Client($this->twilioAccountSid, $twilioAuthToken);
    }

    public function sendMessage(string $to, string $message): ?string
    {
        $this->logger->info("Account SID: $this->twilioAccountSid");
        $this->logger->info("Auth Token: $this->twilioAuthToken");
        $this->logger->info("Number: $this->twilioNumber");

        $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $this->twilioAccountSid . '/Messages.json';
        $requestBody = [
            'To' => $to,
            'From' => 'Dayn',
            'Body' => $message,
        ];

        $basicToken = base64_encode($this->twilioAccountSid . ':' . $this->twilioAuthToken);
        try {
            $response = $this->httpClient->request('POST', $url, [
                'headers' => [
                    'Authorization' => "Basic $basicToken",
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => $requestBody,
            ]);

            $statusCode = $response->getStatusCode();
            $rawResponse = $response->getContent();

            $this->appLoggerService->logObjectAsJson($rawResponse);

            return "Message sent successfully.";
        } catch (TransportExceptionInterface $e) {
            $this->logger->error($e->getMessage());
            return null;
        } catch (ClientExceptionInterface $e) {
            $this->logger->error("Twilio Client Exception");
            $this->appLoggerService->logObjectAsJson($requestBody);
            $this->logger->error($e->getMessage());
            return null;
        } catch (RedirectionExceptionInterface $e) {
            $this->logger->error("Twilio Redirect Exception");
            $this->logger->error($e->getMessage());
            return null;
        } catch (ServerExceptionInterface $e) {
            $this->logger->error("Twilio Server Exception");
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function sendMessageTest(string $to, string $message): ?string
    {
        $this->logger->info("Message sent to $to");
        $this->logger->info("Message Content: $message");
        return null;
    }

    public function sendMessageWithTwilioSdk(string $to, string $message): ?string
    {
        // Send an SMS
        try {
            $sentMessage = $this->twilioClient->messages->create(
                $to, // Recipient's phone number
                [
                    // 'from' => $this->twilioNumber, // Your Twilio number
                    'body' => $message
                ]
            );

            $this->logger->info("Message Status: " . $sentMessage->status);

            return $sentMessage->status;
        } catch (TwilioException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function sendMessageWithEbulkSMS(string $to, string $message): ?string
    {
        $smsData = [
            "SMS" => [
                "auth" => [
                    "username" => "andromadusv2@gmail.com",
                    "apikey" => "5a51048eb270d83f644a368418b8754fbde925b1"
                ],
                "message" => [
                    "sender" => "Dayn",
                    "messagetext" => "Test Message on EBulkSMS",
                    "flash" => "0"
                ],
                "recipients" => [
                    "gsm" => [
                        [
                            "msidn" => $to,
                            "msgid" => UidUtils::generateUid(),
                        ],
                    ],
                ],
            ],
        ];

        $url = "https://api.ebulksms.com/sendsms.json";

        try {
            $response = $this->httpClient->request('POST', $url, [
                'headers' => [
                    'Authorization' => "Basic $basicToken",
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => $requestBody,
            ]);

            $statusCode = $response->getStatusCode();
            $rawResponse = $response->getContent();

            $this->appLoggerService->logObjectAsJson($rawResponse);

            return "Message sent successfully.";
        } catch (TransportExceptionInterface $e) {
            $this->logger->error($e->getMessage());
            return null;
        } catch (ClientExceptionInterface $e) {
            $this->logger->error("Twilio Client Exception");
            $this->appLoggerService->logObjectAsJson($requestBody);
            $this->logger->error($e->getMessage());
            return null;
        } catch (RedirectionExceptionInterface $e) {
            $this->logger->error("Twilio Redirect Exception");
            $this->logger->error($e->getMessage());
            return null;
        } catch (ServerExceptionInterface $e) {
            $this->logger->error("Twilio Server Exception");
            $this->logger->error($e->getMessage());
            return null;
        }
    }
}
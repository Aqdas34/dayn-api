<?php

use App\Service\MonnifyPaymentProcessor;
use Symfony\Component\Dotenv\Dotenv;
use Psr\Log\LoggerInterface;

require dirname(__DIR__).'/vendor/autoload.php';

// Mock Logger
class MockLogger implements LoggerInterface {
    public function emergency($message, array $context = []): void {}
    public function alert($message, array $context = []): void {}
    public function critical($message, array $context = []): void {}
    public function error($message, array $context = []): void { echo "LOGGER ERROR: " . $message . "\n"; }
    public function warning($message, array $context = []): void {}
    public function notice($message, array $context = []): void {}
    public function info($message, array $context = []): void {}
    public function debug($message, array $context = []): void {}
    public function log($level, $message, array $context = []): void {}
}

class MockMonnifyService
{
    public function getReservedAccountTransactions($reference)
    {
        // Simulate finding no transactions here to force fallback to verifyTransaction
        return [];
    }

    public function verifyTransaction($reference)
    {
        // Simulate Monnify response for 500 Naira
        return [
            'transactionReference' => 'MNFY|' . $reference,
            'paymentReference' => $reference,
            'amount' => 500.00, // 500 Naira
            'amountPaid' => 500.00,
            'payableAmount' => 500.00,
            'currency' => 'NGN',
            'paymentStatus' => 'PAID',
            'createdOn' => '2023-10-27T10:00:00Z',
            'completedOn' => '2023-10-27T10:00:05Z',
        ];
    }
}

// Instantiate manually
$logger = new MockLogger();
$contractCode = '123456'; // Dummy
$mockMonnifyService = new MockMonnifyService(); // No type hint check in script unless strict types

// We might need to wrap the mock service if the constructor has strict type hinting for MonnifyService class
// Since MonnifyPaymentProcessor type hints MonnifyService, we either need to extend it or mock it properly.
// Given strict types in PHP, passing a MockMonnifyService that doesn't extend App\Service\MonnifyService will fail.
// So we will try to extend it, but we need to mock the constructor args of MonnifyService too or override the constructor.
// Easiest is to create an anonymous class extending MonnifyService if it's not final.

// Checking MonnifyService... it is not final. But it has readonly properties and constructor.
// We can use Reflection or just a clean mock extending it.

class TestMonnifyService extends \App\Service\MonnifyService
{
    public function __construct() {
        // Bypass parent constructor
    }

    public function getReservedAccountTransactions(string $accountReference): ?array
    {
        return [];
    }

    public function verifyTransaction(string $transactionReference): ?array
    {
        return [
            'transactionReference' => 'MNFY|' . $transactionReference,
            'paymentReference' => $transactionReference,
            'amount' => 500.00, // 500 Naira
            'amountPaid' => 500.00,
            'payableAmount' => 500.00,
            'currency' => 'NGN',
            'paymentStatus' => 'PAID',
            'createdOn' => '2023-10-27T10:00:00Z',
            'completedOn' => '2023-10-27T10:00:05Z',
        ];
    }
}

$mockMonnifyService = new TestMonnifyService();

try {
    $processor = new MonnifyPaymentProcessor(
        $mockMonnifyService,
        $logger,
        $contractCode
    );

    echo "Verifying transaction logic...\n";

    $result = $processor->verifyTransaction('TEST_REF_123');

    if ($result) {
        echo "Result Amount Specified: " . $result->getAmountSpecified() . "\n";
        echo "Result Amount Paid: " . $result->getAmountPaid() . "\n";
        
        if ($result->getAmountSpecified() == 500.00) {
            echo "SUCCESS: Amount is 500.00 as expected.\n";
        } else {
            echo "FAILURE: Amount is " . $result->getAmountSpecified() . ", expected 500.00.\n";
        }
    } else {
        echo "FAILURE: verifyTransaction returned null.\n";
    }

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

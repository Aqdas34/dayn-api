<?php

use App\Kernel;
use App\Entity\User;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

$container = $kernel->getContainer();
$entityManager = $container->get('doctrine.orm.entity_manager');

// Find a test user
$user = $entityManager->getRepository(User::class)->findOneBy([]);

if (!$user) {
    echo "No user found in database.\n";
    exit;
}

echo "Testing login for user: " . $user->getUsername() . "\n";
echo "User UID: " . $user->getUid() . "\n";

// Check if user has a wallet
$wallet = $user->getWallet();
if ($wallet) {
    echo "User has wallet: YES\n";
    echo "Wallet balance: " . $wallet->getBalance() . "\n";
    $hasPin = $wallet->getTransactionPin() !== null;
    echo "Has transaction PIN: " . ($hasPin ? "YES" : "NO") . "\n";
} else {
    echo "User has wallet: NO (THIS IS THE PROBLEM!)\n";
}

// Test the generateUserData logic
try {
    $hasWalletTransactionPin = $wallet !== null && $wallet->getTransactionPin() !== null;
    $userData = [
        'uid' => $user->getUid(),
        'username' => $user->getUsername(),
        'first_name' => $user->getFirstName(),
        'last_name' => $user->getLastName(),
        'phone_number' => $user->getPhoneNumber(),
        'has_wallet_transaction_pin' => $hasWalletTransactionPin,
    ];
    
    echo "\nGenerated user data successfully:\n";
    print_r($userData);
} catch (\Exception $e) {
    echo "\nERROR generating user data: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}


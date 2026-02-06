<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpClient\HttpClient;

$client = HttpClient::create();

echo "Testing Bank Listing API with Monnify Integration\n";
echo "=================================================\n\n";

try {
    echo "1. Testing GET /api/banks/list\n";
    echo "   Endpoint: http://localhost:8000/api/banks/list\n";
    
    $response = $client->request('GET', 'http://localhost:8000/api/banks/list');
    
    $statusCode = $response->getStatusCode();
    echo "   Status Code: $statusCode\n";
    
    if ($statusCode === 200) {
        $content = $response->toArray();
        
        echo "   ✅ Success!\n";
        echo "   Message: " . ($content['message'] ?? 'N/A') . "\n";
        
        if (isset($content['data']) && is_array($content['data'])) {
            $bankCount = count($content['data']);
            echo "   Banks Retrieved: $bankCount\n\n";
            
            if ($bankCount > 0) {
                echo "   Sample Banks (first 5):\n";
                $sampleBanks = array_slice($content['data'], 0, 5);
                foreach ($sampleBanks as $bank) {
                    echo "   - {$bank['name']} (Code: {$bank['code']})\n";
                }
            }
        }
    } else {
        echo "   ❌ Failed with status code: $statusCode\n";
        echo "   Response: " . $response->getContent(false) . "\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    if (method_exists($e, 'getResponse')) {
        try {
            echo "   Response: " . $e->getResponse()->getContent(false) . "\n";
        } catch (Exception $inner) {
            echo "   Could not get response content\n";
        }
    }
}

echo "\n=================================================\n";
echo "Test Complete\n";

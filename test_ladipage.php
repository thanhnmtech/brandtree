<?php
/**
 * Test script for Ladipage integration
 * 
 * This script tests the Ladipage controller to ensure it properly:
 * 1. Validates secret_key and api_key
 * 2. Validates required fields (title, slug, html, ladiID)
 * 3. Validates slug format
 * 4. Checks for duplicate slugs
 * 5. Creates new page_content and page_content_translation records
 * 6. Updates existing records when ladiID already exists
 */

// Test data
$url = 'http://127.0.0.1:8000/api/ladipage/store';

// Get keys from .env
$secretKey = '2V6SYPcwY5b5dcXM';
$apiKey = 'FNp2GrcNJDZvc0rMgmKn0cJh4QIIa5hLg3oqod0AKs';

echo "=== Testing Ladipage Integration ===\n\n";

// Test 1: Invalid secret key
echo "Test 1: Invalid secret key\n";
$data = [
    'secret_key' => 'wrong_key',
    'api_key' => $apiKey,
    'title' => 'Test Page',
    'slug' => 'test-page',
    'html' => '<h1>Test Content</h1>',
    'ladiID' => 'test-ladi-001'
];
$result = sendRequest($url, $data);
echo "Expected: 401 - Secret Key không hợp lệ\n";
echo "Result: " . json_encode($result) . "\n\n";

// Test 2: Invalid API key
echo "Test 2: Invalid API key\n";
$data['secret_key'] = $secretKey;
$data['api_key'] = 'wrong_api_key';
$result = sendRequest($url, $data);
echo "Expected: 401 - Mã tích hợp không hợp lệ\n";
echo "Result: " . json_encode($result) . "\n\n";

// Test 3: Missing required fields
echo "Test 3: Missing title\n";
$data['api_key'] = $apiKey;
unset($data['title']);
$result = sendRequest($url, $data);
echo "Expected: 401 - content empty\n";
echo "Result: " . json_encode($result) . "\n\n";

// Test 4: Invalid slug format
echo "Test 4: Invalid slug format\n";
$data['title'] = 'Test Page';
$data['slug'] = 'test page with spaces';
$result = sendRequest($url, $data);
echo "Expected: 401 - slug invalid\n";
echo "Result: " . json_encode($result) . "\n\n";

// Test 5: Create new page (should succeed)
echo "Test 5: Create new page\n";
$data['slug'] = 'test-page-' . time();
$data['ladiID'] = 'test-ladi-' . time();
$result = sendRequest($url, $data);
echo "Expected: 200 - Success with id and url\n";
echo "Result: " . json_encode($result) . "\n\n";

if (isset($result['code']) && $result['code'] == 200) {
    $createdId = $result['id'];
    $createdLadiID = $data['ladiID'];
    
    // Test 6: Update existing page
    echo "Test 6: Update existing page\n";
    $data['title'] = 'Updated Test Page';
    $data['html'] = '<h1>Updated Content</h1>';
    $result = sendRequest($url, $data);
    echo "Expected: 200 - Success with same id\n";
    echo "Result: " . json_encode($result) . "\n\n";
    
    // Test 7: Try to create another page with same slug (should fail)
    echo "Test 7: Duplicate slug check\n";
    $data['ladiID'] = 'different-ladi-id-' . time();
    $result = sendRequest($url, $data);
    echo "Expected: 401 - Slug đã tồn tại\n";
    echo "Result: " . json_encode($result) . "\n\n";
}

echo "=== Tests Complete ===\n";

function sendRequest($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    if ($result === null) {
        return ['error' => 'Invalid JSON response', 'raw' => $response, 'http_code' => $httpCode];
    }
    
    return $result;
}


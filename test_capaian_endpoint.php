<?php
// test_capaian_endpoint.php - Test actual API endpoint

// First, get a valid token
$baseUrl = 'http://localhost/TugasAkhir/sim-pkpps/public/api/v1';

echo "=== TEST CAPAIAN API ENDPOINT ===\n\n";

// Step 1: Login to get token
echo "1. Login to get token...\n";
$loginData = json_encode([
    'id_santri' => 'HELGA FAISA_1',
    'password' => 's001'
]);

$ch = curl_init($baseUrl . '/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $loginData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$loginResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "❌ Login failed with HTTP $httpCode\n";
    echo "Response: $loginResponse\n";
    exit;
}

$loginResult = json_decode($loginResponse, true);
if (!isset($loginResult['token'])) {
    echo "❌ No token in response\n";
    echo "Response: " . json_encode($loginResult, JSON_PRETTY_PRINT) . "\n";
    exit;
}

$token = $loginResult['token'];
echo "✅ Login successful, token obtained\n\n";

// Step 2: Call capaian/overview endpoint
echo "2. Fetching capaian overview...\n";

$ch = curl_init($baseUrl . '/capaian/overview');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Bearer ' . $token
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";

if ($httpCode === 200) {
    echo "✅ API Call successful!\n\n";
    
    $result = json_decode($response, true);
    
    if ($result['success']) {
        echo "📊 RESPONSE DATA:\n";
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "\n\n";
        
        // Verify data structure
        echo "📋 DATA STRUCTURE VALIDATION:\n";
        $data = $result['data'];
        
        echo ($data['santri'] ? "✅" : "❌") . " Santri data exists\n";
        echo ($data['semester'] ? "✅" : "❌") . " Semester data exists\n";
        echo ($data['statistik_umum'] ? "✅" : "❌") . " Statistik umum exists\n";
        echo ($data['per_kategori'] ? "✅" : "❌") . " Per kategori exists\n";
        
        if (isset($data['semester']['list_semester'])) {
            echo "✅ List semester: " . count($data['semester']['list_semester']) . " items\n";
        }
        
        if (isset($data['per_kategori'])) {
            echo "✅ Categories: " . count($data['per_kategori']) . " items\n";
            foreach ($data['per_kategori'] as $kat) {
                echo "   - {$kat['kategori']}: {$kat['total_materi']} materi\n";
            }
        }
        
    } else {
        echo "❌ API returned success=false\n";
        echo "Message: " . ($result['message'] ?? 'No message') . "\n";
    }
    
} else {
    echo "❌ API Call failed\n";
    echo "Response: $response\n";
}

echo "\n=== TEST COMPLETE ===\n";

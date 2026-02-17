<?php
/**
 * Test Login API
 * Jalankan: php test_login.php
 */

// Ganti sesuai data akun wali yang sudah dibuat
$username = "Nama Santri";  // Ganti dengan nama santri yang sudah punya akun wali
$password = "NIS";          // Ganti dengan NIS santri

$url = 'http://localhost/TugasAkhir/sim-pkpps/public/api/v1/login';

$data = [
    'id_santri' => $username,
    'password' => $password
];

$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
    ],
];

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo "Error connecting to API\n";
} else {
    echo "Response:\n";
    $response = json_decode($result, true);
    print_r($response);
    
    if (isset($response['success']) && $response['success']) {
        echo "\n✅ LOGIN BERHASIL!\n";
        echo "Token: " . ($response['token'] ?? 'No token') . "\n";
        echo "User: " . ($response['user']['name'] ?? 'Unknown') . "\n";
        echo "Role: " . ($response['user']['role'] ?? 'Unknown') . "\n";
    } else {
        echo "\n❌ LOGIN GAGAL!\n";
        echo "Message: " . ($response['message'] ?? 'Unknown error') . "\n";
    }
}

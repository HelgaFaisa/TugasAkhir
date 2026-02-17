<?php
/**
 * Comprehensive Debug Script
 * Akses: http://localhost/TugasAkhir/debug_comprehensive.php
 */

echo "<html><head><title>Debug Comprehensive</title>";
echo "<style>body{font-family:Arial;padding:20px;} .ok{color:green;} .error{color:red;} .section{border:1px solid #ddd;padding:15px;margin:10px 0;} pre{background:#f4f4f4;padding:10px;}</style>";
echo "</head><body>";

echo "<h1>🔍 Comprehensive Debug - SIM-PKPPS</h1>";
echo "<p>Waktu: " . date('Y-m-d H:i:s') . "</p><hr>";

// Test 1: Laravel Files
echo "<div class='section'>";
echo "<h2>1. File Existence Check</h2>";

$files = [
    'Controller' => __DIR__ . '/sim-pkpps/app/Http/Controllers/Admin/UserController.php',
    'Routes' => __DIR__ . '/sim-pkpps/routes/web.php',
    'View Wali' => __DIR__ . '/sim-pkpps/resources/views/admin/users/wali_accounts.blade.php',
    'API Controller' => __DIR__ . '/sim-pkpps/app/Http/Controllers/Api/ApiAuthController.php',
    'Flutter Config' => __DIR__ . '/sim_mobile/lib/core/config/app_config.dart',
];

foreach ($files as $name => $path) {
    if (file_exists($path)) {
        echo "✅ <span class='ok'>{$name}: EXISTS</span> - Modified: " . date('Y-m-d H:i:s', filemtime($path)) . "<br>";
    } else {
        echo "❌ <span class='error'>{$name}: NOT FOUND</span><br>";
    }
}
echo "</div>";

// Test 2: Routes Content
echo "<div class='section'>";
echo "<h2>2. Routes Check</h2>";
$routesFile = __DIR__ . '/sim-pkpps/routes/web.php';
if (file_exists($routesFile)) {
    $content = file_get_contents($routesFile);
    
    $checks = [
        'wali_destroy route' => "name('wali_destroy')",
        'wali_reset_password route' => "name('wali_reset_password')",
        'POST delete method' => "post('wali/{userId}/delete'",
        'POST reset method' => "post('wali/{userId}/reset-password'"
    ];
    
    foreach ($checks as $desc => $needle) {
        if (strpos($content, $needle) !== false) {
            echo "✅ <span class='ok'>{$desc}: FOUND</span><br>";
        } else {
            echo "❌ <span class='error'>{$desc}: NOT FOUND</span><br>";
        }
    }
}
echo "</div>";

// Test 3: View File Check
echo "<div class='section'>";
echo "<h2>3. View File Check (wali_accounts.blade.php)</h2>";
$viewFile = __DIR__ . '/sim-pkpps/resources/views/admin/users/wali_accounts.blade.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    $checks = [
        'Delete button' => "route('admin.users.wali_destroy'",
        'Reset button' => "route('admin.users.wali_reset_password'",
        'CSRF token' => '@csrf',
        'User ID parameter' => '$user->id',
    ];
    
    foreach ($checks as $desc => $needle) {
        if (strpos($content, $needle) !== false) {
            echo "✅ <span class='ok'>{$desc}: FOUND</span><br>";
        } else {
            echo "❌ <span class='error'>{$desc}: NOT FOUND</span><br>";
        }
    }
    
    echo "<br><strong>Last modified:</strong> " . date('Y-m-d H:i:s', filemtime($viewFile));
}
echo "</div>";

// Test 4: Controller Methods
echo "<div class='section'>";
echo "<h2>4. Controller Methods Check</h2>";
$controllerFile = __DIR__ . '/sim-pkpps/app/Http/Controllers/Admin/UserController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    $checks = [
        'destroyAccount method' => 'public function destroyAccount(string $role, string $userId)',
        'resetPassword method' => 'public function resetPassword(string $role, string $userId)',
        'User::findOrFail in destroy' => 'User::findOrFail($userId)',
    ];
    
    foreach ($checks as $desc => $needle) {
        if (strpos($content, $needle) !== false) {
            echo "✅ <span class='ok'>{$desc}: FOUND</span><br>";
        } else {
            echo "❌ <span class='error'>{$desc}: NOT FOUND</span><br>";
        }
    }
}
echo "</div>";

// Test 5: Flutter Config
echo "<div class='section'>";
echo "<h2>5. Flutter Configuration</h2>";
$flutterConfig = __DIR__ . '/sim_mobile/lib/core/config/app_config.dart';
if (file_exists($flutterConfig)) {
    $content = file_get_contents($flutterConfig);
    
    if (strpos($content, 'TugasAkhir/sim-pkpps/public/api/v1') !== false) {
        echo "✅ <span class='ok'>Base URL: CORRECT (includes TugasAkhir path)</span><br>";
    } else {
        echo "❌ <span class='error'>Base URL: INCORRECT (missing TugasAkhir path)</span><br>";
    }
    
    echo "<br><strong>Current URL in file:</strong><br>";
    preg_match('/baseUrl = \'(.+?)\'/s', $content, $matches);
    if (isset($matches[1])) {
        echo "<pre>" . htmlspecialchars($matches[1]) . "</pre>";
    }
}
echo "</div>";

// Test 6: API Test
echo "<div class='section'>";
echo "<h2>6. API Login Test</h2>";
$apiUrl = 'http://localhost/TugasAkhir/sim-pkpps/public/api/v1/login';
$data = json_encode([
    'id_santri' => 'Aydin Fauzan',
    'password' => 's002'
]);

$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\nAccept: application/json\r\n",
        'method'  => 'POST',
        'content' => $data,
        'ignore_errors' => true
    ],
];

$context  = stream_context_create($options);
$result = @file_get_contents($apiUrl, false, $context);

if ($result !== false) {
    $response = json_decode($result, true);
    if (isset($response['success']) && $response['success']) {
        echo "✅ <span class='ok'>API Login: SUCCESS</span><br>";
        echo "<strong>Token:</strong> " . substr($response['token'], 0, 20) . "...<br>";
        echo "<strong>User:</strong> " . $response['user']['name'] . "<br>";
        echo "<strong>Role:</strong> " . $response['user']['role'] . "<br>";
    } else {
        echo "❌ <span class='error'>API Login: FAILED</span><br>";
        echo "<pre>" . htmlspecialchars(print_r($response, true)) . "</pre>";
    }
} else {
    echo "❌ <span class='error'>API: CANNOT CONNECT</span><br>";
}
echo "</div>";

// Test 7: Database Check
echo "<div class='section'>";
echo "<h2>7. Database Wali Accounts</h2>";
$dbFile = __DIR__ . '/sim-pkpps/.env';
if (file_exists($dbFile)) {
    echo "✅ <span class='ok'>.env file exists</span><br>";
    echo "<p>⚠️ Untuk cek database, gunakan phpMyAdmin atau Tinker</p>";
    echo "<pre>php artisan tinker --execute=\"echo App\\Models\\User::where('role','wali')->count();\"</pre>";
} else {
    echo "❌ <span class='error'>.env file not found</span><br>";
}
echo "</div>";

// Summary
echo "<hr><div class='section'>";
echo "<h2>📋 Summary & Next Steps</h2>";
echo "<ol>";
echo "<li><strong>Clear Browser Cache:</strong> Ctrl+Shift+R atau Ctrl+F5</li>";
echo "<li><strong>Login ke Admin:</strong> <a href='http://localhost/TugasAkhir/sim-pkpps/public/admin/login' target='_blank'>Login Admin</a></li>";
echo "<li><strong>Test Wali Accounts:</strong> <a href='http://localhost/TugasAkhir/sim-pkpps/public/admin/users/wali' target='_blank'>Wali Accounts</a></li>";
echo "<li><strong>Flutter:</strong> Hot Restart (bukan Hot Reload)</li>";
echo "<li><strong>Test Login Mobile:</strong> Username=<code>Aydin Fauzan</code>, Password=<code>s002</code></li>";
echo "</ol>";
echo "</div>";

echo "<hr><p><em>Generated at " . date('Y-m-d H:i:s') . "</em></p>";
echo "</body></html>";
?>

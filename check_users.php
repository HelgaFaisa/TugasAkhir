<?php
require __DIR__ . '/sim-pkpps/vendor/autoload.php';
$app = require __DIR__ . '/sim-pkpps/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== USERS IN DATABASE ===\n\n";

$users = \App\Models\User::whereIn('role', ['santri', 'wali'])->get();

if ($users->isEmpty()) {
    echo "❌ No santri/wali users found\n";
    echo "\nCreating test user...\n";
    
    // Create a test wali user
    $santri = \App\Models\Santri::first();
    if ($santri) {
        $user = \App\Models\User::create([
            'username' => 'wali001',
            'name' => 'Wali ' . $santri->nama_lengkap,
            'email' => 'wali001@test.com',
            'password' => bcrypt('S001'),
            'role' => 'wali',
            'role_id' => $santri->id_santri,
        ]);
        echo "✅ Created user: {$user->username} (password: S001)\n";
        echo "   Role: {$user->role}\n";
        echo "   Role ID: {$user->role_id}\n";
    }
} else {
    foreach ($users as $user) {
        echo "Username: {$user->username}\n";
        echo "Role: {$user->role}\n";
        echo "Role ID: {$user->role_id}\n";
        echo "---\n";
    }
}

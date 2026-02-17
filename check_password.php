<?php
require __DIR__ . '/sim-pkpps/vendor/autoload.php';
$app = require __DIR__ . '/sim-pkpps/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$santri = \App\Models\Santri::where('id_santri', 'S001')->first();
if ($santri) {
    echo "Santri: {$santri->nama_lengkap}\n";
    echo "ID: {$santri->id_santri}\n";
    echo "NIS: {$santri->nis}\n";
    
    // Check users
    $users = \App\Models\User::where('role_id', 'S001')->get();
    echo "\nUsers for this santri:\n";
    foreach ($users as $user) {
        echo "  - Username: {$user->username}, Role: {$user->role}\n";
        
        // Test password  
        $testPasswords = ['S001', $santri->nis, '123456', 'password'];
        foreach ($testPasswords as $pass) {
            if (\Illuminate\Support\Facades\Hash::check($pass, $user->password)) {
                echo "    ✅ Password: $pass\n";
                break;
            }
        }
    }
}

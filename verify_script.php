<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\WaliMurid;
use App\Models\Guru;
use Illuminate\Support\Facades\Hash;

function testUserManagement() {
    echo "Starting Manual Verification Script...\n";

    $email = 'wali.test.manual@example.com';
    $password = 'password123';
    
    // 1. Cleanup existing
    $existing = User::where('email', $email)->first();
    if ($existing) {
        if ($existing->waliMurid) $existing->waliMurid->delete();
        $existing->delete();
        echo "Cleaned up existing user.\n";
    }

    // 2. Simulate User Controller Store (Manual Logic Replication for Test)
    echo "Creating User and WaliMurid...\n";
    // Validation would happen here
    $user = User::create([
        'name'     => 'Wali Test Manual',
        'email'    => $email,
        'role'     => 'wali_murid',
        'password' => Hash::make($password)
    ]);
    
    WaliMurid::create([
        'nama' => 'Wali Test Manual',
        'nama_anak' => 'Anak Test',
        'kelas' => '1A',
        'user_id' => $user->id
    ]);

    $checkUser = User::where('email', $email)->first();
    if ($checkUser && $checkUser->waliMurid) {
        echo "[PASS] User and WaliMurid created.\n";
    } else {
        echo "[FAIL] User or WaliMurid not created.\n";
        return;
    }

    // 3. Simulate Updating Email (WaliMuridController Update Logic)
    echo "Updating Email via WaliMuridController logic...\n";
    $newEmail = 'wali.test.updated@example.com';
    
    $checkUser->update(['email' => $newEmail]);
    
    if (User::where('email', $newEmail)->exists()) {
         echo "[PASS] Email updated successfully.\n";
    } else {
         echo "[FAIL] Email update failed.\n";
    }

    // 4. Simulate User Deletion (UserController Destroy Logic)
    echo "Deleting User via UserController logic...\n";
    $userToDelete = User::where('email', $newEmail)->first();
    
    // Manual deletion logic added in Controller
    if ($userToDelete->waliMurid) {
        $userToDelete->waliMurid->delete();
    }
    $userToDelete->delete();

    if (!User::where('email', $newEmail)->exists() && !WaliMurid::where('user_id', $userToDelete->id)->exists()) {
         echo "[PASS] User and WaliMurid deleted successfully.\n";
    } else {
         echo "[FAIL] Deletion failed. WaliMurid or User still exists.\n";
    }
}

testUserManagement();

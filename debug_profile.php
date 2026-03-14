<?php

use App\Models\User;
use App\Models\Division;
use App\Models\Position;
use App\Models\UserProfile;
use Spatie\Permission\Models\Role;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Debugging Profile Persistence...\n";

    $user = User::first();
    if (!$user) {
        // Create dummy user
        $user = User::create([
            'name' => 'Debug User',
            'email' => 'debug@example.com',
            'password' => bcrypt('password')
        ]);
        echo "Created Debug User: {$user->id}\n";
    }

    $division = Division::first();
    if (!$division) {
        $division = Division::create(['name' => 'Debug Div', 'slug' => 'debug-div']);
        echo "Created Dummy Division\n";
    }

    $position = Position::first();
    if (!$position) {
        $position = Position::create(['name' => 'Debug Pos', 'slug' => 'debug-pos']);
        echo "Created Dummy Position\n";
    }

    echo "Using User: {$user->id}, Division: {$division->id}, Position: {$position->id}\n";

    // Attempt UpdateOrCreate
    $profile = $user->profile()->updateOrCreate(
        ['user_id' => $user->id],
        [
            'division_id' => $division->id,
            'position_id' => $position->id,
            'nip' => 'DEBUG123',
            'phone' => '08123456789'
        ]
    );

    echo "Profile Updated/Created. ID: {$profile->id}\n";

    // Refresh and Check
    $profile->refresh();
    echo "Saved Division ID: " . $profile->division_id . "\n";
    echo "Saved Position ID: " . $profile->position_id . "\n";

    if ($profile->division_id == $division->id && $profile->position_id == $position->id) {
        echo "SUCCESS: Data persisted correctly.\n";
    } else {
        echo "FAILURE: Data mismatch.\n";
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

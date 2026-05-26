<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Area;
use App\Models\Donation;
use App\Models\BeneficiaryRequest;
use App\Services\DonationService;
use App\Services\BeneficiaryRequestService;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();
    echo "=== Starting V2 Lifecycle Test ===\n\n";

    // 1. Create Area
    $area = Area::create(['name' => 'منطقة التجربة V2 ' . time(), 'active' => true]);
    echo "[+] Area created: {$area->id}\n";

    // 2. Create Area Admin
    $areaAdmin = User::create([
        'name' => 'أدمن V2', 'email' => 'admin_v2_' . time() . '@example.com',
        'password' => bcrypt('password'), 'role' => 'admin', 'area_id' => $area->id,
    ]);

    // 3. Create Beneficiary Request 1
    $requestService = app(BeneficiaryRequestService::class);
    $req1 = $requestService->create([
        'first_name' => 'محتاج 1', 'phone' => '0500000010', 'area_id' => $area->id,
        'full_address' => 'عنوان 1', 'latitude' => 21.5, 'longitude' => 39.1,
        'social_status' => 'widowed', 'family_members_count' => 5
    ]);
    
    // 4. Create Beneficiary Request 2
    $req2 = $requestService->create([
        'first_name' => 'محتاج 2', 'phone' => '0500000011', 'area_id' => $area->id,
        'full_address' => 'عنوان 2', 'latitude' => 21.5, 'longitude' => 39.1,
        'social_status' => 'married', 'family_members_count' => 2
    ]);

    echo "[+] Created requests: {$req1->code} (Status: {$req1->status}), {$req2->code} (Status: {$req2->status})\n";

    // 5. Donor Donates to Both
    echo "--- Donor creates donation and selects cases ---\n";
    $donationService = app(DonationService::class);
    $donation = $donationService->create([
        'donor_name' => 'متبرع مباشر', 'donor_phone' => '0500000020',
        'donation_scope' => 'own_area', 'donor_area_id' => $area->id,
        'donation_type' => 'meat_kg', 'meat_kg' => 15,
        'selected_cases' => [$req1->id, $req2->id]
    ]);

    echo "[+] Donation created: {$donation->code} (Status: {$donation->status})\n";

    // 6. Check statuses and allocations
    $req1->refresh(); $req2->refresh();
    echo "--- Post-Donation Status Check ---\n";
    echo "Request 1 Status: {$req1->status} (Expected: approved)\n";
    echo "Request 1 Allocations: {$req1->allocations()->count()} (Expected: 1)\n";
    echo "Request 2 Status: {$req2->status} (Expected: approved)\n";
    echo "Request 2 Allocations: {$req2->allocations()->count()} (Expected: 1)\n";
    echo "Donation Allocations: {$donation->allocations()->count()} (Expected: 2)\n";

    // Simulate second donor to req1 to see multiple donations
    $donation2 = $donationService->create([
        'donor_phone' => '0500000021', 'donation_scope' => 'own_area', 'donor_area_id' => $area->id,
        'donation_type' => 'money', 'amount' => 500,
        'selected_cases' => [$req1->id]
    ]);
    $req1->refresh();
    echo "--- Second Donation Check ---\n";
    echo "Request 1 Allocations now: {$req1->allocations()->count()} (Expected: 2)\n";

    echo "\n=== Test V2 Completed Successfully ===\n";
    DB::rollBack();
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n[ERROR] " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

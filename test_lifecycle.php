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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Support\AdminAreaScope;

try {
    DB::beginTransaction();

    echo "=== Starting Lifecycle Test ===\n\n";

    // 1. Create Area
    $area = Area::create([
        'name' => 'منطقة التجربة ' . time(),
        'active' => true,
    ]);
    echo "[+] Area created: {$area->name} (ID: {$area->id})\n";

    // 2. Create Area Admin
    $areaAdmin = User::create([
        'name' => 'أدمن منطقة التجربة',
        'email' => 'admin_test_' . time() . '@example.com',
        'password' => Hash::make('password'),
        'role' => 'admin',
        'area_id' => $area->id,
    ]);

    // 3. Create Agent (Sanad)
    $agent = User::create([
        'name' => 'عضو توزيع (سند)',
        'email' => 'agent_test_' . time() . '@example.com',
        'password' => Hash::make('password'),
        'role' => 'agent',
        'area_id' => $area->id,
    ]);
    echo "[+] Agent created: {$agent->name} (ID: {$agent->id})\n\n";

    // 4. Create Beneficiary Request
    echo "--- Creating Beneficiary Request ---\n";
    $requestService = app(BeneficiaryRequestService::class);
    $beneficiaryRequest = $requestService->create([
        'first_name' => 'محتاج تجريبي',
        'phone' => '0500000002',
        'area_id' => $area->id,
        'full_address' => 'عنوان تجريبي',
        'latitude' => 21.5,
        'longitude' => 39.1,
    ]);
    echo "[+] Request created: {$beneficiaryRequest->code}\n";
    echo "    Assigned Admin ID: " . ($beneficiaryRequest->assigned_admin_id ?? 'None') . " (Expected: {$areaAdmin->id})\n\n";

    // 5. Test assignment by Area Admin to Agent
    echo "--- Testing Admin Assignment to Agent ---\n";
    auth()->login($areaAdmin);
    
    $scope = app(AdminAreaScope::class);
    $availableAgents = $scope->agents()->get();
    echo "[*] Available agents for this admin: " . $availableAgents->count() . "\n";
    
    if ($availableAgents->contains('id', $agent->id)) {
        echo "[+] Agent is correctly visible to Admin.\n";
        
        $requestService->assign($beneficiaryRequest, $agent, 'ملاحظة إدارية للتوصيل');
        
        $beneficiaryRequest->refresh();
        echo "[+] Request Status: {$beneficiaryRequest->status}\n";
        echo "[+] Assigned Agent ID: {$beneficiaryRequest->assigned_agent_id} (Expected: {$agent->id})\n";
        echo "[+] Admin Notes: {$beneficiaryRequest->admin_notes}\n";
    } else {
        echo "[-] Agent NOT visible to Admin.\n";
    }

    echo "\n=== Test Completed Successfully ===\n";
    
    DB::rollBack();
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n[ERROR] " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

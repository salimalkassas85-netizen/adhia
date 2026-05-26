<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\BeneficiaryRequest;
use App\Models\Donation;
use App\Models\StatusLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EidInitiativeTest extends TestCase
{
    use RefreshDatabase;

    public function test_beneficiary_can_submit_request_with_location(): void
    {
        $area = Area::create(['name' => 'الحي الأول']);

        $response = $this->post('/request-gift', [
            'first_name' => 'أحمد',
            'phone' => '0500000000',
            'area_id' => $area->id,
            'family_members_count' => 5,
            'has_children' => 1,
            'full_address' => 'شارع المسجد',
            'landmark' => 'قرب المركز',
            'latitude' => '21.4225000',
            'longitude' => '39.8262000',
            'location_accuracy' => 15,
        ]);

        $request = BeneficiaryRequest::first();
        $response->assertRedirect(route('public.request.success', $request->code));
        $this->assertDatabaseHas('beneficiary_requests', ['first_name' => 'أحمد', 'latitude' => 21.4225]);
    }

    public function test_success_page_does_not_expose_location(): void
    {
        $request = $this->beneficiaryRequest();

        $this->get(route('public.request.success', $request->code))
            ->assertOk()
            ->assertSee($request->code)
            ->assertDontSee((string) $request->latitude)
            ->assertDontSee($request->full_address);
    }

    public function test_donor_can_submit_donation(): void
    {
        $area = Area::create(['name' => 'الحي الأول']);

        $response = $this->post('/donate', [
            'donor_phone' => '0550000000',
            'donor_area_id' => $area->id,
            'target_area_id' => $area->id,
            'donation_scope' => 'selected_area',
            'donation_type' => 'money',
            'amount' => 500,
        ]);

        $donation = Donation::first();
        $response->assertRedirect(route('public.donation.success', $donation->code));
        $this->assertDatabaseHas('donations', ['donor_phone' => '0550000000', 'amount' => 500]);
    }

    public function test_donor_cannot_see_beneficiaries(): void
    {
        $request = $this->beneficiaryRequest();

        $this->get('/donate')
            ->assertOk()
            ->assertDontSee($request->first_name)
            ->assertDontSee($request->phone);
    }

    public function test_admin_can_see_all_requests_and_donations(): void
    {
        $admin = $this->user('admin', true);
        $request = $this->beneficiaryRequest();
        $donation = Donation::create([
            'code' => 'DON-TEST',
            'donor_phone' => '055',
            'donation_scope' => 'most_needed',
            'donation_type' => 'money',
            'amount' => 100,
        ]);

        $this->actingAs($admin)->get(route('admin.beneficiary-requests.index'))->assertSee($request->code);
        $this->actingAs($admin)->get(route('admin.donations.index'))->assertSee($donation->code);
    }

    public function test_agent_must_accept_pledge_before_dashboard(): void
    {
        $agent = $this->user('agent', false);

        $this->actingAs($agent)->get(route('agent.dashboard'))
            ->assertRedirect(route('pledge.show'));
    }

    public function test_pledge_acceptance_stores_timestamp(): void
    {
        $agent = $this->user('agent', false);

        $this->actingAs($agent)->post(route('pledge.accept'))
            ->assertRedirect(route('agent.dashboard'));

        $this->assertNotNull($agent->fresh()->pledge_accepted_at);
    }

    public function test_agent_sees_only_assigned_requests(): void
    {
        $agent = $this->user('agent', true);
        $assigned = $this->beneficiaryRequest(['assigned_agent_id' => $agent->id, 'status' => 'assigned']);
        $other = $this->beneficiaryRequest(['code' => 'GIFT-OTHER']);

        $this->actingAs($agent)->get(route('agent.requests.index'))
            ->assertSee($assigned->code)
            ->assertDontSee($other->code);
    }

    public function test_agent_cannot_open_unassigned_request(): void
    {
        $agent = $this->user('agent', true);
        $request = $this->beneficiaryRequest();

        $this->actingAs($agent)->get(route('agent.requests.show', $request))->assertForbidden();
    }

    public function test_admin_can_assign_request_to_agent(): void
    {
        $admin = $this->user('admin', true);
        $agent = $this->user('agent', true);
        $request = $this->beneficiaryRequest();

        $this->actingAs($admin)->post(route('admin.beneficiary-requests.assign', $request), [
            'assigned_agent_id' => $agent->id,
        ])->assertRedirect();

        $this->assertSame($agent->id, $request->fresh()->assigned_agent_id);
    }

    public function test_status_logs_are_created(): void
    {
        $admin = $this->user('admin', true);
        $request = $this->beneficiaryRequest();

        $this->actingAs($admin)->post(route('admin.beneficiary-requests.approve', $request))->assertRedirect();

        $this->assertSame(1, StatusLog::count());
        $this->assertSame('approved', StatusLog::first()->to_status);
    }

    public function test_delivered_status_sets_delivered_at(): void
    {
        $agent = $this->user('agent', true);
        $request = $this->beneficiaryRequest(['assigned_agent_id' => $agent->id, 'status' => 'assigned']);

        $this->actingAs($agent)->post(route('agent.requests.status', $request), [
            'status' => 'delivered',
            'note' => 'تم التسليم بحمد الله',
        ])->assertRedirect();

        $this->assertNotNull($request->fresh()->delivered_at);
    }

    public function test_agent_can_see_assigned_donor_pickup_location(): void
    {
        $admin = $this->user('admin', true);
        $agent = $this->user('agent', true);
        $donation = Donation::create([
            'code' => 'DON-PICKUP',
            'donor_name' => 'فاعل خير',
            'donor_phone' => '055',
            'donation_scope' => 'most_needed',
            'donation_type' => 'meat_kg',
            'meat_kg' => 20,
            'pickup_address' => 'موقع الاستلام',
            'latitude' => 21.5,
            'longitude' => 39.9,
        ]);

        $this->actingAs($admin)->post(route('admin.donations.assign-pickup', $donation), [
            'pickup_agent_id' => $agent->id,
        ])->assertRedirect();

        $this->actingAs($agent)->get(route('agent.pickups.show', $donation))
            ->assertOk()
            ->assertSee('موقع الاستلام')
            ->assertSee('OpenStreetMap');
    }

    public function test_agent_cannot_see_unassigned_donor_pickup(): void
    {
        $agent = $this->user('agent', true);
        $donation = Donation::create([
            'code' => 'DON-PRIVATE',
            'donor_phone' => '055',
            'donation_scope' => 'most_needed',
            'donation_type' => 'money',
            'amount' => 100,
            'latitude' => 21.5,
            'longitude' => 39.9,
        ]);

        $this->actingAs($agent)->get(route('agent.pickups.show', $donation))->assertForbidden();
    }

    public function test_area_admin_sees_only_area_requests_and_donations(): void
    {
        $area = Area::create(['name' => 'منطقة الأدمن']);
        $otherArea = Area::create(['name' => 'منطقة أخرى']);
        $admin = $this->user('admin', true);
        $admin->update(['area_id' => $area->id]);

        $ownRequest = $this->beneficiaryRequest(['code' => 'GIFT-AREA', 'area_id' => $area->id]);
        $otherRequest = $this->beneficiaryRequest(['code' => 'GIFT-OTHER-AREA', 'area_id' => $otherArea->id]);
        $ownDonation = Donation::create([
            'code' => 'DON-AREA',
            'donor_phone' => '055',
            'donor_area_id' => $area->id,
            'target_area_id' => $area->id,
            'donation_scope' => 'selected_area',
            'donation_type' => 'money',
            'amount' => 100,
        ]);
        $otherDonation = Donation::create([
            'code' => 'DON-OTHER-AREA',
            'donor_phone' => '055',
            'donor_area_id' => $otherArea->id,
            'target_area_id' => $otherArea->id,
            'donation_scope' => 'selected_area',
            'donation_type' => 'money',
            'amount' => 100,
        ]);

        $this->actingAs($admin)->get(route('admin.beneficiary-requests.index'))
            ->assertSee($ownRequest->code)
            ->assertDontSee($otherRequest->code);

        $this->actingAs($admin)->get(route('admin.donations.index'))
            ->assertSee($ownDonation->code)
            ->assertDontSee($otherDonation->code);
    }

    public function test_area_admin_cannot_open_other_area_request_or_donation(): void
    {
        $area = Area::create(['name' => 'منطقة الأدمن']);
        $otherArea = Area::create(['name' => 'منطقة أخرى']);
        $admin = $this->user('admin', true);
        $admin->update(['area_id' => $area->id]);

        $otherRequest = $this->beneficiaryRequest(['area_id' => $otherArea->id]);
        $otherDonation = Donation::create([
            'code' => 'DON-FORBIDDEN',
            'donor_phone' => '055',
            'target_area_id' => $otherArea->id,
            'donation_scope' => 'selected_area',
            'donation_type' => 'money',
            'amount' => 100,
        ]);

        $this->actingAs($admin)->get(route('admin.beneficiary-requests.show', $otherRequest))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.donations.show', $otherDonation))->assertForbidden();
    }

    private function user(string $role, bool $pledged): User
    {
        return User::factory()->create([
            'role' => $role,
            'pledge_accepted_at' => $pledged ? now() : null,
        ]);
    }

    private function beneficiaryRequest(array $overrides = []): BeneficiaryRequest
    {
        $area = Area::first() ?? Area::create(['name' => 'الحي الأول']);

        return BeneficiaryRequest::create(array_merge([
            'code' => 'GIFT-TEST-'.BeneficiaryRequest::count(),
            'first_name' => 'سالم',
            'phone' => '0500000000',
            'area_id' => $area->id,
            'family_members_count' => 4,
            'full_address' => 'عنوان خاص',
            'latitude' => 21.4225,
            'longitude' => 39.8262,
            'status' => 'pending',
        ], $overrides));
    }
}

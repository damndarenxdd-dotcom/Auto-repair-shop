<?php

namespace Tests\Feature;

use App\Models\RepairJob;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobOrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_job_order_api(): void
    {
        $response = $this->getJson('/api/job-orders');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_list_job_orders(): void
    {
        $customerRole = Role::firstOrCreate(['name' => 'customer'], ['description' => 'Customer']);
        $customer = User::factory()->create([
            'role_id' => $customerRole->id,
        ]);

        $this->actingAs($customer);

        RepairJob::create([
            'customer_id' => $customer->id,
            'vehicle_model' => 'Toyota Corolla',
            'license_plate' => 'ABC-123',
            'year' => 2022,
            'description' => 'Engine light inspection',
            'status' => 'pending',
        ]);

        $response = $this->getJson('/api/job-orders');

        $response->assertOk()
            ->assertJsonPath('data.0.vehicle_model', 'Toyota Corolla');
    }

    public function test_authenticated_user_can_create_job_order(): void
    {
        $customerRole = Role::firstOrCreate(['name' => 'customer'], ['description' => 'Customer']);
        $mechanicRole = Role::firstOrCreate(['name' => 'mechanic'], ['description' => 'Mechanic']);

        $customer = User::factory()->create(['role_id' => $customerRole->id]);
        $mechanic = User::factory()->create(['role_id' => $mechanicRole->id]);

        $this->actingAs($customer);

        $response = $this->postJson('/api/job-orders', [
            'customer_id' => $customer->id,
            'mechanic_id' => $mechanic->id,
            'vehicle_model' => 'Honda Civic',
            'license_plate' => 'XYZ-987',
            'year' => 2021,
            'description' => 'Brake pads and rotor check',
            'status' => 'assigned',
            'estimated_cost' => 320.50,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.vehicle_model', 'Honda Civic')
            ->assertJsonPath('data.customer_id', $customer->id);

        $this->assertDatabaseHas('repair_jobs', [
            'license_plate' => 'XYZ-987',
            'status' => 'assigned',
        ]);
    }

    public function test_authenticated_user_can_sync_job_order_services(): void
    {
        $customerRole = Role::firstOrCreate(['name' => 'customer'], ['description' => 'Customer']);
        $mechanicRole = Role::firstOrCreate(['name' => 'mechanic'], ['description' => 'Mechanic']);

        $customer = User::factory()->create(['role_id' => $customerRole->id]);
        $mechanic = User::factory()->create(['role_id' => $mechanicRole->id]);
        $service = Service::create([
            'name' => 'Oil Change',
            'description' => 'Engine oil and filter replacement',
            'price' => 89.99,
            'estimated_hours' => 1,
            'is_active' => true,
        ]);

        $job = RepairJob::create([
            'customer_id' => $customer->id,
            'mechanic_id' => $mechanic->id,
            'vehicle_model' => 'Ford Focus',
            'license_plate' => 'LMN-456',
            'year' => 2020,
            'description' => 'Routine maintenance',
            'status' => 'assigned',
        ]);

        $this->actingAs($customer);

        $response = $this->patchJson('/api/job-orders/'.$job->id.'/services', [
            'services' => [[
                'service_id' => $service->id,
                'quantity' => 2,
                'unit_price' => 89.99,
            ]],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.services.0.id', $service->id)
            ->assertJsonPath('data.services.0.quantity', 2);

        $this->assertDatabaseHas('repair_job_services', [
            'repair_job_id' => $job->id,
            'service_id' => $service->id,
            'quantity' => 2,
        ]);
    }
}

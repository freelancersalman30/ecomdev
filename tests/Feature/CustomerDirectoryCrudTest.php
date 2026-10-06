<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerDirectoryCrudTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            ['name' => 'Dreamers Admin', 'password' => Hash::make('password')]
        );
        $this->actingAs($this->admin, 'web');
    }

    public function test_admin_can_view_customer_directory_and_search(): void
    {
        $customer = Customer::create([
            'name' => 'Kabila Khan',
            'phone' => '01711002233',
            'email' => 'kabila@example.com',
            'city' => 'Chittagong',
            'is_active' => true,
        ]);

        $response = $this->get(route('admin.customers.index', ['search' => 'Kabila']));
        $response->assertStatus(200);
        $response->assertSee('Kabila Khan');
        $response->assertSee('01711002233');
    }

    public function test_admin_can_create_new_customer(): void
    {
        $payload = [
            'name' => 'Salim Mia',
            'phone' => '01988776655',
            'email' => 'salim@example.com',
            'password' => 'secret123',
            'address' => 'Mirpur 10, Block C',
            'city' => 'Dhaka',
            'postal_code' => '1216',
            'loyalty_points' => 150,
            'is_flagged_fraud' => 1,
            'fraud_reason' => 'Multiple return history recorded',
            'notes' => 'Requires prepaid delivery verification',
        ];

        $response = $this->post(route('admin.customers.store'), $payload);
        $response->assertRedirect(route('admin.customers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('customers', [
            'name' => 'Salim Mia',
            'phone' => '01988776655',
            'email' => 'salim@example.com',
            'city' => 'Dhaka',
            'loyalty_points' => 150,
            'is_flagged_fraud' => 1,
            'fraud_reason' => 'Multiple return history recorded',
            'notes' => 'Requires prepaid delivery verification',
        ]);
    }

    public function test_admin_can_view_customer_profile(): void
    {
        $customer = Customer::create([
            'name' => 'Habibullah Rahman',
            'phone' => '01655443322',
            'email' => 'habib@example.com',
            'address' => 'Banani Road 11',
            'city' => 'Dhaka',
            'is_active' => true,
        ]);

        $response = $this->get(route('admin.customers.show', $customer->id));
        $response->assertStatus(200);
        $response->assertSee('Habibullah Rahman');
        $response->assertSee('01655443322');
        $response->assertSee('Banani Road 11');
    }

    public function test_admin_can_update_customer(): void
    {
        $customer = Customer::create([
            'name' => 'Original Customer Name',
            'phone' => '01511223344',
            'email' => 'original@example.com',
            'address' => 'Old Address',
            'city' => 'Sylhet',
            'is_active' => true,
        ]);

        $updatePayload = [
            'name' => 'Updated Customer Name',
            'phone' => '01511223344',
            'email' => 'updated@example.com',
            'address' => 'New Address 45',
            'city' => 'Dhaka',
            'loyalty_points' => 50,
            'is_flagged_fraud' => 0,
            'notes' => 'Upgraded customer profile',
        ];

        $response = $this->put(route('admin.customers.update', $customer->id), $updatePayload);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Updated Customer Name',
            'email' => 'updated@example.com',
            'address' => 'New Address 45',
            'city' => 'Dhaka',
            'loyalty_points' => 50,
            'is_flagged_fraud' => 0,
            'notes' => 'Upgraded customer profile',
        ]);
    }

    public function test_admin_can_soft_delete_customer(): void
    {
        $customer = Customer::create([
            'name' => 'John Doe Test',
            'phone' => '01899112233',
            'email' => 'johndoe@test.com',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'deleted_at' => null,
        ]);

        $response = $this->delete(route('admin.customers.destroy', $customer->id));
        $response->assertRedirect(route('admin.customers.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('customers', [
            'id' => $customer->id,
        ]);
    }
}

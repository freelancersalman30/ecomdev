<?php

namespace Tests\Feature;

use App\Models\DeliveryMethod;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDeliveryManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            ['name' => 'Dreamers Admin', 'password' => Hash::make('password')]
        );
        $this->actingAs($admin, 'web');
    }

    /**
     * Test admin delivery management page renders successfully.
     */
    public function test_admin_delivery_page_renders(): void
    {
        $response = $this->get('/admin/delivery');
        $response->assertStatus(200);
        $response->assertSee('Delivery Zones & Shipping Charges');
    }

    /**
     * Test admin can create a new delivery zone.
     */
    public function test_admin_can_create_delivery_zone(): void
    {
        $response = $this->post('/admin/delivery', [
            'name' => 'Sylhet Metro Express',
            'code' => 'sylhet_metro',
            'charge' => 110.00,
            'estimated_days' => '1-2 Days',
            'min_order_for_free_delivery' => 3000.00,
            'description' => 'Fast delivery inside Sylhet metropolitan.',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('delivery_methods', [
            'code' => 'sylhet_metro',
            'charge' => 110.00,
        ]);
    }

    /**
     * Test admin can update an existing delivery zone.
     */
    public function test_admin_can_update_delivery_zone(): void
    {
        $zone = DeliveryMethod::firstOrCreate(
            ['code' => 'khulna_zone'],
            [
                'name' => 'Khulna Zone',
                'charge' => 120.00,
                'estimated_days' => '2-3 Days',
                'is_active' => true,
            ]
        );

        $response = $this->put("/admin/delivery/{$zone->id}", [
            'name' => 'Khulna Divisional Zone',
            'code' => 'khulna_zone',
            'charge' => 125.00,
            'estimated_days' => '2-4 Days',
            'min_order_for_free_delivery' => 2000.00,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('delivery_methods', [
            'id' => $zone->id,
            'name' => 'Khulna Divisional Zone',
            'charge' => 125.00,
        ]);
    }

    /**
     * Test admin can toggle delivery method status.
     */
    public function test_admin_can_toggle_delivery_status(): void
    {
        $zone = DeliveryMethod::firstOrCreate(
            ['code' => 'rajshahi_zone'],
            [
                'name' => 'Rajshahi Zone',
                'charge' => 130.00,
                'estimated_days' => '2-3 Days',
                'is_active' => true,
            ]
        );

        $response = $this->post("/admin/delivery/{$zone->id}/toggle");
        $response->assertRedirect();

        $zone->refresh();
        $this->assertFalse($zone->is_active);
    }

    /**
     * Test admin can set default delivery zone.
     */
    public function test_admin_can_set_default_delivery_zone(): void
    {
        $zone = DeliveryMethod::firstOrCreate(
            ['code' => 'barisal_zone'],
            [
                'name' => 'Barisal Zone',
                'charge' => 140.00,
                'estimated_days' => '3-4 Days',
                'is_active' => true,
                'is_default' => false,
            ]
        );

        $response = $this->post("/admin/delivery/{$zone->id}/default");
        $response->assertRedirect();

        $zone->refresh();
        $this->assertTrue($zone->is_default);
    }

    /**
     * Test admin can update global shipping rules.
     */
    public function test_admin_can_update_global_shipping_rules(): void
    {
        $response = $this->post('/admin/delivery/global-rules', [
            'free_shipping_threshold' => 5000.00,
            'default_courier_partner' => 'steadfast',
            'delivery_information_notice' => 'Tested courier dispatch notice.',
            'footer_courier_partners' => 'Steadfast • Pathao',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('5000', Setting::get('free_shipping_threshold'));
    }
}

<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Models\Warranty;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WarrantyVerificationTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            ['name' => 'Dreamers Admin', 'password' => Hash::make('password')]
        );
    }

    public function test_admin_sidebar_and_warranty_index_renders(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.warranties.index'));

        $response->assertStatus(200);
        $response->assertSee('Warranty Verification');
        $response->assertSee('Total Issued');
        $response->assertSee('Active Covered');
        $response->assertSee('Expiring Soon');
    }

    public function test_warranty_model_calculates_exact_remaining_days_correctly(): void
    {
        $product = Product::first();
        $this->assertNotNull($product);

        // 1. Active warranty with 100 days remaining
        $activeWarranty = Warranty::create([
            'warranty_code' => Warranty::generateCode(),
            'product_id' => $product->id,
            'customer_name' => 'Test Customer',
            'customer_phone' => '01700000000',
            'warranty_period' => '1 Year',
            'warranty_days' => 365,
            'start_date' => Carbon::now()->subDays(265)->toDateString(),
            'end_date' => Carbon::now()->addDays(100)->toDateString(),
            'status' => 'active',
        ]);

        $this->assertTrue($activeWarranty->is_valid);
        $this->assertFalse($activeWarranty->is_expired);
        $this->assertEquals(100, $activeWarranty->remaining_days);

        // 2. Expired warranty
        $expiredWarranty = Warranty::create([
            'warranty_code' => Warranty::generateCode(),
            'product_id' => $product->id,
            'customer_name' => 'Expired Customer',
            'customer_phone' => '01700000001',
            'warranty_period' => '6 Months',
            'warranty_days' => 180,
            'start_date' => Carbon::now()->subDays(200)->toDateString(),
            'end_date' => Carbon::now()->subDays(20)->toDateString(),
            'status' => 'active',
        ]);

        $this->assertEquals(0, $expiredWarranty->remaining_days);
        $this->assertTrue($expiredWarranty->is_expired);
        $this->assertFalse($expiredWarranty->is_valid);

        // 3. Voided warranty
        $voidedWarranty = Warranty::create([
            'warranty_code' => Warranty::generateCode(),
            'product_id' => $product->id,
            'customer_name' => 'Voided Customer',
            'customer_phone' => '01700000002',
            'warranty_period' => '1 Year',
            'warranty_days' => 365,
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->addDays(365)->toDateString(),
            'status' => 'voided',
        ]);

        $this->assertEquals(0, $voidedWarranty->remaining_days);
        $this->assertFalse($voidedWarranty->is_valid);
    }

    public function test_admin_can_register_manual_warranty(): void
    {
        $product = Product::first();
        $this->assertNotNull($product);

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.warranties.store'), [
            'product_id' => $product->id,
            'customer_name' => 'Test Hardware Engineer',
            'customer_phone' => '01899112233',
            'customer_email' => 'engineer@example.com',
            'serial_number' => 'SN-MANUAL-001',
            'warranty_period' => '2 Years Official',
            'warranty_days' => 730,
            'start_date' => Carbon::now()->toDateString(),
            'status' => 'active',
            'admin_notes' => 'Tested on workbench before handover',
        ]);

        $response->assertRedirect(route('admin.warranties.index'));

        $this->assertDatabaseHas('product_warranties', [
            'serial_number' => 'SN-MANUAL-001',
            'customer_name' => 'Test Hardware Engineer',
            'customer_phone' => '01899112233',
            'warranty_days' => 730,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_update_warranty_and_extend_date(): void
    {
        $warranty = Warranty::first();
        $this->assertNotNull($warranty);

        $newEndDate = Carbon::now()->addDays(500)->toDateString();

        $response = $this->actingAs($this->admin, 'web')->put(route('admin.warranties.update', $warranty->id), [
            'serial_number' => 'SN-UPDATED-999',
            'status' => 'claimed',
            'end_date' => $newEndDate,
            'claim_notes' => 'Replaced capacitor and serviced.',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('product_warranties', [
            'id' => $warranty->id,
            'serial_number' => 'SN-UPDATED-999',
            'status' => 'claimed',
            'claim_notes' => 'Replaced capacitor and serviced.',
        ]);
    }

    public function test_customer_can_view_product_warranties_with_remaining_days(): void
    {
        $customer = Customer::firstOrCreate(
            ['phone' => '01799887766'],
            ['name' => 'Customer Tester', 'email' => 'customer_test@example.com', 'password' => Hash::make('password')]
        );

        $product = Product::first();

        // Create a warranty for this customer
        $warranty = Warranty::create([
            'warranty_code' => Warranty::generateCode(),
            'serial_number' => 'SN-CUST-1234',
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'warranty_period' => '1 Year Official Warranty',
            'warranty_days' => 365,
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->addDays(365)->toDateString(),
            'status' => 'active',
        ]);

        // 1. Check Customer Warranty Hub
        $response = $this->actingAs($customer, 'customer')->get(route('customer.warranties'));
        $response->assertStatus(200);
        $response->assertSee($warranty->warranty_code);
        $response->assertSee('Days Left');
        $response->assertSee($product->name);

        // 2. Check Customer Dashboard Widget
        $dashResponse = $this->actingAs($customer, 'customer')->get(route('customer.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Active Product Warranties');
        $dashResponse->assertSee('Days Remaining');
    }

    public function test_public_warranty_verification_by_code(): void
    {
        $warranty = Warranty::first();
        $this->assertNotNull($warranty);

        $response = $this->get(route('warranty.verify', ['code' => $warranty->warranty_code]));
        $response->assertStatus(200);
        $response->assertSee('Verified Genuine Hardware');
        $response->assertSee($warranty->warranty_code);
        $response->assertSee('Remaining Warranty');
    }

    public function test_order_creation_automatically_generates_warranties(): void
    {
        $product = Product::first();
        $orderService = app(OrderService::class);

        $order = $orderService->createOrder([
            'shipping_name' => 'Order Test Customer',
            'shipping_phone' => '01712345678',
            'shipping_address' => 'Mirpur DOHS, Dhaka',
            'shipping_city' => 'Dhaka',
            'payment_method' => 'cod',
            'order_type' => 'online',
        ], [
            [
                'product_id' => $product->id,
                'quantity' => 1,
            ],
        ]);

        $this->assertNotNull($order);
        $this->assertGreaterThan(0, $order->warranties()->count());

        $firstWarranty = $order->warranties()->first();
        $this->assertStringStartsWith('WAR-', $firstWarranty->warranty_code);
        $this->assertGreaterThan(0, $firstWarranty->remaining_days);
    }
}

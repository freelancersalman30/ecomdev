<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPosCustomerOrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            ['name' => 'Dreamers Admin', 'password' => Hash::make('password')]
        );

        Account::firstOrCreate(
            ['id' => 1],
            ['name' => 'Cash Counter Account', 'account_type' => 'cash', 'account_number' => 'POS-01', 'is_active' => true]
        );

        $category = Category::firstOrCreate(
            ['slug' => 'pos-test-cat'],
            ['name' => 'POS Testing Category', 'is_active' => true]
        );

        $this->product = Product::firstOrCreate(
            ['sku' => 'ESP32-DEV-TEST'],
            [
                'category_id' => $category->id,
                'name' => 'ESP32 Development Board',
                'slug' => 'esp32-dev-test',
                'purchase_price' => 500,
                'selling_price' => 750,
                'stock_quantity' => 50,
                'is_active' => true,
            ]
        );
    }

    public function test_pos_index_page_renders_manual_customer_input_fields(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pos.index'));

        $response->assertStatus(200);
        $response->assertSee('Customer Details');
        $response->assertSee('Manual Entry');
        $response->assertSee('Customer Address (Street / Area / House)');
        $response->assertSee('Order Notes / Remarks (Optional)');
    }

    public function test_pos_checkout_with_manual_customer_details_creates_order_and_customer(): void
    {
        $payload = [
            'cart' => [
                [
                    'product_id' => $this->product->id,
                    'variant_id' => null,
                    'quantity' => 2,
                ],
            ],
            'discount' => 50,
            'tax' => 0,
            'paid_amount' => 1450,
            'payment_method' => 'pos_cash',
            'customer_name' => 'Md. Salman Chowdhury',
            'customer_phone' => '01812345678',
            'customer_address' => 'House #42, Road #7, Dhanmondi, Dhaka',
            'customer_city' => 'Dhaka',
            'customer_email' => 'salman@example.com',
            'customer_note' => 'Deliver with test certificate and warranty card',
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.pos.checkout'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $orderId = $response->json('order_id');
        $order = Order::find($orderId);

        $this->assertNotNull($order);
        $this->assertEquals('Md. Salman Chowdhury', $order->shipping_name);
        $this->assertEquals('01812345678', $order->shipping_phone);
        $this->assertEquals('House #42, Road #7, Dhanmondi, Dhaka', $order->shipping_address);
        $this->assertEquals('Dhaka', $order->shipping_city);
        $this->assertEquals('salman@example.com', $order->shipping_email);
        $this->assertEquals('Deliver with test certificate and warranty card', $order->customer_note);

        // Verify customer was created in CRM
        $customer = Customer::where('phone', '01812345678')->first();
        $this->assertNotNull($customer);
        $this->assertEquals('Md. Salman Chowdhury', $customer->name);
        $this->assertEquals('House #42, Road #7, Dhanmondi, Dhaka', $customer->address);
    }

    public function test_pos_receipt_displays_manual_customer_name_phone_and_address(): void
    {
        $order = Order::create([
            'order_no' => 'POS-TEST-12345',
            'order_type' => 'pos',
            'status' => 'completed',
            'subtotal' => 1500,
            'discount' => 50,
            'tax' => 0,
            'grand_total' => 1450,
            'paid_amount' => 1450,
            'due_amount' => 0,
            'payment_method' => 'pos_cash',
            'shipping_name' => 'Engr. Tanvir Ahmed',
            'shipping_phone' => '01799887766',
            'shipping_address' => 'Mirpur DOHS, Road 12, House 5',
            'shipping_city' => 'Dhaka',
            'customer_note' => 'Special PCB customer with warranty',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.pos.receipt', $order->id));

        $response->assertStatus(200);
        $response->assertSee('Engr. Tanvir Ahmed');
        $response->assertSee('01799887766');
        $response->assertSee('Mirpur DOHS, Road 12, House 5');
        $response->assertSee('Special PCB customer with warranty');
    }

    public function test_pos_checkout_with_delivery_charge_and_advance_paid(): void
    {
        $payload = [
            'cart' => [
                [
                    'product_id' => $this->product->id,
                    'variant_id' => null,
                    'quantity' => 2, // 750 * 2 = 1500
                ],
            ],
            'discount' => 50,
            'shipping_charge' => 120,
            'advance_paid' => 500,
            'paid_amount' => 500,
            'payment_method' => 'pos_cash',
            'customer_name' => 'Kazi Nazrul',
            'customer_phone' => '01911223344',
            'customer_address' => 'Chittagong Port Area',
            'customer_city' => 'Chittagong',
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.pos.checkout'), $payload);

        $response->assertStatus(200);
        $orderId = $response->json('order_id');
        $order = Order::find($orderId);

        $this->assertNotNull($order);
        $this->assertEquals(1500, $order->subtotal);
        $this->assertEquals(50, $order->discount);
        $this->assertEquals(120, $order->shipping_charge);
        $this->assertEquals(1570, $order->grand_total);
        $this->assertEquals(500, $order->paid_amount);
        $this->assertEquals(1070, $order->due_amount);
        $this->assertEquals('partially_paid', $order->payment_status);

        // Verify receipt shows Delivery Charge and Advance Paid / Due Balance
        $receiptResponse = $this->actingAs($this->admin)->get(route('admin.pos.receipt', $order->id));
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee('Delivery Charge:');
        $receiptResponse->assertSee('120.00');
        $receiptResponse->assertSee('Advance Paid');
        $receiptResponse->assertSee('500.00');
        $receiptResponse->assertSee('Due Balance:');
        $receiptResponse->assertSee('1,070.00');
    }

    public function test_pos_checkout_with_walk_in_customer_defaults_properly(): void
    {
        $payload = [
            'cart' => [
                [
                    'product_id' => $this->product->id,
                    'variant_id' => null,
                    'quantity' => 1,
                ],
            ],
            'discount' => 0,
            'tax' => 0,
            'paid_amount' => 750,
            'payment_method' => 'pos_cash',
            'customer_name' => null,
            'customer_phone' => null,
            'customer_address' => null,
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.pos.checkout'), $payload);

        $response->assertStatus(200);
        $orderId = $response->json('order_id');
        $order = Order::find($orderId);

        $this->assertEquals('Walk-in Customer', $order->shipping_name);
        $this->assertEquals('01700000000', $order->shipping_phone);
        $this->assertEquals('Store Counter - DREAMERS PCB', $order->shipping_address);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IncompleteOrderCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected Product $product;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            ['name' => 'Dreamers Admin', 'password' => Hash::make('password')]
        );

        $category = Category::firstOrCreate(
            ['slug' => 'microcontrollers'],
            ['name' => 'Microcontrollers', 'is_active' => true]
        );

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'ESP32 Development Board',
            'slug' => 'esp32-development-board',
            'sku' => 'ESP32-DEV-01',
            'purchase_price' => 300,
            'selling_price' => 550,
            'stock_quantity' => 50,
            'is_active' => true,
        ]);
    }

    public function test_save_incomplete_order_captures_drop_off_data(): void
    {
        // 1. Put item in cart session
        $cartKey = $this->product->id.'_';
        $sessionData = [
            'cart' => [
                $cartKey => [
                    'product_id' => $this->product->id,
                    'variant_id' => null,
                    'quantity' => 2,
                    'subtotal' => 1100,
                ],
            ],
        ];

        // 2. Call AJAX save-incomplete
        $response = $this->withSession($sessionData)->postJson(route('checkout.save_incomplete'), [
            'shipping_name' => 'Tanvir Hasan',
            'shipping_phone' => '01812345678',
            'shipping_email' => 'tanvir@example.com',
            'shipping_city' => 'Chittagong',
            'shipping_address' => 'GEC Circle, Chittagong',
            'shipping_area' => 'outside_dhaka',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        // Assert order was saved in database with status 'incomplete'
        $this->assertDatabaseHas('orders', [
            'status' => 'incomplete',
            'shipping_name' => 'Tanvir Hasan',
            'shipping_phone' => '01812345678',
            'shipping_email' => 'tanvir@example.com',
            'shipping_city' => 'Chittagong',
        ]);

        // Stock quantity should NOT be deducted for incomplete drop-offs
        $this->assertEquals(50, $this->product->fresh()->stock_quantity);
    }

    public function test_checkout_process_converts_incomplete_order_to_pending(): void
    {
        $cartKey = $this->product->id.'_';
        $sessionData = [
            'cart' => [
                $cartKey => [
                    'product_id' => $this->product->id,
                    'variant_id' => null,
                    'quantity' => 3,
                    'subtotal' => 1650,
                ],
            ],
        ];

        // 1. Customer drops contact info
        $this->withSession($sessionData)->postJson(route('checkout.save_incomplete'), [
            'shipping_name' => 'Salman Chowdhury',
            'shipping_phone' => '01711223344',
            'shipping_address' => 'Mirpur-10, Dhaka',
            'shipping_city' => 'Dhaka',
            'shipping_area' => 'inside_dhaka',
        ]);

        $incompleteOrder = Order::where('shipping_phone', '01711223344')->where('status', 'incomplete')->first();
        $this->assertNotNull($incompleteOrder);

        // 2. Customer finally submits the checkout form
        $response = $this->withSession(array_merge($sessionData, [
            'incomplete_order_id' => $incompleteOrder->id,
        ]))->post(route('checkout.process'), [
            'shipping_name' => 'Salman Chowdhury',
            'shipping_phone' => '01711223344',
            'shipping_email' => 'salman@example.com',
            'shipping_address' => 'House 12, Road 5, Mirpur-10',
            'shipping_city' => 'Dhaka',
            'shipping_area' => 'inside_dhaka',
            'payment_method' => 'cash_on_delivery',
        ]);

        $response->assertRedirect(route('checkout.success', $incompleteOrder->order_no));

        // Incomplete order should now be 'pending'
        $this->assertDatabaseHas('orders', [
            'id' => $incompleteOrder->id,
            'status' => 'pending',
            'shipping_email' => 'salman@example.com',
        ]);

        // Stock quantity SHOULD be deducted upon confirmed checkout
        $this->assertEquals(47, $this->product->fresh()->stock_quantity);
    }
}

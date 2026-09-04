<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\DeliveryMethod;
use App\Models\Order;
use App\Models\Product;
use App\Models\Warranty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Customer Registration API.
     */
    public function test_customer_can_register_via_api(): void
    {
        $response = $this->postJson('/api/v1/customer/register', [
            'name' => 'Tanvir Ahmed',
            'phone' => '01711223344',
            'email' => 'tanvir@example.com',
            'password' => 'secret123',
            'address' => 'Mirpur-10, Dhaka',
            'city' => 'Dhaka',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'customer' => [
                    'name' => 'Tanvir Ahmed',
                    'phone' => '01711223344',
                ],
            ]);

        $this->assertNotEmpty($response->json('token'));
        $this->assertDatabaseHas('customers', [
            'phone' => '01711223344',
        ]);
    }

    /**
     * Test Customer Login API.
     */
    public function test_customer_can_login_with_phone(): void
    {
        $customer = Customer::create([
            'name' => 'Fahim Hasan',
            'phone' => '01899887766',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/customer/login', [
            'login' => '01899887766',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'customer' => [
                    'name' => 'Fahim Hasan',
                    'phone' => '01899887766',
                ],
            ]);

        $this->assertNotEmpty($response->json('token'));
    }

    /**
     * Test Home Feed API.
     */
    public function test_home_feed_returns_expected_structure(): void
    {
        $category = Category::create([
            'name' => 'Microcontrollers',
            'slug' => 'microcontrollers',
            'is_active' => true,
            'is_featured' => true,
        ]);

        $product = Product::create([
            'name' => 'ESP32 Development Board',
            'slug' => 'esp32-dev-board',
            'sku' => 'ESP32-WROOM-32',
            'category_id' => $category->id,
            'selling_price' => 450.00,
            'stock_quantity' => 25,
            'is_featured' => true,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/home');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'banners',
                    'categories',
                    'flash_campaigns',
                    'brands',
                    'flash_deals',
                    'featured_products',
                    'best_sellers',
                    'new_arrivals',
                ],
            ]);
    }

    /**
     * Test Product Detail API with Variant Matrices.
     */
    public function test_product_detail_api(): void
    {
        $category = Category::create([
            'name' => 'Sensors',
            'slug' => 'sensors',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'DHT22 Temperature Sensor',
            'slug' => 'dht22-temp-sensor',
            'sku' => 'DHT22-SEN',
            'category_id' => $category->id,
            'selling_price' => 320.00,
            'stock_quantity' => 50,
            'is_active' => true,
            'warranty' => '6 Months Replacement',
        ]);

        $response = $this->getJson("/api/v1/products/{$product->slug}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'DHT22 Temperature Sensor',
                    'warranty' => '6 Months Replacement',
                ],
            ]);
    }

    /**
     * Test Cart Validation and Coupon Apply.
     */
    public function test_cart_validation_and_coupon_apply(): void
    {
        $category = Category::create([
            'name' => 'Boards',
            'slug' => 'boards',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Arduino Uno R3',
            'slug' => 'arduino-uno-r3',
            'sku' => 'ARD-UNO-R3',
            'category_id' => $category->id,
            'selling_price' => 650.00,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        // 1. Cart validate
        $cartResponse = $this->postJson('/api/v1/cart/validate', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $cartResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'subtotal' => 1300.00,
            ]);

        // 2. Coupon Apply
        Coupon::create([
            'code' => 'ROBOTICS100',
            'discount_type' => 'fixed',
            'discount_value' => 100.00,
            'min_order_amount' => 500.00,
            'is_active' => true,
        ]);

        $couponResponse = $this->postJson('/api/v1/coupon/apply', [
            'code' => 'ROBOTICS100',
            'subtotal' => 1300.00,
        ]);

        $couponResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'code' => 'ROBOTICS100',
                    'discount_amount' => 100.00,
                    'new_subtotal' => 1200.00,
                ],
            ]);
    }

    /**
     * Test Delivery Methods API.
     */
    public function test_delivery_methods_api(): void
    {
        DeliveryMethod::create([
            'name' => 'Inside Dhaka Express',
            'code' => 'inside_dhaka_express',
            'charge' => 70.00,
            'estimated_days' => '24 Hours',
            'is_active' => true,
            'is_default' => true,
        ]);

        $response = $this->getJson('/api/v1/delivery-methods?subtotal=500');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test Order Placement API.
     */
    public function test_order_placement_api(): void
    {
        $category = Category::create([
            'name' => 'Microcontrollers',
            'slug' => 'microcontrollers-arm',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'STM32 Blue Pill',
            'slug' => 'stm32-blue-pill',
            'sku' => 'STM32-F103C8T6',
            'category_id' => $category->id,
            'selling_price' => 350.00,
            'stock_quantity' => 15,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/checkout/place-order', [
            'name' => 'Kamal Hossain',
            'phone' => '01755667788',
            'address' => 'Sector 4, Uttara, Dhaka',
            'city' => 'Dhaka',
            'payment_method' => 'cod',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'subtotal' => 700.00,
                    'payment_method' => 'cod',
                ],
            ]);

        $this->assertDatabaseHas('orders', [
            'shipping_phone' => '01755667788',
            'subtotal' => 700.00,
        ]);
    }

    /**
     * Test Live Order Tracking Timeline API.
     */
    public function test_live_order_tracking_api(): void
    {
        $order = Order::create([
            'order_no' => 'ORD-TRACK-1234',
            'status' => 'processing',
            'subtotal' => 1000.00,
            'grand_total' => 1060.00,
            'payment_method' => 'cod',
            'shipping_name' => 'Arefin Shuvo',
            'shipping_phone' => '01911223344',
            'shipping_address' => 'Banani, Dhaka',
        ]);

        $response = $this->getJson('/api/v1/track-order?order_no=ORD-TRACK-1234&phone=01911223344');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'order_no' => 'ORD-TRACK-1234',
                    'status' => 'processing',
                    'total_steps' => 5,
                ],
            ]);
    }

    /**
     * Test Warranty Verification by Serial API.
     */
    public function test_warranty_verify_by_serial(): void
    {
        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics-warranty',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'PCB Soldering Kit',
            'slug' => 'pcb-soldering-kit',
            'sku' => 'PCB-SOLD-01',
            'category_id' => $category->id,
            'selling_price' => 1200.00,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        $warranty = Warranty::create([
            'warranty_code' => 'WAR-887766',
            'serial_number' => 'SN-PCB-998877',
            'product_id' => $product->id,
            'customer_name' => 'Rakib Hasan',
            'customer_phone' => '01600112233',
            'warranty_period' => '1 Year',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonths(11),
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/v1/warranty/verify?serial_no=SN-PCB-998877');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'serial_number' => 'SN-PCB-998877',
                    'is_valid' => true,
                ],
            ]);
    }
}

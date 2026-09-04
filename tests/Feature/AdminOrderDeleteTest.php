<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminOrderDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Product $product;

    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            ['name' => 'Dreamers Admin', 'password' => Hash::make('password')]
        );

        $category = Category::firstOrCreate(
            ['slug' => 'test-category'],
            ['name' => 'Test Category', 'is_active' => true]
        );

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Arduino Uno R3',
            'slug' => 'arduino-uno-r3',
            'sku' => 'ARD-UNO-R3',
            'purchase_price' => 400,
            'selling_price' => 650,
            'stock_quantity' => 20,
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'name' => 'Test Customer',
            'phone' => '01711223344',
            'email' => 'testcustomer@example.com',
            'total_orders' => 0,
            'total_spent' => 0,
        ]);
    }

    public function test_admin_can_delete_single_order(): void
    {
        $order = Order::create([
            'order_no' => 'ORD-TEST-001',
            'customer_id' => $this->customer->id,
            'order_type' => 'online',
            'status' => 'pending',
            'subtotal' => 650,
            'shipping_cost' => 60,
            'grand_total' => 710,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'shipping_name' => 'Test Customer',
            'shipping_phone' => '01711223344',
            'shipping_address' => 'Dhaka, Bangladesh',
            'shipping_city' => 'Dhaka',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'sku' => $this->product->sku,
            'unit_price' => 650,
            'quantity' => 2,
            'subtotal' => 1300,
        ]);

        // Stock starts at 20. Deleting active order of quantity 2 should restore stock to 22.
        $response = $this->actingAs($this->admin)->delete(route('admin.orders.destroy', $order->id));

        $response->assertRedirect(route('admin.orders.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('orders', ['id' => $order->id]);
        $this->assertEquals(22, $this->product->fresh()->stock_quantity);
    }

    public function test_admin_can_bulk_delete_orders(): void
    {
        $order1 = Order::create([
            'order_no' => 'ORD-BULK-001',
            'customer_id' => $this->customer->id,
            'order_type' => 'online',
            'status' => 'pending',
            'subtotal' => 650,
            'shipping_cost' => 60,
            'grand_total' => 710,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'shipping_name' => 'Test Customer',
            'shipping_phone' => '01711223344',
            'shipping_address' => 'Dhaka, Bangladesh',
            'shipping_city' => 'Dhaka',
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'sku' => $this->product->sku,
            'unit_price' => 650,
            'quantity' => 1,
            'subtotal' => 650,
        ]);

        $order2 = Order::create([
            'order_no' => 'ORD-BULK-002',
            'customer_id' => $this->customer->id,
            'order_type' => 'online',
            'status' => 'processing',
            'subtotal' => 650,
            'shipping_cost' => 60,
            'grand_total' => 710,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'shipping_name' => 'Test Customer',
            'shipping_phone' => '01711223344',
            'shipping_address' => 'Dhaka, Bangladesh',
            'shipping_city' => 'Dhaka',
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'sku' => $this->product->sku,
            'unit_price' => 650,
            'quantity' => 3,
            'subtotal' => 1950,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.orders.bulk.delete'), [
            'order_ids' => [$order1->id, $order2->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('orders', ['id' => $order1->id]);
        $this->assertSoftDeleted('orders', ['id' => $order2->id]);
        // Restored 1 + 3 = 4 to 20 -> 24
        $this->assertEquals(24, $this->product->fresh()->stock_quantity);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderAccountBalanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Account $cashAccount;

    protected Account $bkashAccount;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            ['name' => 'Dreamers Admin', 'password' => Hash::make('password')]
        );

        $this->cashAccount = Account::create([
            'name' => 'Cash Counter Drawer',
            'account_type' => 'cash',
            'opening_balance' => 10000,
            'current_balance' => 10000,
            'is_default' => true,
            'is_active' => true,
        ]);

        $this->bkashAccount = Account::create([
            'name' => 'bKash Merchant Account',
            'account_type' => 'mobile_banking',
            'opening_balance' => 5000,
            'current_balance' => 5000,
            'is_default' => false,
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'PCB Development',
            'slug' => 'pcb-development',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Arduino Nano V3',
            'slug' => 'arduino-nano-v3',
            'sku' => 'ARD-NANO-01',
            'purchase_price' => 200,
            'selling_price' => 350,
            'stock_quantity' => 100,
            'is_active' => true,
        ]);
    }

    public function test_pos_sale_credits_account_and_updates_available_balance(): void
    {
        $initialBalance = (float) $this->cashAccount->fresh()->current_balance;

        $response = $this->actingAs($this->admin)->postJson(route('admin.pos.checkout'), [
            'cart' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                ],
            ],
            'paid_amount' => 700,
            'payment_method' => 'pos_cash',
            'account_id' => $this->cashAccount->id,
            'customer_name' => 'Walk-in Shopper',
            'customer_phone' => '01711223344',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->cashAccount->refresh();
        $this->assertEquals($initialBalance + 700, (float) $this->cashAccount->current_balance);

        $order = Order::where('shipping_phone', '01711223344')->first();
        $this->assertNotNull($order);
        $this->assertEquals(700, (float) $order->paid_amount);
        $this->assertEquals($this->cashAccount->id, $order->account_id);

        $this->assertDatabaseHas('transactions', [
            'account_id' => $this->cashAccount->id,
            'type' => 'credit',
            'amount' => 700,
            'source_type' => 'order',
            'source_id' => $order->id,
            'reference_no' => $order->order_no,
        ]);
    }

    public function test_marking_cod_order_completed_credits_unpaid_due_to_available_balance(): void
    {
        $initialBalance = (float) $this->cashAccount->fresh()->current_balance;

        $order = Order::create([
            'order_no' => 'DPCB-TEST-001',
            'order_type' => 'online',
            'status' => 'pending',
            'subtotal' => 1000,
            'grand_total' => 1100,
            'paid_amount' => 0,
            'due_amount' => 1100,
            'payment_method' => 'cash_on_delivery',
            'payment_status' => 'pending',
            'shipping_name' => 'Online Customer',
            'shipping_phone' => '01899887766',
            'shipping_address' => 'Mirpur, Dhaka',
            'shipping_city' => 'Dhaka',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.orders.status.update', $order->id), [
            'status' => 'completed',
            'note' => 'Delivered by courier and received COD cash',
        ]);

        $response->assertRedirect();

        $order->refresh();
        $this->cashAccount->refresh();

        $this->assertEquals('completed', $order->status);
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals(1100, (float) $order->paid_amount);
        $this->assertEquals(0, (float) $order->due_amount);

        // Account balance increased by 1100
        $this->assertEquals($initialBalance + 1100, (float) $this->cashAccount->current_balance);

        $this->assertDatabaseHas('transactions', [
            'account_id' => $this->cashAccount->id,
            'type' => 'credit',
            'amount' => 1100,
            'source_type' => 'order',
            'source_id' => $order->id,
        ]);
    }

    public function test_cancelling_completed_order_reverses_account_balance(): void
    {
        $initialBalance = (float) $this->bkashAccount->fresh()->current_balance;

        $order = Order::create([
            'order_no' => 'DPCB-TEST-002',
            'order_type' => 'online',
            'status' => 'pending',
            'subtotal' => 2000,
            'grand_total' => 2000,
            'paid_amount' => 0,
            'due_amount' => 2000,
            'payment_method' => 'bkash',
            'account_id' => $this->bkashAccount->id,
            'payment_status' => 'pending',
            'shipping_name' => 'bKash Shopper',
            'shipping_phone' => '01700998877',
            'shipping_address' => 'Gulshan, Dhaka',
            'shipping_city' => 'Dhaka',
        ]);

        // Complete the order
        $this->actingAs($this->admin)->post(route('admin.orders.status.update', $order->id), [
            'status' => 'completed',
        ]);

        $this->bkashAccount->refresh();
        $this->assertEquals($initialBalance + 2000, (float) $this->bkashAccount->current_balance);

        // Now cancel the order
        $this->actingAs($this->admin)->post(route('admin.orders.status.update', $order->id), [
            'status' => 'cancelled',
            'note' => 'Customer requested return and refund',
        ]);

        $this->bkashAccount->refresh();
        // Balance reversed back to initial
        $this->assertEquals($initialBalance, (float) $this->bkashAccount->current_balance);

        $this->assertDatabaseHas('transactions', [
            'account_id' => $this->bkashAccount->id,
            'type' => 'debit',
            'amount' => 2000,
            'source_type' => 'order',
            'source_id' => $order->id,
        ]);
    }

    public function test_admin_can_record_manual_due_payment_and_credit_account(): void
    {
        $initialBalance = (float) $this->cashAccount->fresh()->current_balance;

        $order = Order::create([
            'order_no' => 'DPCB-TEST-003',
            'order_type' => 'online',
            'status' => 'processing',
            'subtotal' => 3000,
            'grand_total' => 3000,
            'paid_amount' => 1000,
            'due_amount' => 2000,
            'payment_method' => 'cash_on_delivery',
            'payment_status' => 'partially_paid',
            'shipping_name' => 'Partial Customer',
            'shipping_phone' => '01655443322',
            'shipping_address' => 'Banani, Dhaka',
            'shipping_city' => 'Dhaka',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.orders.payment', $order->id), [
            'amount' => 2000,
            'account_id' => $this->cashAccount->id,
            'note' => 'Remaining due collected in full',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->cashAccount->refresh();

        $this->assertEquals(3000, (float) $order->paid_amount);
        $this->assertEquals(0, (float) $order->due_amount);
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals($initialBalance + 2000, (float) $this->cashAccount->current_balance);
    }

    public function test_sync_order_accounts_artisan_command_syncs_existing_sales(): void
    {
        $initialBalance = (float) $this->cashAccount->fresh()->current_balance;

        // Create completed order that lacks transaction
        $order = Order::create([
            'order_no' => 'DPCB-UNSYNCED-01',
            'order_type' => 'pos',
            'status' => 'completed',
            'subtotal' => 500,
            'grand_total' => 500,
            'paid_amount' => 500,
            'due_amount' => 0,
            'payment_method' => 'pos_cash',
            'payment_status' => 'paid',
            'shipping_name' => 'Counter Customer',
            'shipping_phone' => '01799881122',
            'shipping_address' => 'Store POS',
            'shipping_city' => 'Dhaka',
        ]);

        $this->artisan('app:sync-order-accounts')
            ->assertSuccessful()
            ->expectsOutputToContain('Successfully synchronized');

        $this->cashAccount->refresh();
        $this->assertEquals($initialBalance + 500, (float) $this->cashAccount->current_balance);

        $this->assertDatabaseHas('transactions', [
            'account_id' => $this->cashAccount->id,
            'type' => 'credit',
            'amount' => 500,
            'source_type' => 'order',
            'source_id' => $order->id,
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PurchasesAndSupplierDueTest extends TestCase
{
    protected User $admin;

    protected Supplier $supplier;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            ['name' => 'Dreamers Admin', 'password' => Hash::make('password')]
        );
        $this->actingAs($this->admin, 'web');

        $category = Category::firstOrCreate(
            ['slug' => 'test-category'],
            ['name' => 'Test Category', 'is_active' => true]
        );

        $this->product = Product::firstOrCreate(
            ['sku' => 'TEST-PROD-001'],
            [
                'name' => 'Test Controller Board',
                'slug' => 'test-controller-board',
                'category_id' => $category->id,
                'purchase_price' => 500,
                'selling_price' => 800,
                'stock_quantity' => 10,
                'is_active' => true,
            ]
        );

        $this->supplier = Supplier::create([
            'name' => 'Test Mega Supplier Ltd',
            'company' => 'Mega Supplier Corp',
            'phone' => '01711999888',
            'opening_balance' => 0,
            'current_due' => 0,
            'is_active' => true,
        ]);
    }

    public function test_purchase_creation_with_partial_payment_sets_correct_due(): void
    {
        $response = $this->post(route('admin.purchases.store'), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => date('Y-m-d'),
            'paid_amount' => 200,
            'discount' => 0,
            'tax' => 0,
            'shipping_cost' => 0,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'unit_cost' => 500,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.purchases.index'));

        $purchase = Purchase::where('supplier_id', $this->supplier->id)->latest()->first();
        $this->assertNotNull($purchase);
        $this->assertEquals(1000.00, (float) $purchase->grand_total);
        $this->assertEquals(200.00, (float) $purchase->paid_amount);
        $this->assertEquals(800.00, (float) $purchase->due_amount);
        $this->assertEquals('partial', $purchase->payment_status);

        $this->supplier->refresh();
        $this->assertEquals(1000.00, (float) $this->supplier->total_purchased);
        $this->assertEquals(200.00, (float) $this->supplier->total_paid);
        $this->assertEquals(800.00, (float) $this->supplier->current_due);
    }

    public function test_paying_purchase_directly_updates_purchase_and_supplier_due(): void
    {
        $purchase = Purchase::create([
            'supplier_id' => $this->supplier->id,
            'purchase_no' => 'PO-TEST-001',
            'purchase_date' => date('Y-m-d'),
            'subtotal' => 1000,
            'grand_total' => 1000,
            'paid_amount' => 0,
            'due_amount' => 1000,
            'payment_status' => 'due',
            'status' => 'received',
            'created_by' => $this->admin->id,
        ]);

        $this->supplier->recalculateDue();
        $this->supplier->refresh();
        $this->assertEquals(1000.00, (float) $this->supplier->current_due);

        // Record full payment for this PO
        $payResponse = $this->post(route('admin.purchases.pay', $purchase->id), [
            'amount' => 1000,
            'payment_date' => date('Y-m-d'),
            'payment_method' => 'bank',
            'reference_no' => 'TRX-12345',
        ]);

        $payResponse->assertSessionHas('success');

        $purchase->refresh();
        $this->assertEquals(1000.00, (float) $purchase->paid_amount);
        $this->assertEquals(0.00, (float) $purchase->due_amount);
        $this->assertEquals('paid', $purchase->payment_status);

        $this->supplier->refresh();
        $this->assertEquals(0.00, (float) $this->supplier->current_due);
        $this->assertEquals(1000.00, (float) $this->supplier->total_paid);
    }

    public function test_paying_supplier_generally_settles_purchases_due_in_fifo_order(): void
    {
        $po1 = Purchase::create([
            'supplier_id' => $this->supplier->id,
            'purchase_no' => 'PO-FIFO-1',
            'purchase_date' => '2026-01-01',
            'subtotal' => 500,
            'grand_total' => 500,
            'paid_amount' => 0,
            'due_amount' => 500,
            'payment_status' => 'due',
            'status' => 'received',
            'created_by' => $this->admin->id,
        ]);

        $po2 = Purchase::create([
            'supplier_id' => $this->supplier->id,
            'purchase_no' => 'PO-FIFO-2',
            'purchase_date' => '2026-01-02',
            'subtotal' => 500,
            'grand_total' => 500,
            'paid_amount' => 0,
            'due_amount' => 500,
            'payment_status' => 'due',
            'status' => 'received',
            'created_by' => $this->admin->id,
        ]);

        $this->supplier->recalculateDue();

        // Pay 700 to supplier generally
        $this->post(route('admin.suppliers.pay', $this->supplier->id), [
            'amount' => 700,
            'payment_date' => date('Y-m-d'),
            'payment_method' => 'cash',
        ]);

        $po1->refresh();
        $po2->refresh();
        $this->supplier->refresh();

        // PO1 should be fully paid (500)
        $this->assertEquals(500.00, (float) $po1->paid_amount);
        $this->assertEquals(0.00, (float) $po1->due_amount);
        $this->assertEquals('paid', $po1->payment_status);

        // PO2 should have 200 paid and 300 due
        $this->assertEquals(200.00, (float) $po2->paid_amount);
        $this->assertEquals(300.00, (float) $po2->due_amount);
        $this->assertEquals('partial', $po2->payment_status);

        // Supplier should have 300 current due
        $this->assertEquals(300.00, (float) $this->supplier->current_due);
    }
}

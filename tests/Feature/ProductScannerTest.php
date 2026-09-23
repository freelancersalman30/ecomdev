<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProductScannerTest extends TestCase
{
    protected User $admin;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            ['name' => 'Dreamers Admin', 'password' => Hash::make('password')]
        );

        $this->category = Category::firstOrCreate(
            ['slug' => 'development-boards'],
            ['name' => 'Development Boards', 'is_active' => true]
        );
    }

    public function test_admin_can_access_product_scanner_terminal(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.products.scanner'));

        $response->assertStatus(200);
        $response->assertSee('Auto Stock-In');
        $response->assertSee('Auto-Entry');
        $response->assertSee('Session Scans');
    }

    public function test_scanner_lookup_finds_existing_product_by_barcode_or_sku(): void
    {
        $product = Product::firstOrCreate(
            ['sku' => 'TEST-SCAN-001'],
            [
                'category_id' => $this->category->id,
                'name' => 'Arduino Nano V3',
                'slug' => 'arduino-nano-v3-scan-test',
                'barcode' => '8901234567890',
                'purchase_price' => 200,
                'selling_price' => 320,
                'stock_quantity' => 15,
                'is_active' => true,
            ]
        );

        $response = $this->actingAs($this->admin, 'web')->postJson(route('admin.products.scanner.lookup'), [
            'code' => '8901234567890',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'exists' => true,
            'product' => [
                'id' => $product->id,
                'sku' => 'TEST-SCAN-001',
                'barcode' => '8901234567890',
            ],
        ]);
    }

    public function test_scanner_lookup_returns_suggestion_for_new_barcode(): void
    {
        $newBarcode = '9998887776665';

        $response = $this->actingAs($this->admin, 'web')->postJson(route('admin.products.scanner.lookup'), [
            'code' => $newBarcode,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'exists' => false,
            'scanned_code' => $newBarcode,
        ]);
        $this->assertArrayHasKey('suggestion', $response->json());
    }

    public function test_scanner_quick_store_creates_new_product_automatically(): void
    {
        $newBarcode = 'BAR-AUTO-'.rand(10000, 99999);

        $response = $this->actingAs($this->admin, 'web')->postJson(route('admin.products.scanner.quick_store'), [
            'barcode' => $newBarcode,
            'name' => 'Raspberry Pi Pico W',
            'category_id' => $this->category->id,
            'purchase_price' => 450,
            'selling_price' => 650,
            'stock_quantity' => 20,
            'warranty' => '6 Months Warranty',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'action' => 'created',
        ]);

        $this->assertDatabaseHas('products', [
            'barcode' => $newBarcode,
            'name' => 'Raspberry Pi Pico W',
            'stock_quantity' => 20,
            'purchase_price' => 450,
            'selling_price' => 650,
        ]);
    }

    public function test_scanner_stock_in_increments_existing_product_quantity(): void
    {
        $product = Product::firstOrCreate(
            ['sku' => 'STOCK-IN-001'],
            [
                'category_id' => $this->category->id,
                'name' => 'NodeMCU ESP8266',
                'slug' => 'nodemcu-esp8266-test',
                'barcode' => '8881112223334',
                'purchase_price' => 180,
                'selling_price' => 280,
                'stock_quantity' => 10,
                'is_active' => true,
            ]
        );

        $initialStock = $product->stock_quantity;

        $response = $this->actingAs($this->admin, 'web')->postJson(route('admin.products.scanner.stock_in'), [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'product' => [
                'stock_quantity' => $initialStock + 5,
            ],
        ]);

        $this->assertEquals($initialStock + 5, $product->fresh()->stock_quantity);
    }

    public function test_scanner_batch_commit_persists_multiple_scanned_items(): void
    {
        $barcode1 = 'BATCH-'.rand(1000, 9999);
        $barcode2 = 'BATCH-'.rand(1000, 9999);

        $response = $this->actingAs($this->admin, 'web')->postJson(route('admin.products.scanner.batch_commit'), [
            'items' => [
                [
                    'barcode' => $barcode1,
                    'name' => 'SMD Resistor Reel 10K',
                    'category_id' => $this->category->id,
                    'quantity' => 50,
                    'purchase_price' => 100,
                    'selling_price' => 200,
                ],
                [
                    'barcode' => $barcode2,
                    'name' => 'SMD Capacitor Reel 100nF',
                    'category_id' => $this->category->id,
                    'quantity' => 30,
                    'purchase_price' => 80,
                    'selling_price' => 150,
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'processed_items' => 2,
            'total_units' => 80,
        ]);

        $this->assertDatabaseHas('products', ['barcode' => $barcode1, 'stock_quantity' => 50]);
        $this->assertDatabaseHas('products', ['barcode' => $barcode2, 'stock_quantity' => 30]);
    }

    public function test_admin_can_render_printable_barcode_label(): void
    {
        $product = Product::first();
        $this->assertNotNull($product);

        $response = $this->actingAs($this->admin, 'web')->get(route('admin.products.barcode_label', $product->id));

        $response->assertStatus(200);
        $response->assertSee('DREAMERS PCB');
        $response->assertSee($product->sku);
    }
}

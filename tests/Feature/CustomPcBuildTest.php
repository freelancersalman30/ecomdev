<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomPcBuild;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class CustomPcBuildTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Product $testProduct;

    protected Account $testAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            ['name' => 'Dreamers Admin', 'password' => Hash::make('password')]
        );
        $this->actingAs($this->admin, 'web');

        $category = Category::firstOrCreate(
            ['slug' => 'processors'],
            ['name' => 'Processors', 'is_active' => true]
        );

        $this->testProduct = Product::firstOrCreate(
            ['sku' => 'INTEL-13400F-TEST'],
            [
                'name' => 'Intel Core i5 13400F 10-Core Processor',
                'slug' => 'intel-core-i5-13400f-test-'.Str::random(4),
                'category_id' => $category->id,
                'selling_price' => 24500.00,
                'stock_quantity' => 15,
                'warranty' => '3 Years Official',
                'is_active' => true,
            ]
        );

        $this->testAccount = Account::firstOrCreate(
            ['name' => 'Main Cash Drawer'],
            [
                'account_type' => 'cash',
                'account_number' => 'CASH-001',
                'opening_balance' => 50000.00,
                'current_balance' => 50000.00,
                'is_active' => true,
            ]
        );
    }

    public function test_admin_can_view_custom_pc_builds_list(): void
    {
        CustomPcBuild::create([
            'title' => 'Test Gaming Beast RTX 4070',
            'category' => 'Gaming',
            'performance_level' => 'High-End',
            'final_price' => 145000.00,
            'is_published' => true,
        ]);

        $response = $this->get(route('admin.custom-pc-builds.index'));
        $response->assertStatus(200);
        $response->assertSee('Test Gaming Beast RTX 4070');
        $response->assertSee('Ready Custom PC Building Studio');
    }

    public function test_admin_can_view_create_page(): void
    {
        $response = $this->get(route('admin.custom-pc-builds.create'));
        $response->assertStatus(200);
        $response->assertSee('Custom PC Building Studio');
        $response->assertSee('Components Configured');
    }

    public function test_admin_can_store_custom_pc_build(): void
    {
        $components = [
            [
                'slot_key' => 'processor',
                'slot_name' => 'Processor / CPU',
                'product_id' => $this->testProduct->id,
                'product_name' => $this->testProduct->name,
                'sku' => $this->testProduct->sku,
                'warranty' => '3 Years',
                'unit_price' => 24500.00,
                'quantity' => 1,
                'subtotal' => 24500.00,
            ],
            [
                'slot_key' => 'ram',
                'slot_name' => 'RAM / Memory',
                'custom_item_name' => 'G.Skill Ripjaws 16GB DDR4 3200MHz',
                'sku' => 'GSK-16GB',
                'warranty' => 'Lifetime',
                'unit_price' => 4500.00,
                'quantity' => 2,
                'subtotal' => 9000.00,
            ],
        ];

        $payload = [
            'title' => 'Dreamers Intel Core i5 Budget Gaming Rig',
            'category' => 'Gaming',
            'performance_level' => 'Mid-Range',
            'description' => 'Great 1080p high fps gaming configuration.',
            'estimated_wattage' => 450,
            'regular_price' => 33500.00,
            'discount_type' => 'fixed',
            'discount_amount' => 1500.00,
            'final_price' => 32000.00,
            'stock_status' => 'in_stock',
            'is_published' => 1,
            'is_featured' => 1,
            'components_json' => json_encode($components),
        ];

        $response = $this->post(route('admin.custom-pc-builds.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('custom_pc_builds', [
            'title' => 'Dreamers Intel Core i5 Budget Gaming Rig',
            'final_price' => 32000.00,
            'is_published' => 1,
            'is_featured' => 1,
        ]);
    }

    public function test_admin_can_view_custom_pc_build_details(): void
    {
        $build = CustomPcBuild::create([
            'title' => 'Workstation Rig Threadripper',
            'category' => 'Workstation',
            'performance_level' => 'Extreme',
            'regular_price' => 250000.00,
            'final_price' => 240000.00,
            'components' => [
                [
                    'slot_name' => 'Processor / CPU',
                    'product_id' => $this->testProduct->id,
                    'product_name' => $this->testProduct->name,
                    'unit_price' => 24500.00,
                    'quantity' => 1,
                    'subtotal' => 24500.00,
                ],
            ],
        ]);

        $response = $this->get(route('admin.custom-pc-builds.show', $build->id));
        $response->assertStatus(200);
        $response->assertSee('Workstation Rig Threadripper');
        $response->assertSee('240,000.00');
    }

    public function test_admin_can_update_custom_pc_build(): void
    {
        $build = CustomPcBuild::create([
            'title' => 'Old Title PC',
            'category' => 'Office',
            'final_price' => 35000.00,
        ]);

        $payload = [
            'title' => 'Updated Office Performance PC',
            'category' => 'Office',
            'performance_level' => 'Mid-Range',
            'final_price' => 38000.00,
            'is_published' => 1,
            'components_json' => json_encode([]),
        ];

        $response = $this->put(route('admin.custom-pc-builds.update', $build->id), $payload);
        $response->assertRedirect(route('admin.custom-pc-builds.show', $build->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('custom_pc_builds', [
            'id' => $build->id,
            'title' => 'Updated Office Performance PC',
            'final_price' => 38000.00,
        ]);
    }

    public function test_admin_can_duplicate_custom_pc_build(): void
    {
        $build = CustomPcBuild::create([
            'title' => 'Original PC Build',
            'category' => 'Gaming',
            'final_price' => 85000.00,
            'is_published' => true,
        ]);

        $response = $this->post(route('admin.custom-pc-builds.duplicate', $build->id));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('custom_pc_builds', [
            'title' => 'Original PC Build (Copy)',
            'is_published' => false,
        ]);
    }

    public function test_admin_can_view_quotation(): void
    {
        $build = CustomPcBuild::create([
            'title' => 'Quotation Demo PC',
            'category' => 'Gaming',
            'final_price' => 60000.00,
        ]);

        $response = $this->get(route('admin.custom-pc-builds.quotation', $build->id));
        $response->assertStatus(200);
        $response->assertSee('Official PC Quotation');
        $response->assertSee('Quotation Demo PC');
    }

    public function test_admin_can_search_products_api(): void
    {
        $response = $this->get(route('admin.custom-pc-builds.search-products', ['q' => 'Intel Core']));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'sku' => $this->testProduct->sku,
        ]);
    }

    public function test_admin_can_convert_pc_build_to_order(): void
    {
        $customer = Customer::create([
            'name' => 'PC Buyer User',
            'phone' => '01833445566',
            'email' => 'pcbuyer@test.com',
            'is_active' => true,
        ]);

        $build = CustomPcBuild::create([
            'title' => 'Ready Assemble Rig',
            'regular_price' => 24500.00,
            'final_price' => 24000.00,
            'components' => [
                [
                    'slot_name' => 'Processor',
                    'product_id' => $this->testProduct->id,
                    'quantity' => 1,
                    'unit_price' => 24500.00,
                    'subtotal' => 24500.00,
                ],
            ],
        ]);

        $payload = [
            'customer_type' => 'existing',
            'customer_id' => $customer->id,
            'order_type' => 'pos',
            'payment_method' => 'pos_cash',
            'account_id' => $this->testAccount->id,
            'paid_amount' => 24000.00,
        ];

        $response = $this->post(route('admin.custom-pc-builds.convert-order', $build->id), $payload);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'order_type' => 'pos',
        ]);
    }

    public function test_admin_can_soft_delete_custom_pc_build(): void
    {
        $build = CustomPcBuild::create([
            'title' => 'To Delete PC Build',
            'final_price' => 45000.00,
        ]);

        $response = $this->delete(route('admin.custom-pc-builds.destroy', $build->id));
        $response->assertRedirect(route('admin.custom-pc-builds.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('custom_pc_builds', [
            'id' => $build->id,
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ChildCategory;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            ['name' => 'Dreamers Admin', 'password' => Hash::make('password')]
        );
        $this->actingAs($admin, 'web');
    }

    public function test_admin_can_view_category_management_page(): void
    {
        $response = $this->get('/admin/categories');
        $response->assertStatus(200);
        $response->assertSee('Product Category Architecture');
    }

    public function test_admin_can_crud_and_inactivate_primary_category(): void
    {
        // 1. Create Active Category
        $createRes = $this->post('/admin/categories', [
            'name' => 'Wireless Transceivers',
            'icon' => 'radio',
            'description' => 'Lora, Zigbee and RF modules',
            'display_order' => 5,
            'is_featured' => '1',
            'is_active' => '1',
        ]);
        $createRes->assertRedirect();
        $category = Category::where('name', 'Wireless Transceivers')->first();
        $this->assertTrue($category->is_active);

        // 2. Inactivate Category via Update (sending is_active = 0)
        $updateRes = $this->put('/admin/categories/'.$category->id, [
            'name' => 'Wireless & RF Modules',
            'slug' => 'wireless-rf-modules',
            'icon' => 'wifi',
            'is_active' => '0',
        ]);
        $updateRes->assertRedirect();
        $category->refresh();
        $this->assertFalse($category->is_active);
        $this->assertEquals('Wireless & RF Modules', $category->name);

        // 3. One-Click Toggle Category Status
        $toggleRes = $this->post('/admin/categories/'.$category->id.'/toggle-status');
        $toggleRes->assertRedirect();
        $category->refresh();
        $this->assertTrue($category->is_active);

        // 4. Delete
        $deleteRes = $this->delete('/admin/categories/'.$category->id);
        $deleteRes->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_admin_can_crud_and_toggle_subcategory(): void
    {
        $category = Category::create([
            'name' => 'Development Boards',
            'slug' => 'development-boards',
            'is_active' => true,
        ]);

        // 1. Create SubCategory
        $createRes = $this->post('/admin/sub-categories', [
            'category_id' => $category->id,
            'name' => 'ESP32 IoT Series',
            'is_active' => '1',
        ]);
        $createRes->assertRedirect();
        $subCategory = SubCategory::where('name', 'ESP32 IoT Series')->first();
        $this->assertTrue($subCategory->is_active);

        // 2. Inactivate SubCategory via update
        $updateRes = $this->put('/admin/sub-categories/'.$subCategory->id, [
            'category_id' => $category->id,
            'name' => 'ESP32 IoT Series',
            'is_active' => '0',
        ]);
        $updateRes->assertRedirect();
        $subCategory->refresh();
        $this->assertFalse($subCategory->is_active);

        // 3. Toggle status
        $toggleRes = $this->post('/admin/sub-categories/'.$subCategory->id.'/toggle-status');
        $toggleRes->assertRedirect();
        $subCategory->refresh();
        $this->assertTrue($subCategory->is_active);

        // 4. Delete
        $deleteRes = $this->delete('/admin/sub-categories/'.$subCategory->id);
        $deleteRes->assertRedirect();
        $this->assertDatabaseMissing('sub_categories', ['id' => $subCategory->id]);
    }

    public function test_admin_can_crud_and_toggle_childcategory(): void
    {
        $category = Category::create([
            'name' => 'Sensors',
            'slug' => 'sensors',
            'is_active' => true,
        ]);

        $subCategory = SubCategory::create([
            'category_id' => $category->id,
            'name' => 'Environmental Sensors',
            'slug' => 'environmental-sensors',
            'is_active' => true,
        ]);

        // 1. Create ChildCategory
        $createRes = $this->post('/admin/child-categories', [
            'sub_category_id' => $subCategory->id,
            'name' => 'BME280 Pressure Sensors',
            'is_active' => '1',
        ]);
        $createRes->assertRedirect();
        $childCategory = ChildCategory::where('name', 'BME280 Pressure Sensors')->first();
        $this->assertTrue($childCategory->is_active);

        // 2. Toggle Status to Inactive
        $toggleRes = $this->post('/admin/child-categories/'.$childCategory->id.'/toggle-status');
        $toggleRes->assertRedirect();
        $childCategory->refresh();
        $this->assertFalse($childCategory->is_active);

        // 3. Delete
        $deleteRes = $this->delete('/admin/child-categories/'.$childCategory->id);
        $deleteRes->assertRedirect();
        $this->assertDatabaseMissing('child_categories', ['id' => $childCategory->id]);
    }
}

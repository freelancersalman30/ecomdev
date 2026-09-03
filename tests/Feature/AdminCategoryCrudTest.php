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
        $response->assertSee('Categories, Sub-Categories & Child-Categories CRUD');
    }

    public function test_admin_can_crud_primary_category(): void
    {
        // 1. Create
        $createRes = $this->post('/admin/categories', [
            'name' => 'Wireless Transceivers',
            'icon' => 'radio',
            'description' => 'Lora, Zigbee and RF modules',
            'display_order' => 5,
            'is_featured' => '1',
            'is_active' => '1',
        ]);
        $createRes->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Wireless Transceivers', 'icon' => 'radio']);

        $category = Category::where('name', 'Wireless Transceivers')->first();

        // 2. Update
        $updateRes = $this->put('/admin/categories/'.$category->id, [
            'name' => 'Wireless & RF Modules',
            'slug' => 'wireless-rf-modules',
            'icon' => 'wifi',
            'description' => 'Updated RF modules',
            'display_order' => 10,
            'is_active' => '1',
        ]);
        $updateRes->assertRedirect();
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Wireless & RF Modules', 'slug' => 'wireless-rf-modules']);

        // 3. Delete
        $deleteRes = $this->delete('/admin/categories/'.$category->id);
        $deleteRes->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_admin_can_crud_subcategory(): void
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
            'description' => 'ESP32 Wi-Fi + Bluetooth modules',
        ]);
        $createRes->assertRedirect();
        $this->assertDatabaseHas('sub_categories', ['name' => 'ESP32 IoT Series', 'category_id' => $category->id]);

        $subCategory = SubCategory::where('name', 'ESP32 IoT Series')->first();

        // 2. Update SubCategory
        $updateRes = $this->put('/admin/sub-categories/'.$subCategory->id, [
            'category_id' => $category->id,
            'name' => 'ESP32 & ESP8266 Series',
            'slug' => 'esp32-esp8266-series',
            'description' => 'Updated description',
        ]);
        $updateRes->assertRedirect();
        $this->assertDatabaseHas('sub_categories', ['id' => $subCategory->id, 'name' => 'ESP32 & ESP8266 Series']);

        // 3. Delete SubCategory
        $deleteRes = $this->delete('/admin/sub-categories/'.$subCategory->id);
        $deleteRes->assertRedirect();
        $this->assertDatabaseMissing('sub_categories', ['id' => $subCategory->id]);
    }

    public function test_admin_can_crud_childcategory(): void
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
        ]);
        $createRes->assertRedirect();
        $this->assertDatabaseHas('child_categories', ['name' => 'BME280 Pressure Sensors', 'sub_category_id' => $subCategory->id]);

        $childCategory = ChildCategory::where('name', 'BME280 Pressure Sensors')->first();

        // 2. Update ChildCategory
        $updateRes = $this->put('/admin/child-categories/'.$childCategory->id, [
            'sub_category_id' => $subCategory->id,
            'name' => 'BME280 & BMP280 Sensors',
            'slug' => 'bme280-bmp280-sensors',
        ]);
        $updateRes->assertRedirect();
        $this->assertDatabaseHas('child_categories', ['id' => $childCategory->id, 'name' => 'BME280 & BMP280 Sensors']);

        // 3. Delete ChildCategory
        $deleteRes = $this->delete('/admin/child-categories/'.$childCategory->id);
        $deleteRes->assertRedirect();
        $this->assertDatabaseMissing('child_categories', ['id' => $childCategory->id]);
    }
}

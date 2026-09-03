<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_product_layout_settings_page()
    {
        $admin = User::factory()->create([
            'email' => 'admin@dreamerspcb.com',
        ]);

        $category = Category::create([
            'name' => 'Sensors',
            'slug' => 'sensors',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'BMP280 Barometric Pressure Sensor',
            'slug' => 'bmp280-sensor',
            'sku' => 'BMP280-TEST',
            'category_id' => $category->id,
            'purchase_price' => 120,
            'selling_price' => 200,
            'discount_price' => 160,
            'stock_quantity' => 40,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.products.layout'));

        $response->assertStatus(200);
        $response->assertSee('Product Layout & Display Customizer', false);
        $response->assertSee('Modern Daraz');
        $response->assertSee('Compact Tech');
        $response->assertSee('Minimalist Bordered');
        $response->assertSee('Live Storefront Card Preview');
    }

    public function test_admin_can_update_product_layout_settings()
    {
        $admin = User::factory()->create([
            'email' => 'admin@dreamerspcb.com',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.products.layout.update'), [
            'product_card_style' => 'compact_tech',
            'home_flash_sale_layout' => 'grid',
            'home_category_layout' => 'grid',
            'product_related_layout' => 'grid',
            'shop_grid_columns' => '5_cols',
            'carousel_interval' => 4500,
            'carousel_autoplay' => '1',
            'carousel_pause_hover' => '1',
            'show_discount_badge' => '1',
            'show_old_price' => '1',
            'show_quick_add' => '1',
            'show_tech_specs' => '1',
            'show_ratings' => '1',
        ]);

        $response->assertRedirect(route('admin.products.layout'));
        $response->assertSessionHas('success');

        $this->assertEquals('compact_tech', Setting::get('product_card_style'));
        $this->assertEquals('grid', Setting::get('home_flash_sale_layout'));
        $this->assertEquals('grid', Setting::get('home_category_layout'));
        $this->assertEquals('grid', Setting::get('product_related_layout'));
        $this->assertEquals('5_cols', Setting::get('shop_grid_columns'));
        $this->assertEquals('4500', Setting::get('carousel_interval'));
    }

    public function test_admin_can_reset_layout_settings_to_defaults()
    {
        $admin = User::factory()->create([
            'email' => 'admin@dreamerspcb.com',
        ]);

        Setting::set('product_card_style', 'compact_tech', 'product_layout');
        Setting::set('home_flash_sale_layout', 'grid', 'product_layout');

        $response = $this->actingAs($admin)->post(route('admin.products.layout.reset'));

        $response->assertRedirect(route('admin.products.layout'));
        $response->assertSessionHas('success');

        $this->assertEquals('modern_daraz', Setting::get('product_card_style'));
        $this->assertEquals('carousel', Setting::get('home_flash_sale_layout'));
    }

    public function test_storefront_dynamically_reflects_card_style_and_grid_settings()
    {
        $category = Category::create([
            'name' => 'Microcontrollers',
            'slug' => 'microcontrollers',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'ESP32-WROOM-32D Development Board',
            'slug' => 'esp32-wroom-32d',
            'sku' => 'ESP32-TEST-01',
            'category_id' => $category->id,
            'purchase_price' => 250,
            'selling_price' => 450,
            'discount_price' => 380,
            'stock_quantity' => 100,
            'is_active' => true,
        ]);

        // 1. Set style to minimalist_bordered and shop columns to 3_cols
        Setting::set('product_card_style', 'minimalist_bordered', 'product_layout');
        Setting::set('shop_grid_columns', '3_cols', 'product_layout');
        Setting::set('home_flash_sale_layout', 'grid', 'product_layout');

        $shopResponse = $this->get('/shop');
        $shopResponse->assertStatus(200);
        $shopResponse->assertSee('lg:grid-cols-3');
        $shopResponse->assertSee('ESP32-WROOM-32D');

        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('ESP32-WROOM-32D');
    }
}

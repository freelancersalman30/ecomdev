<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCarouselTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_auto_sliding_product_carousels_and_discount_percentages()
    {
        $category = Category::create([
            'name' => 'Microcontrollers',
            'slug' => 'microcontrollers',
            'is_active' => true,
        ]);

        for ($i = 1; $i <= 6; $i++) {
            Product::create([
                'name' => "ESP32 Dev Module #{$i}",
                'slug' => "esp32-dev-module-{$i}",
                'sku' => "ESP32-DEV-{$i}",
                'category_id' => $category->id,
                'purchase_price' => 200,
                'selling_price' => 350,
                'discount_price' => 300,
                'stock_quantity' => 50,
                'is_flash_sale' => true,
                'is_active' => true,
            ]);
        }

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('productCarousel');
        $response->assertSee('x-ref="track"', false);
        // Verify old price, new price, and discount percentage are displayed
        $response->assertSee('300.00');
        $response->assertSee('350.00');
        $response->assertSee('-14%');
        $response->assertSee('line-through');
    }

    public function test_product_detail_page_renders_related_product_carousel_and_discount_percentage()
    {
        $category = Category::create([
            'name' => 'Sensors',
            'slug' => 'sensors',
            'is_active' => true,
        ]);

        $mainProduct = Product::create([
            'name' => 'DHT22 Temperature & Humidity Sensor',
            'slug' => 'dht22-sensor-module',
            'sku' => 'DHT22-001',
            'category_id' => $category->id,
            'purchase_price' => 150,
            'selling_price' => 250,
            'discount_price' => 200, // 20% discount
            'stock_quantity' => 30,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'BME280 Pressure Sensor',
            'slug' => 'bme280-pressure-sensor',
            'sku' => 'BME280-002',
            'category_id' => $category->id,
            'purchase_price' => 180,
            'selling_price' => 300,
            'discount_price' => 240, // 20% discount
            'stock_quantity' => 25,
            'is_active' => true,
        ]);

        $response = $this->get(route('product.show', $mainProduct->slug));

        $response->assertStatus(200);
        $response->assertSee('productCarousel');
        $response->assertSee('People Also Bought');
        // Verify discount badge and old regular price on product detail
        $response->assertSee('-20% OFF');
        $response->assertSee('250.00');
    }
}

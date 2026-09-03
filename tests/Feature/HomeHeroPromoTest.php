<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeHeroPromoTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_latest_real_products_in_hero_promo_strip(): void
    {
        $cat = Category::create([
            'name' => 'Microcontrollers',
            'slug' => 'microcontrollers',
            'is_active' => true,
        ]);

        $prod1 = Product::create([
            'name' => 'Real Live Product ESP32-S3 Cam',
            'slug' => 'real-live-product-esp32-s3-cam',
            'sku' => 'ESP32S3-CAM',
            'category_id' => $cat->id,
            'purchase_price' => 500,
            'selling_price' => 850,
            'discount_price' => 750,
            'stock_quantity' => 20,
            'is_active' => true,
        ]);

        $prod2 = Product::create([
            'name' => 'Real Live Product STM32 BlackPill',
            'slug' => 'real-live-product-stm32-blackpill',
            'sku' => 'STM32-BP',
            'category_id' => $cat->id,
            'purchase_price' => 300,
            'selling_price' => 450,
            'discount_price' => 400,
            'stock_quantity' => 30,
            'is_active' => true,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        // Check that the real latest products appear in the promo strip
        $response->assertSee('Real Live Product ESP32-S3 Cam');
        $response->assertSee('Real Live Product STM32 BlackPill');
        $response->assertSee(route('product.show', $prod1->slug));
        $response->assertSee(route('product.show', $prod2->slug));
    }
}

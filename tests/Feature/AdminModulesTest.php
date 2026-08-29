<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\LandingPage;
use App\Models\Product;
use Tests\TestCase;

class AdminModulesTest extends TestCase
{
    public function test_home_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('DREAMERS');
        $response->assertSee('Flash Sale');
    }

    public function test_shop_catalog_renders(): void
    {
        $response = $this->get('/shop');
        $response->assertStatus(200);
        $response->assertSee('Catalog Shop');
    }

    public function test_cart_page_renders(): void
    {
        $response = $this->get('/cart');
        $response->assertStatus(200);
    }

    public function test_cart_json_api(): void
    {
        $response = $this->get('/cart/json');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'items', 'count', 'summary']);
    }

    public function test_customer_login_page_renders(): void
    {
        $response = $this->get('/customer/login');
        $response->assertStatus(200);
        $response->assertSee('Customer Login');

        $loginShortcut = $this->get('/login');
        $loginShortcut->assertStatus(200);
    }

    public function test_guest_redirected_to_customer_login(): void
    {
        $response = $this->get('/customer/dashboard');
        $response->assertRedirect('/customer/login');
    }

    public function test_customer_register_page_renders(): void
    {
        $response = $this->get('/customer/register');
        $response->assertStatus(200);
        $response->assertSee('Create Customer Account');
    }

    public function test_customer_authenticated_dashboard_and_orders(): void
    {
        $customer = \App\Models\Customer::firstOrCreate(
            ['phone' => '01711223344'],
            [
                'name' => 'Salman Chowdhury',
                'email' => 'salman@dreamerspcb.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'city' => 'Dhaka',
                'is_active' => true,
            ]
        );

        // Authenticated customer access
        $response = $this->actingAs($customer, 'customer')->get('/customer/dashboard');
        $response->assertStatus(200);
        $response->assertSee($customer->name);

        $ordersResponse = $this->actingAs($customer, 'customer')->get('/customer/orders');
        $ordersResponse->assertStatus(200);

        $profileResponse = $this->actingAs($customer, 'customer')->get('/customer/profile');
        $profileResponse->assertStatus(200);

        $wishlistResponse = $this->actingAs($customer, 'customer')->get('/customer/wishlist');
        $wishlistResponse->assertStatus(200);
    }

    public function test_track_order_page_renders(): void
    {
        $response = $this->get('/track-order');
        $response->assertStatus(200);
        $response->assertSee('Tracking');

        $order = \App\Models\Order::first();
        if ($order) {
            $trackResponse = $this->get('/track-order?order_no=' . $order->order_no);
            $trackResponse->assertStatus(200);
            $trackResponse->assertSee($order->order_no);
        }
    }

    public function test_checkout_submission_process(): void
    {
        $product = Product::first();
        if ($product) {
            // 1. Add to cart
            $this->post('/cart/add', [
                'product_id' => $product->id,
                'quantity' => 2
            ]);

            // 2. Submit checkout
            $response = $this->post('/checkout/process', [
                'shipping_name' => 'Salman Chowdhury',
                'shipping_phone' => '01711223344',
                'shipping_city' => 'Dhaka',
                'shipping_address' => 'House 12, Road 4, Dhanmondi',
                'shipping_area' => 'inside_dhaka',
                'payment_method' => 'cash_on_delivery',
                'notes' => 'Test order placement'
            ]);

            $response->assertRedirect();
        }
    }

    public function test_product_detail_page_renders(): void
    {
        $product = Product::first();
        if ($product) {
            $response = $this->get('/product/' . $product->slug);
            $response->assertStatus(200);
            $response->assertSee($product->name);
        } else {
            $this->assertTrue(true);
        }
    }

    public function test_admin_dashboard_renders_successfully(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('DREAMERS');
    }

    public function test_pos_terminal_renders(): void
    {
        $response = $this->get('/admin/pos');
        $response->assertStatus(200);
        $response->assertSee('POS');
    }

    public function test_orders_pipeline_renders(): void
    {
        $response = $this->get('/admin/orders');
        $response->assertStatus(200);
    }

    public function test_products_index_renders(): void
    {
        $response = $this->get('/admin/products');
        $response->assertStatus(200);
    }

    public function test_categories_index_renders(): void
    {
        $response = $this->get('/admin/categories');
        $response->assertStatus(200);
    }

    public function test_brands_index_renders(): void
    {
        $response = $this->get('/admin/brands');
        $response->assertStatus(200);
    }

    public function test_attributes_index_renders(): void
    {
        $response = $this->get('/admin/attributes');
        $response->assertStatus(200);
    }

    public function test_purchases_index_renders(): void
    {
        $response = $this->get('/admin/purchases');
        $response->assertStatus(200);
    }

    public function test_suppliers_index_renders(): void
    {
        $response = $this->get('/admin/suppliers');
        $response->assertStatus(200);
    }

    public function test_coupons_index_renders(): void
    {
        $response = $this->get('/admin/coupons');
        $response->assertStatus(200);
    }

    public function test_landing_pages_index_renders(): void
    {
        $response = $this->get('/admin/landing-pages');
        $response->assertStatus(200);
    }

    public function test_fraud_checks_index_renders(): void
    {
        $response = $this->get('/admin/fraud-checks');
        $response->assertStatus(200);
    }

    public function test_sms_marketing_index_renders(): void
    {
        $response = $this->get('/admin/sms-marketing');
        $response->assertStatus(200);
    }

    public function test_accounts_index_renders(): void
    {
        $response = $this->get('/admin/accounts');
        $response->assertStatus(200);
    }

    public function test_expenses_localized_bengali_cards_render(): void
    {
        $response = $this->get('/admin/expenses');
        $response->assertStatus(200);
        $response->assertSee('বর্তমানে তহবিলে অবশিষ্ট ব্যালেন্স');
        $response->assertSee('এই বছরে মোট খরচ হয়েছে');
        $response->assertSee('এই মাসে মোট খরচ হয়েছে');
        $response->assertSee('আজকের মোট খরচ');
    }

    public function test_users_index_renders(): void
    {
        $response = $this->get('/admin/users');
        $response->assertStatus(200);
    }

    public function test_roles_index_renders(): void
    {
        $response = $this->get('/admin/roles');
        $response->assertStatus(200);
    }

    public function test_customers_index_renders(): void
    {
        $response = $this->get('/admin/customers');
        $response->assertStatus(200);
    }

    public function test_general_settings_renders(): void
    {
        $response = $this->get('/admin/settings/general');
        $response->assertStatus(200);
    }

    public function test_email_settings_renders(): void
    {
        $response = $this->get('/admin/settings/email');
        $response->assertStatus(200);
    }

    public function test_api_hub_settings_renders(): void
    {
        $response = $this->get('/admin/settings/api-hub');
        $response->assertStatus(200);
    }

    public function test_banners_index_renders(): void
    {
        $response = $this->get('/admin/banners');
        $response->assertStatus(200);
    }

    public function test_reports_index_renders(): void
    {
        $response = $this->get('/admin/reports');
        $response->assertStatus(200);
    }

    public function test_seo_settings_renders(): void
    {
        $response = $this->get('/admin/settings/seo');
        $response->assertStatus(200);
    }

    public function test_sitemap_settings_renders(): void
    {
        $response = $this->get('/admin/settings/sitemap');
        $response->assertStatus(200);
        $response->assertSee('XML Sitemap');
    }

    public function test_system_tools_renders(): void
    {
        $response = $this->get('/admin/system/tools');
        $response->assertStatus(200);
    }

    public function test_sitemap_xml_renders(): void
    {
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
    }

    public function test_product_create_and_edit_page_renders(): void
    {
        $response = $this->get('/admin/products/create');
        $response->assertStatus(200);
        $response->assertSee('Primary Product Thumbnail');

        $product = Product::first();
        if ($product) {
            $editResponse = $this->get('/admin/products/' . $product->id . '/edit');
            $editResponse->assertStatus(200);
        }
    }

    public function test_product_store_with_uploaded_images(): void
    {
        $category = Category::first();
        $thumbnail = \Illuminate\Http\UploadedFile::fake()->image('stm32_thumb.jpg', 600, 600);
        $galleryImage = \Illuminate\Http\UploadedFile::fake()->image('stm32_gal1.jpg', 800, 800);

        $sku = 'TEST-PCB-' . rand(1000, 9999);

        $response = $this->post('/admin/products', [
            'name' => 'ESP32-S3 Dual Core AI Dev Board',
            'sku' => $sku,
            'category_id' => $category ? $category->id : 1,
            'purchase_price' => 250,
            'selling_price' => 450,
            'stock_quantity' => 25,
            'thumbnail' => $thumbnail,
            'gallery_images' => [$galleryImage],
        ]);

        $response->assertRedirect('/admin/products');

        $createdProduct = Product::where('sku', $sku)->first();
        $this->assertNotNull($createdProduct);
        $this->assertStringContainsString('/uploads/products/', $createdProduct->thumbnail);
        $this->assertGreaterThanOrEqual(1, $createdProduct->images()->count());
    }

    public function test_footer_settings_page_renders(): void
    {
        $response = $this->get('/admin/settings/footer');
        $response->assertStatus(200);
        $response->assertSee('Footer Information');
    }

    public function test_footer_settings_update_crud_persists(): void
    {
        $testHotline = '+880 1999-887766';
        $testBio = 'DREAMERS PCB - Next Gen Electronics Laboratory';

        $response = $this->post('/admin/settings/footer', [
            'footer_hotline' => $testHotline,
            'footer_about' => $testBio,
            'footer_email' => 'tech@dreamerspcb.com',
            'footer_facebook_url' => 'https://facebook.com/dreamerspcb',
        ]);

        $response->assertRedirect('/admin/settings/footer');
        $this->assertEquals($testHotline, \App\Models\Setting::get('footer_hotline'));
        $this->assertEquals($testBio, \App\Models\Setting::get('footer_about'));
    }
}

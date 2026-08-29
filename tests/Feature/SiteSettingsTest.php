<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            ['name' => 'Dreamers Admin', 'password' => Hash::make('password')]
        );
    }

    /**
     * Test guest cannot access site settings hub.
     */
    public function test_guest_cannot_access_settings_hub(): void
    {
        $response = $this->get('/admin/settings/general');
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test admin can access site settings hub with all 7 tabs.
     */
    public function test_admin_can_access_settings_hub(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get('/admin/settings/general');
        $response->assertStatus(200);
        $response->assertSee('Enterprise Site Settings Hub');
        $response->assertSee('Identity', false);
        $response->assertSee('Shipping', false);
        $response->assertSee('Currency', false);
        $response->assertSee('Invoices', false);
        $response->assertSee('Notices', false);
        $response->assertSee('Social', false);
        $response->assertSee('Custom Scripts', false);
    }

    /**
     * Test admin can update general text, delivery, and invoice settings.
     */
    public function test_admin_can_update_general_settings(): void
    {
        $response = $this->actingAs($this->admin, 'web')->put('/admin/settings/general', [
            'site_name' => 'DREAMERS PCB Enterprise Pro',
            'site_tagline' => 'Next-Gen Robotics & Hardware',
            'site_phone' => '+880 1871-279555',
            'site_email' => 'contact@dreamerspcb.com',
            'inside_dhaka_shipping' => '80',
            'outside_dhaka_shipping' => '150',
            'free_shipping_min_amount' => '4000',
            'currency_symbol' => '৳',
            'currency_code' => 'BDT',
            'invoice_title' => 'DREAMERS PCB - OFFICIAL BILL',
            'invoice_trade_license' => 'TRAD/2026/998811',
            'announcement_enabled' => '1',
            'announcement_text' => '🔥 Special August Flash Sale Live Now!',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('DREAMERS PCB Enterprise Pro', Setting::get('site_name'));
        $this->assertEquals('Next-Gen Robotics & Hardware', Setting::get('site_tagline'));
        $this->assertEquals('+880 1871-279555', Setting::get('site_phone'));
        $this->assertEquals('contact@dreamerspcb.com', Setting::get('site_email'));
        $this->assertEquals('80', Setting::get('inside_dhaka_shipping'));
        $this->assertEquals('150', Setting::get('outside_dhaka_shipping'));
        $this->assertEquals('4000', Setting::get('free_shipping_min_amount'));
        $this->assertEquals('DREAMERS PCB - OFFICIAL BILL', Setting::get('invoice_title'));
        $this->assertEquals('1', Setting::get('announcement_enabled'));
        $this->assertEquals('🔥 Special August Flash Sale Live Now!', Setting::get('announcement_text'));
    }

    /**
     * Test uploading store branding logo and favicon.
     */
    public function test_admin_can_upload_branding_assets(): void
    {
        $logo = UploadedFile::fake()->image('test_logo.png', 200, 60);

        $response = $this->actingAs($this->admin, 'web')->put('/admin/settings/general', [
            'site_logo' => $logo,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $logoPath = Setting::get('site_logo');
        $this->assertNotEmpty($logoPath);
        $this->assertStringContainsString('/uploads/settings/', $logoPath);

        // Verify physical file was saved in public directory
        $fullPath = public_path($logoPath);
        $this->assertTrue(File::exists($fullPath));

        // Clean up test file
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    /**
     * Test toggles default to 0 when unchecked.
     */
    public function test_toggles_save_zero_when_unchecked(): void
    {
        $response = $this->actingAs($this->admin, 'web')->put('/admin/settings/general', [
            'site_name' => 'DREAMERS PCB',
            // Omit announcement_enabled, cod_enabled, maintenance_mode
        ]);

        $response->assertRedirect();
        $this->assertEquals('0', Setting::get('announcement_enabled'));
        $this->assertEquals('0', Setting::get('cod_enabled'));
        $this->assertEquals('0', Setting::get('maintenance_mode'));
    }

    /**
     * Test admin can customize website theme colors and see them reflected on storefront.
     */
    public function test_admin_can_update_theme_colors(): void
    {
        $response = $this->actingAs($this->admin, 'web')->put('/admin/settings/general', [
            'theme_primary_color' => '#2563eb',
            'theme_primary_hover' => '#1d4ed8',
            'theme_secondary_color' => '#10b981',
            'theme_header_bg' => '#0f172a',
            'theme_announcement_bg' => '#1e293b',
            'theme_announcement_text_color' => '#60a5fa',
            'theme_footer_bg' => '#0b1120',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('#2563eb', Setting::get('theme_primary_color'));
        $this->assertEquals('#1d4ed8', Setting::get('theme_primary_hover'));
        $this->assertEquals('#10b981', Setting::get('theme_secondary_color'));
        $this->assertEquals('#0f172a', Setting::get('theme_header_bg'));
        $this->assertEquals('#1e293b', Setting::get('theme_announcement_bg'));
        $this->assertEquals('#60a5fa', Setting::get('theme_announcement_text_color'));
        $this->assertEquals('#0b1120', Setting::get('theme_footer_bg'));

        // Verify storefront renders with injected theme colors
        $storefront = $this->get('/');
        $storefront->assertStatus(200);
        $storefront->assertSee('#2563eb');
    }

    /**
     * Test admin can configure Google Analytics, GTM, Search Console, Meta Pixel, and TikTok Pixel.
     */
    public function test_admin_can_configure_google_and_pixel_tracking(): void
    {
        $response = $this->actingAs($this->admin, 'web')->put('/admin/settings/general', [
            'google_analytics_enabled' => '1',
            'google_analytics_id' => 'G-ABC1234567',
            'google_tag_manager_enabled' => '1',
            'google_tag_manager_id' => 'GTM-TEST999',
            'google_site_verification' => 'google-token-verification-code-xyz',
            'facebook_pixel_enabled' => '1',
            'facebook_pixel_id' => '987654321012345',
            'tiktok_pixel_enabled' => '1',
            'tiktok_pixel_id' => 'CTIKTOKTEST888',
            'pexels_api_key' => 'pexels-secret-key-12345',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('1', Setting::get('google_analytics_enabled'));
        $this->assertEquals('G-ABC1234567', Setting::get('google_analytics_id'));
        $this->assertEquals('1', Setting::get('google_tag_manager_enabled'));
        $this->assertEquals('GTM-TEST999', Setting::get('google_tag_manager_id'));
        $this->assertEquals('google-token-verification-code-xyz', Setting::get('google_site_verification'));
        $this->assertEquals('1', Setting::get('facebook_pixel_enabled'));
        $this->assertEquals('987654321012345', Setting::get('facebook_pixel_id'));
        $this->assertEquals('1', Setting::get('tiktok_pixel_enabled'));
        $this->assertEquals('CTIKTOKTEST888', Setting::get('tiktok_pixel_id'));
        $this->assertEquals('pexels-secret-key-12345', Setting::get('pexels_api_key'));

        // Verify storefront renders GA4, GTM, Meta Pixel, and verification tags
        $storefront = $this->get('/');
        $storefront->assertStatus(200);
        $storefront->assertSee('G-ABC1234567');
        $storefront->assertSee('GTM-TEST999');
        $storefront->assertSee('google-token-verification-code-xyz');
        $storefront->assertSee('987654321012345');
        $storefront->assertSee('CTIKTOKTEST888');
    }
}

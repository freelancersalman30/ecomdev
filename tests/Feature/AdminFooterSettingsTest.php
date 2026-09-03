<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminFooterSettingsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            ['name' => 'Dreamers Admin', 'password' => Hash::make('password')]
        );
        $this->actingAs($admin, 'web');
    }

    public function test_footer_settings_page_renders_with_popular_categories(): void
    {
        $response = $this->get('/admin/settings/footer');
        $response->assertStatus(200);
        $response->assertSee('Footer Popular Categories Links');
    }

    public function test_admin_can_update_and_manage_popular_categories(): void
    {
        $categories = [
            ['title' => 'Custom FPGA Development Boards', 'url' => '/shop?search=FPGA'],
            ['title' => 'Hot Air SMD Rework Stations', 'url' => '/shop?search=Quick861'],
            ['title' => 'Sensors & Relay Kits', 'url' => '/shop?search=Sensors'],
        ];

        $response = $this->post('/admin/settings/footer', [
            'footer_about' => 'Leading Electronics Superstore in Bangladesh',
            'footer_hotline' => '+880 1700-112233',
            'popular_categories' => $categories,
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect('/admin/settings/footer');

        $savedJson = Setting::get('footer_popular_categories');
        $this->assertNotNull($savedJson);
        $decoded = json_decode($savedJson, true);
        $this->assertCount(3, $decoded);
        $this->assertEquals('Custom FPGA Development Boards', $decoded[0]['title']);
        $this->assertEquals('/shop?search=FPGA', $decoded[0]['url']);

        // Test that the home page footer renders the updated categories
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Custom FPGA Development Boards');
        $homeResponse->assertSee('/shop?search=FPGA');
    }
}

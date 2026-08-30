<?php

namespace Tests\Feature;

use App\Models\ApiSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminApiHubTest extends TestCase
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

    public function test_api_hub_page_renders(): void
    {
        $response = $this->get('/admin/settings/api-hub');
        $response->assertStatus(200);
        $response->assertSee('Steadfast Courier API');
        $response->assertSee('BulkSMS BD Gateway');
    }

    public function test_get_on_save_url_redirects_to_api_hub(): void
    {
        $response = $this->get('/admin/settings/api-hub/save');
        $response->assertRedirect('/admin/settings/api-hub');
    }

    public function test_api_hub_can_save_bulksms_bd(): void
    {
        $response = $this->put('/admin/settings/api-hub/save', [
            'provider' => 'bulksms_bd',
            'api_key' => 'my_bulksms_secret_key_123',
            'sender_id' => 'MYBRAND',
            'is_active' => '1',
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $setting = ApiSetting::where('provider', 'bulksms_bd')->first();
        $this->assertNotNull($setting);
        $this->assertEquals('sms', $setting->type);
        $this->assertEquals('my_bulksms_secret_key_123', $setting->api_key);
        $this->assertEquals('MYBRAND', $setting->sender_id);
        $this->assertTrue($setting->is_active);
    }

    public function test_api_hub_can_save_steadfast(): void
    {
        $response = $this->put('/admin/settings/api-hub/save', [
            'provider' => 'steadfast',
            'api_key' => 'sf_live_key_xyz',
            'secret_key' => 'sf_live_secret_xyz',
            'is_active' => '1',
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $setting = ApiSetting::where('provider', 'steadfast')->first();
        $this->assertNotNull($setting);
        $this->assertEquals('courier', $setting->type);
        $this->assertEquals('sf_live_key_xyz', $setting->api_key);
        $this->assertEquals('sf_live_secret_xyz', $setting->secret_key);
    }
}

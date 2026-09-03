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
        $response->assertSee('Custom Bulk SMS Gateway');
        $response->assertSee('Bulk SMS Dhaka Gateway');
        $response->assertSee('Steadfast Courier API');
        $response->assertSee('BulkSMS BD Gateway');
        $response->assertSee('bKash Payment Gateway');
    }

    public function test_get_on_save_url_redirects_to_api_hub(): void
    {
        $response = $this->get('/admin/settings/api-hub/save');
        $response->assertRedirect('/admin/settings/api-hub');
    }

    public function test_api_hub_can_save_and_activate_custom_sms_gateway(): void
    {
        $response = $this->put('/admin/settings/api-hub/save', [
            'provider' => 'custom_sms',
            'gateway_name' => 'GreenWeb SMS Provider',
            'http_method' => 'GET',
            'endpoint_url' => 'https://api.greenweb.com.bd/api.php',
            'api_key_param' => 'token',
            'api_key_value' => 'my_greenweb_token_abc',
            'phone_param' => 'to',
            'message_param' => 'message',
            'success_keyword' => 'Ok',
            'is_active' => '1',
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $setting = ApiSetting::where('provider', 'custom_sms')->first();
        $this->assertNotNull($setting);
        $this->assertEquals('sms', $setting->type);
        $this->assertEquals('https://api.greenweb.com.bd/api.php', $setting->credentials['endpoint_url']);
        $this->assertEquals('my_greenweb_token_abc', $setting->credentials['api_key_value']);
        $this->assertTrue($setting->is_active);
    }

    public function test_api_hub_can_save_and_activate_bulksmsdhaka(): void
    {
        $response = $this->put('/admin/settings/api-hub/save', [
            'provider' => 'bulksmsdhaka',
            'api_key' => 'dhaka_key_live_998',
            'caller_id' => '1234',
            'is_active' => '1',
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $setting = ApiSetting::where('provider', 'bulksmsdhaka')->first();
        $this->assertNotNull($setting);
        $this->assertEquals('sms', $setting->type);
        $this->assertEquals('dhaka_key_live_998', $setting->api_key);
        $this->assertEquals('1234', $setting->caller_id);
        $this->assertTrue($setting->is_active);
    }

    public function test_api_hub_can_save_and_activate_bulksms_bd(): void
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

    public function test_api_hub_can_deactivate_bulksms_bd(): void
    {
        // First activate
        ApiSetting::updateOrCreate(
            ['provider' => 'bulksms_bd'],
            ['is_active' => true, 'type' => 'sms', 'title' => 'BulkSMS BD Gateway']
        );

        // Submit form with is_active = 0 (or unchecked)
        $response = $this->put('/admin/settings/api-hub/save', [
            'provider' => 'bulksms_bd',
            'api_key' => 'my_bulksms_secret_key_123',
            'sender_id' => 'MYBRAND',
            'is_active' => '0',
        ]);

        $response->assertSessionHas('success');
        $setting = ApiSetting::where('provider', 'bulksms_bd')->first();
        $this->assertFalse($setting->is_active);
    }

    public function test_api_hub_can_save_and_activate_steadfast(): void
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
        $this->assertTrue($setting->is_active);
    }

    public function test_api_hub_can_deactivate_steadfast(): void
    {
        ApiSetting::updateOrCreate(
            ['provider' => 'steadfast'],
            ['is_active' => true, 'type' => 'courier', 'title' => 'Steadfast Courier API']
        );

        $response = $this->put('/admin/settings/api-hub/save', [
            'provider' => 'steadfast',
            'api_key' => 'sf_live_key_xyz',
            'secret_key' => 'sf_live_secret_xyz',
            'is_active' => '0',
        ]);

        $response->assertSessionHas('success');
        $setting = ApiSetting::where('provider', 'steadfast')->first();
        $this->assertFalse($setting->is_active);
    }

    public function test_api_hub_can_save_and_toggle_bkash(): void
    {
        // Save and Activate bKash
        $response = $this->put('/admin/settings/api-hub/save', [
            'provider' => 'bkash',
            'app_key' => 'bkash_app_key_live_123',
            'app_secret' => 'bkash_app_sec_live_456',
            'username' => '01711223344',
            'password' => 'secret_bkash_pass',
            'is_sandbox' => '0',
            'is_active' => '1',
        ]);

        $response->assertSessionHas('success');
        $setting = ApiSetting::where('provider', 'bkash')->first();
        $this->assertNotNull($setting);
        $this->assertEquals('payment', $setting->type);
        $this->assertEquals('bkash_app_key_live_123', $setting->app_key);
        $this->assertEquals('bkash_app_sec_live_456', $setting->app_secret);
        $this->assertEquals('01711223344', $setting->username);
        $this->assertEquals('secret_bkash_pass', $setting->password);
        $this->assertFalse($setting->is_sandbox);
        $this->assertTrue($setting->is_active);

        // Deactivate bKash
        $response2 = $this->put('/admin/settings/api-hub/save', [
            'provider' => 'bkash',
            'is_active' => '0',
        ]);
        $response2->assertSessionHas('success');
        $setting->refresh();
        $this->assertFalse($setting->is_active);
    }

    public function test_test_connection_endpoint_for_providers(): void
    {
        // Test Custom SMS Connection Endpoint
        $customResponse = $this->postJson('/admin/settings/api-hub/test', [
            'provider' => 'custom_sms',
            'endpoint_url' => 'https://api.example.com/sms',
            'api_key_value' => 'demo_key_123',
        ]);
        $customResponse->assertStatus(200);

        // Test BulkSMS Dhaka Connection Endpoint
        $dhakaResponse = $this->postJson('/admin/settings/api-hub/test', [
            'provider' => 'bulksmsdhaka',
            'api_key' => 'demo_dhaka_key',
            'caller_id' => '1234',
        ]);
        $dhakaResponse->assertStatus(200);
        $dhakaResponse->assertJson(['success' => true]);

        // Test Steadfast Connection Endpoint
        $sfResponse = $this->postJson('/admin/settings/api-hub/test', [
            'provider' => 'steadfast',
            'api_key' => 'sf_test_key',
            'secret_key' => 'sf_test_secret',
        ]);
        $sfResponse->assertStatus(200);
        $sfResponse->assertJson(['success' => true]);

        // Test BulkSMS Connection Endpoint
        $smsResponse = $this->postJson('/admin/settings/api-hub/test', [
            'provider' => 'bulksms',
            'api_key' => 'my_bulksms_demo_key',
        ]);
        $smsResponse->assertStatus(200);
        $smsResponse->assertJson(['success' => true]);

        // Test bKash Connection Endpoint
        $bkashResponse = $this->postJson('/admin/settings/api-hub/test', [
            'provider' => 'bkash',
            'app_key' => 'bkash_demo_app_key',
            'app_secret' => 'bkash_demo_app_secret',
            'username' => '01700112233',
            'password' => 'pass1234',
            'is_sandbox' => 1,
        ]);
        $bkashResponse->assertStatus(200);
        $bkashResponse->assertJson(['success' => true]);
    }
}

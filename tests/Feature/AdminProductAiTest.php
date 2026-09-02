<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductAiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_generate_ai_description_and_seo_for_product()
    {
        $user = User::factory()->create([
            'email' => 'admin@dreamerspcb.com',
        ]);

        $response = $this->actingAs($user)->postJson(route('admin.products.ai.generate'), [
            'name' => 'ESP32-WROOM-32D Dual Core Microcontroller Board',
            'category' => 'Microcontrollers',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'description',
            'short_description',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'source',
        ]);
        $this->assertTrue($response->json('success'));
        $this->assertNotEmpty($response->json('meta_title'));
        $this->assertNotEmpty($response->json('meta_description'));
        $this->assertNotEmpty($response->json('meta_keywords'));
        $this->assertStringContainsString('Product Overview', $response->json('description'));
        $this->assertStringContainsString('Technical Specifications', $response->json('description'));
    }

    public function test_admin_can_view_and_update_gemini_settings()
    {
        $user = User::factory()->create([
            'email' => 'admin@dreamerspcb.com',
        ]);

        // 1. View settings page
        $getResponse = $this->actingAs($user)->get(route('admin.settings.gemini'));
        $getResponse->assertStatus(200);
        $getResponse->assertSee('Google Gemini AI Settings');

        // 2. Update settings
        $putResponse = $this->actingAs($user)->put(route('admin.settings.gemini.update'), [
            'gemini_api_key' => 'AIzaSyTestApiKey123456789',
            'gemini_model' => 'gemini-1.5-flash',
            'gemini_temperature' => '0.3',
            'gemini_auto_seo' => '1',
        ]);

        $putResponse->assertRedirect(route('admin.settings.gemini'));
        $this->assertEquals('AIzaSyTestApiKey123456789', Setting::get('gemini_api_key'));
        $this->assertEquals('gemini-1.5-flash', Setting::get('gemini_model'));
        $this->assertEquals('0.3', Setting::get('gemini_temperature'));
    }
}

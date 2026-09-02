<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductAiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_generate_ai_description_for_product()
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
            'source',
        ]);
        $this->assertTrue($response->json('success'));
        $this->assertStringContainsString('Product Overview', $response->json('description'));
        $this->assertStringContainsString('Technical Specifications', $response->json('description'));
    }
}

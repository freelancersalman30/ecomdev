<?php

namespace Tests\Feature;

use App\Models\ApiSetting;
use App\Models\FraudCheck;
use App\Models\User;
use App\Services\FraudCheckService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminFraudApiIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected FraudCheckService $fraudService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            ['name' => 'Dreamers Admin', 'password' => Hash::make('password')]
        );

        $this->fraudService = app(FraudCheckService::class);
    }

    public function test_admin_can_view_fraud_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.fraud.index'));

        $response->assertStatus(200);
        $response->assertSee('Fraud & Courier Risk Detection Hub', false);
        $response->assertSee('Zachaikori API', false);
        $response->assertSee('Universal Custom API', false);
    }

    public function test_admin_can_save_zachaikori_api_settings(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.fraud.api_settings'), [
            'provider' => 'zachaikori',
            'endpoint_url' => 'https://api.zachaikori.com/api/v1/check',
            'api_key' => 'test_zachaikori_token_123',
            'http_method' => 'GET',
            'phone_param' => 'phone',
            'min_success_rate' => 65,
            'is_active' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('api_settings', [
            'provider' => 'zachaikori',
            'type' => 'fraud',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_save_universal_fraud_api_settings(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.fraud.api_settings'), [
            'provider' => 'universal_fraud',
            'provider_name' => 'FraudChecker BD',
            'endpoint_url' => 'https://api.fraudcheckerbd.com/check',
            'http_method' => 'POST_JSON',
            'auth_header_name' => 'Authorization',
            'auth_header_value' => 'Bearer secret_token_xyz',
            'phone_param' => 'mobile',
            'success_rate_key' => 'delivery_rate',
            'total_orders_key' => 'total_parcels',
            'delivered_orders_key' => 'delivered_count',
            'cancelled_orders_key' => 'return_count',
            'is_active' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('api_settings', [
            'provider' => 'universal_fraud',
            'type' => 'fraud',
            'is_active' => true,
        ]);
    }

    public function test_fraud_check_queries_zachaikori_api_with_mock(): void
    {
        Http::fake([
            'https://api.zachaikori.com/*' => Http::response([
                'success' => true,
                'total_parcels' => 10,
                'delivered_parcels' => 9,
                'cancelled_parcels' => 1,
                'success_rate' => 90.00,
                'risk_level' => 'low',
                'is_blacklisted' => false,
                'message' => 'Good buyer profile with Steadfast and Pathao',
            ], 200),
        ]);

        ApiSetting::create([
            'provider' => 'zachaikori',
            'type' => 'fraud',
            'title' => 'Zachaikori Fraud & Courier Risk API',
            'credentials' => [
                'endpoint_url' => 'https://api.zachaikori.com/api/v1/check',
                'api_key' => 'valid_token_xyz',
                'http_method' => 'GET',
                'phone_param' => 'phone',
            ],
            'is_active' => true,
        ]);

        $result = $this->fraudService->checkExternalApi('01711223344');

        $this->assertTrue($result['success']);
        $this->assertEquals('zachaikori', $result['provider']);
        $this->assertEquals(90.00, $result['success_rate']);
        $this->assertEquals(10, $result['total_parcels']);
        $this->assertEquals(9, $result['delivered_parcels']);
        $this->assertEquals(1, $result['cancelled_parcels']);

        $this->assertDatabaseHas('fraud_checks', [
            'phone' => '01711223344',
            'courier_success_rate' => 90.00,
            'total_parcels' => 10,
        ]);
    }

    public function test_fraud_check_queries_universal_api_with_mock(): void
    {
        Http::fake([
            'https://api.universal-fraud.com/*' => Http::response([
                'status' => 'success',
                'data' => [
                    'delivery_rate' => 30.00,
                    'total' => 10,
                    'delivered' => 3,
                    'returned' => 7,
                    'risk' => 'critical',
                    'is_fraud' => true,
                    'note' => 'High return rate across multiple merchants',
                ],
            ], 200),
        ]);

        ApiSetting::create([
            'provider' => 'universal_fraud',
            'type' => 'fraud',
            'title' => 'Universal Custom Fraud Gateway',
            'credentials' => [
                'provider_name' => 'Custom Fraud API',
                'endpoint_url' => 'https://api.universal-fraud.com/check',
                'http_method' => 'POST_JSON',
                'phone_param' => 'phone',
                'auth_header_name' => 'X-API-KEY',
                'auth_header_value' => 'secret_123',
                'success_rate_key' => 'data.delivery_rate',
                'total_orders_key' => 'data.total',
                'delivered_orders_key' => 'data.delivered',
                'cancelled_orders_key' => 'data.returned',
                'risk_level_key' => 'data.risk',
            ],
            'is_active' => true,
        ]);

        $evaluation = $this->fraudService->evaluateOrder('01899887766');

        $this->assertTrue($evaluation['is_fraud_suspect']);
        $this->assertGreaterThanOrEqual(75, $evaluation['score']);
        $this->assertEquals('critical', $evaluation['risk_level']);
        $this->assertEquals(30.00, $evaluation['success_rate']);
    }

    public function test_admin_can_test_api_connection_via_ajax(): void
    {
        Http::fake([
            'https://api.test-gateway.com/*' => Http::response([
                'success_rate' => 85.00,
                'total_parcels' => 20,
                'delivered_parcels' => 17,
                'cancelled_parcels' => 3,
            ], 200),
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.fraud.test_api'), [
            'provider' => 'universal_fraud',
            'endpoint_url' => 'https://api.test-gateway.com/check',
            'http_method' => 'GET',
            'phone' => '01711223344',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'provider' => 'universal_fraud',
            'success_rate' => 85.00,
        ]);
    }

    public function test_admin_can_blacklist_and_remove_blacklist(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.fraud.blacklist'), [
            'phone' => '01799881122',
            'notes' => 'Fake order placed repeatedly',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('fraud_checks', [
            'phone' => '01799881122',
            'is_blacklisted' => true,
        ]);

        $record = FraudCheck::where('phone', '01799881122')->first();
        $removeResponse = $this->actingAs($this->admin)->post(route('admin.fraud.blacklist.remove', $record->id));

        $removeResponse->assertRedirect();
        $this->assertDatabaseHas('fraud_checks', [
            'id' => $record->id,
            'is_blacklisted' => false,
        ]);
    }
}

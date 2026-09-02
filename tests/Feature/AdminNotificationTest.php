<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Services\AdminNotificationService;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminNotificationTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            ['name' => 'Dreamers Admin', 'password' => Hash::make('password')]
        );
        $this->actingAs($this->admin, 'web');
    }

    public function test_notification_center_renders_successfully(): void
    {
        $response = $this->get(route('admin.notifications.index'));
        $response->assertStatus(200);
        $response->assertSee('Notifications Center');
    }

    public function test_notification_latest_json_endpoint(): void
    {
        $response = $this->getJson(route('admin.notifications.latest'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'unread_count',
            'notifications',
        ]);
    }

    public function test_notification_service_dispatches_order_notifications(): void
    {
        $order = Order::firstOrCreate(
            ['order_no' => 'DPCB-TEST-999'],
            [
                'customer_id' => null,
                'shipping_name' => 'Tanvir Ahmed',
                'shipping_phone' => '01711112233',
                'shipping_address' => 'Mirpur 10, Dhaka',
                'shipping_city' => 'Dhaka',
                'grand_total' => 2500.00,
                'status' => 'pending',
                'payment_method' => 'cash_on_delivery',
                'payment_status' => 'pending',
                'order_type' => 'online',
            ]
        );

        $service = app(AdminNotificationService::class);

        // 1. New Order Notification
        $service->notifyNewOrder($order);
        $this->assertTrue($this->admin->notifications()->where('data->type', 'new_order')->exists());

        // 2. Courier Assigned Notification
        $service->notifyCourierAssigned($order, 'Steadfast', 'S-TEST123456');
        $this->assertTrue($this->admin->notifications()->where('data->type', 'courier_assigned')->exists());

        // 3. Delivery Done Notification
        $service->notifyDeliveryDone($order);
        $this->assertTrue($this->admin->notifications()->where('data->type', 'delivery_done')->exists());
    }

    public function test_mark_as_read_and_mark_all_read(): void
    {
        $order = Order::firstOrCreate(
            ['order_no' => 'DPCB-TEST-1000'],
            [
                'shipping_name' => 'Test Buyer',
                'shipping_phone' => '01899998888',
                'shipping_address' => 'House 1, Road 2, Dhaka',
                'shipping_city' => 'Dhaka',
                'grand_total' => 1200.00,
                'status' => 'pending',
                'payment_method' => 'cash_on_delivery',
            ]
        );

        $service = app(AdminNotificationService::class);
        $service->notifyNewOrder($order);

        $notification = $this->admin->unreadNotifications()->first();
        $this->assertNotNull($notification);

        // Mark single as read
        $response = $this->post(route('admin.notifications.read', $notification->id));
        $response->assertStatus(302);
        $this->assertNotNull($notification->fresh()->read_at);

        // Mark all as read
        $service->notifyDeliveryDone($order);
        $this->assertGreaterThan(0, $this->admin->unreadNotifications()->count());

        $responseAll = $this->post(route('admin.notifications.mark_all_read'));
        $responseAll->assertStatus(302);
        $this->assertEquals(0, $this->admin->unreadNotifications()->count());
    }

    public function test_admin_layout_includes_bell_dropdown(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('adminNotificationDropdown');
        $response->assertSee('Notifications');
    }
}

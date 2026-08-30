<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Test admin login page renders successfully.
     */
    public function test_admin_login_page_renders(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('Administrator Authentication');
        $response->assertSee('admin@dreamerspcb.com');
    }

    /**
     * Test unauthenticated guest visiting protected admin route is redirected to admin login.
     */
    public function test_unauthenticated_guest_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test customer login page renders successfully.
     */
    public function test_customer_login_page_renders(): void
    {
        $response = $this->get('/customer/login');
        $response->assertStatus(200);
        $response->assertSee('Customer Login');

        $shortcutResponse = $this->get('/login');
        $shortcutResponse->assertStatus(200);
        $shortcutResponse->assertSee('Customer Login');
    }

    /**
     * Test unauthenticated guest visiting customer portal is redirected to customer login.
     */
    public function test_unauthenticated_guest_redirected_to_customer_login(): void
    {
        $response = $this->get('/customer/dashboard');
        $response->assertRedirect('/customer/login');
    }

    /**
     * Test admin can log in with valid credentials.
     */
    public function test_admin_can_login_with_valid_credentials(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            [
                'name' => 'Dreamers Admin',
                'password' => Hash::make('password'),
            ]
        );

        $response = $this->post('/admin/login', [
            'email' => 'admin@dreamerspcb.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin, 'web');
    }

    /**
     * Test admin cannot log in with invalid credentials.
     */
    public function test_admin_cannot_login_with_invalid_password(): void
    {
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => 'admin@dreamerspcb.com',
            'password' => 'wrong-password-xyz',
        ]);

        $response->assertRedirect('/admin/login');
        $response->assertSessionHas('error');
        $this->assertGuest('web');
    }

    /**
     * Test customer can log in with phone and password.
     */
    public function test_customer_can_login_with_phone(): void
    {
        $customer = Customer::firstOrCreate(
            ['phone' => '01711223344'],
            [
                'name' => 'Salman Chowdhury',
                'email' => 'salman@dreamerspcb.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $customer->password = Hash::make('password');
        $customer->save();

        $response = $this->post('/customer/login', [
            'login' => '01711223344',
            'password' => 'password',
        ]);

        $response->assertRedirect('/customer/dashboard');
        $this->assertAuthenticatedAs($customer, 'customer');
    }

    /**
     * Test customer cannot log in with invalid password.
     */
    public function test_customer_cannot_login_with_wrong_password(): void
    {
        $response = $this->from('/customer/login')->post('/customer/login', [
            'login' => '01711223344',
            'password' => 'incorrect-pass',
        ]);

        $response->assertRedirect('/customer/login');
        $response->assertSessionHas('error');
        $this->assertGuest('customer');
    }

    /**
     * Test admin can log out safely.
     */
    public function test_admin_logout(): void
    {
        $admin = User::firstOrCreate(['email' => 'admin@dreamerspcb.com'], ['name' => 'Admin', 'password' => Hash::make('password')]);

        $response = $this->actingAs($admin, 'web')->post('/admin/logout');
        $response->assertRedirect('/admin/login');
        $this->assertGuest('web');
    }

    /**
     * Test customer can log out safely.
     */
    public function test_customer_logout(): void
    {
        $customer = Customer::firstOrCreate(['phone' => '01711223344'], ['name' => 'Salman', 'password' => Hash::make('password'), 'is_active' => true]);

        $response = $this->actingAs($customer, 'customer')->post('/customer/logout');
        $response->assertRedirect('/');
        $this->assertGuest('customer');
    }

    /**
     * Test concurrent dual-guard isolation: admin and customer can both be logged in simultaneously.
     */
    public function test_admin_and_customer_dual_guard_isolation(): void
    {
        $admin = User::firstOrCreate(['email' => 'admin@dreamerspcb.com'], ['name' => 'Admin', 'password' => Hash::make('password')]);
        $customer = Customer::firstOrCreate(['phone' => '01711223344'], ['name' => 'Salman', 'password' => Hash::make('password'), 'is_active' => true]);

        $this->actingAs($admin, 'web');
        $this->actingAs($customer, 'customer');

        $this->assertAuthenticatedAs($admin, 'web');
        $this->assertAuthenticatedAs($customer, 'customer');

        // Customer dashboard renders for customer
        $customerDash = $this->get('/customer/dashboard');
        $customerDash->assertStatus(200);

        // Admin dashboard renders for admin
        $adminDash = $this->get('/admin/dashboard');
        $adminDash->assertStatus(200);
    }
}

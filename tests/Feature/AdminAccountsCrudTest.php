<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccountsCrudTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Account::query()->delete();

        $admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            ['name' => 'Dreamers Admin', 'password' => Hash::make('password')]
        );
        $this->actingAs($admin, 'web');
    }

    public function test_admin_can_view_accounts_index(): void
    {
        Account::create([
            'name' => 'Primary Cash Drawer',
            'account_type' => 'cash',
            'opening_balance' => 5000,
            'current_balance' => 5000,
            'is_active' => true,
        ]);

        $response = $this->get(route('admin.accounts.index'));
        $response->assertStatus(200);
        $response->assertSee('Primary Cash Drawer');
        $response->assertSee('Total Fund Balance');
    }

    public function test_admin_can_create_new_account(): void
    {
        $response = $this->post(route('admin.accounts.store'), [
            'name' => 'City Bank Corporate',
            'account_type' => 'bank',
            'account_number' => '1203498511',
            'bank_name' => 'City Bank PLC',
            'branch_name' => 'Dhanmondi',
            'opening_balance' => 25000,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('accounts', [
            'name' => 'City Bank Corporate',
            'account_type' => 'bank',
            'account_number' => '1203498511',
            'current_balance' => 25000,
        ]);
    }

    public function test_admin_can_update_account(): void
    {
        $account = Account::create([
            'name' => 'Old Account Title',
            'account_type' => 'cash',
            'opening_balance' => 1000,
            'current_balance' => 1000,
            'is_active' => true,
        ]);

        $response = $this->put(route('admin.accounts.update', $account), [
            'name' => 'Updated Bank Account',
            'account_type' => 'bank',
            'account_number' => '9988776655',
            'bank_name' => 'Eastern Bank PLC',
            'branch_name' => 'Gulshan',
            'is_default' => '1',
            'is_active' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'name' => 'Updated Bank Account',
            'account_type' => 'bank',
            'account_number' => '9988776655',
            'is_default' => true,
        ]);
    }

    public function test_admin_can_toggle_account_status(): void
    {
        $account = Account::create([
            'name' => 'bKash Merchant 01',
            'account_type' => 'mobile_banking',
            'opening_balance' => 0,
            'current_balance' => 0,
            'is_active' => true,
        ]);

        $response = $this->post(route('admin.accounts.toggle', $account));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_account(): void
    {
        $account = Account::create([
            'name' => 'Temporary Account',
            'account_type' => 'cash',
            'opening_balance' => 0,
            'current_balance' => 0,
            'is_active' => true,
        ]);

        $response = $this->delete(route('admin.accounts.destroy', $account));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('accounts', [
            'id' => $account->id,
        ]);
    }

    public function test_admin_can_deposit_and_transfer_funds(): void
    {
        $acc1 = Account::create([
            'name' => 'Source Bank',
            'account_type' => 'bank',
            'opening_balance' => 10000,
            'current_balance' => 10000,
            'is_active' => true,
        ]);

        $acc2 = Account::create([
            'name' => 'Destination Cash',
            'account_type' => 'cash',
            'opening_balance' => 2000,
            'current_balance' => 2000,
            'is_active' => true,
        ]);

        // Deposit into acc1
        $depositResponse = $this->post(route('admin.accounts.deposit'), [
            'account_id' => $acc1->id,
            'amount' => 5000,
            'transaction_date' => date('Y-m-d'),
            'note' => 'Test Deposit',
        ]);
        $depositResponse->assertRedirect();
        $this->assertEquals(15000, $acc1->fresh()->current_balance);

        // Transfer from acc1 to acc2
        $transferResponse = $this->post(route('admin.accounts.transfer'), [
            'from_account_id' => $acc1->id,
            'to_account_id' => $acc2->id,
            'amount' => 3000,
            'transaction_date' => date('Y-m-d'),
        ]);
        $transferResponse->assertRedirect();
        $this->assertEquals(12000, $acc1->fresh()->current_balance);
        $this->assertEquals(5000, $acc2->fresh()->current_balance);
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Services\AccountTransactionService;
use Illuminate\Console\Command;

class SyncOrderAccountBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-order-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize completed and paid sales orders into account balances and record transaction ledger entries';

    /**
     * Execute the console command.
     */
    public function handle(AccountTransactionService $accountTransactionService): int
    {
        $this->info('Starting sales revenue synchronization with accounts...');

        $synced = $accountTransactionService->syncExistingCompletedOrders();

        if (empty($synced)) {
            $this->info('✓ All completed and paid sales orders are already synchronized with accounts.');
        } else {
            $this->info('Successfully synchronized '.count($synced).' sales orders:');
            foreach ($synced as $item) {
                $this->line("  ✓ Order #{$item['order_no']} — ৳".number_format($item['amount'], 2)." credited to [{$item['account']}]");
            }
        }

        $this->newLine();
        $this->info('📊 Updated Accounts Live Fund Balances:');

        $headers = ['ID', 'Account Name', 'Type', 'Opening Balance', 'Current Available Balance', 'Status'];
        $rows = Account::all()->map(function ($acc) {
            return [
                $acc->id,
                $acc->name.($acc->is_default ? ' (Default)' : ''),
                strtoupper(str_replace('_', ' ', $acc->account_type)),
                '৳'.number_format($acc->opening_balance, 2),
                '৳'.number_format($acc->current_balance, 2),
                $acc->is_active ? 'Active' : 'Inactive',
            ];
        });

        $this->table($headers, $rows);

        $totalAvailable = Account::where('is_active', true)->sum('current_balance');
        $this->info('💰 Total Available Fund Balance Across All Accounts: ৳'.number_format($totalAvailable, 2));

        return Command::SUCCESS;
    }
}

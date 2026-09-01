<?php

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CleanDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-demo-data {--force : Force execution without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean all demo operational data (products, orders, customers, purchases, expenses, coupons, transactions) and reset account balances to 0 for a fresh production start.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting demo operational data cleanup...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = [
            'product_warranties',
            'order_items',
            'order_status_logs',
            'courier_consignments',
            'orders',
            'product_images',
            'product_variants',
            'products',
            'purchase_items',
            'supplier_payments',
            'purchases',
            'suppliers',
            'expenses',
            'transactions',
            'coupons',
            'landing_pages',
            'customers',
            'fraud_checks',
            'sms_logs',
        ];

        foreach ($tables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->truncate();
                $this->line("  ✓ Truncated table: {$table}");
            }
        }

        // Reset account balances to 0.00
        if (DB::getSchemaBuilder()->hasTable('accounts')) {
            Account::query()->update([
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
            ]);
            $this->line('  ✓ Reset all account balances to 0.00');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Clean demo uploaded product images in public/uploads/products
        $productUploadsPath = public_path('uploads/products');
        if (File::exists($productUploadsPath)) {
            $files = File::allFiles($productUploadsPath);
            foreach ($files as $file) {
                File::delete($file);
            }
            $this->line('  ✓ Cleared demo product images from public/uploads/products');
        }

        $this->info('🎉 All demo operational data has been wiped clean! Admin is ready for fresh start.');

        return Command::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Supplier;
use Illuminate\Console\Command;

class RecalculateSupplierDues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:recalculate-dues';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate all supplier dues and sync purchase payment statuses';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting recalculation of supplier and purchase dues...');

        $suppliers = Supplier::all();
        $count = 0;

        foreach ($suppliers as $supplier) {
            $supplier->recalculateDue();
            $count++;
            $this->line("  ✓ Synced Supplier #{$supplier->id} ({$supplier->name}): Total Paid = ৳{$supplier->total_paid}, Current Due = ৳{$supplier->current_due}");
        }

        $this->info("Successfully recalculated dues for {$count} suppliers and their purchase orders!");

        return Command::SUCCESS;
    }
}

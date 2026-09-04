<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('delivery_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->decimal('charge', 10, 2)->default(0.00);
            $table->string('estimated_days')->default('2-3 Days');
            $table->decimal('min_order_for_free_delivery', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed initial default zones
        DB::table('delivery_methods')->insert([
            [
                'name' => 'Inside Dhaka',
                'code' => 'inside_dhaka',
                'charge' => 70.00,
                'estimated_days' => '1-2 Days',
                'min_order_for_free_delivery' => null,
                'description' => 'Fast doorstep delivery across all areas within Dhaka Metropolitan.',
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Outside Dhaka',
                'code' => 'outside_dhaka',
                'charge' => 130.00,
                'estimated_days' => '2-4 Days',
                'min_order_for_free_delivery' => null,
                'description' => 'Reliable courier delivery across all 64 districts in Bangladesh.',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dhaka Suburbs (Gazipur, Savar, Narayanganj)',
                'code' => 'dhaka_suburbs',
                'charge' => 100.00,
                'estimated_days' => '1-3 Days',
                'min_order_for_free_delivery' => null,
                'description' => 'Dedicated sub-district courier coverage for Greater Dhaka peripheral zones.',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Express Same-Day Delivery',
                'code' => 'express_delivery',
                'charge' => 200.00,
                'estimated_days' => 'Within 12 Hours',
                'min_order_for_free_delivery' => null,
                'description' => 'Emergency same-day urgent express dispatch within Dhaka City.',
                'is_active' => false,
                'is_default' => false,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Store Pickup (Multiplan Center, Dhaka)',
                'code' => 'store_pickup',
                'charge' => 0.00,
                'estimated_days' => 'Instant Collection',
                'min_order_for_free_delivery' => null,
                'description' => 'Direct collection from DREAMERS PCB physical counter with 0 shipping charge.',
                'is_active' => false,
                'is_default' => false,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_methods');
    }
};

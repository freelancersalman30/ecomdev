<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('custom_pc_builds', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('build_code')->unique()->nullable();
            $table->string('category')->default('Gaming')->nullable();
            $table->string('performance_level')->default('Mid-Range')->nullable();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('components')->nullable();
            $table->unsignedInteger('estimated_wattage')->default(0);
            $table->decimal('regular_price', 12, 2)->default(0.00);
            $table->string('discount_type')->default('none'); // 'none', 'fixed', 'percentage'
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('final_price', 12, 2)->default(0.00);
            $table->string('stock_status')->default('in_stock'); // 'in_stock', 'pre_order', 'out_of_stock'
            $table->boolean('is_published')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_pc_builds');
    }
};

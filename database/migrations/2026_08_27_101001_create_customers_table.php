<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone')->index();
            $table->string('email')->nullable()->index();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->integer('loyalty_points')->default(0);
            $table->decimal('total_spent', 14, 2)->default(0);
            $table->integer('total_orders_count')->default(0);
            $table->integer('delivered_orders_count')->default(0);
            $table->integer('cancelled_orders_count')->default(0);
            $table->integer('returned_orders_count')->default(0);
            $table->decimal('delivery_success_rate', 5, 2)->default(100.00);
            $table->boolean('is_flagged_fraud')->default(false);
            $table->string('fraud_reason')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

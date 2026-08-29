<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique()->index(); // e.g. DPCB-20260827-0101
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('order_type', ['online', 'pos', 'landing_page'])->default('online');
            
            // Status Pipeline
            $table->enum('status', [
                'incomplete',
                'pending',
                'processing',
                'on_the_way',
                'in_courier',
                'completed',
                'cancelled',
                'returned'
            ])->default('pending')->index();

            // Financials
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->string('coupon_code')->nullable();
            $table->decimal('shipping_charge', 14, 2)->default(0);
            $table->decimal('tax', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->decimal('due_amount', 14, 2)->default(0);

            // Payment Details
            $table->string('payment_method')->default('cash_on_delivery'); // cod, bkash, nagad, sslcommerz, pos_cash, pos_card, split
            $table->enum('payment_status', ['pending', 'paid', 'partially_paid', 'refunded'])->default('pending');
            $table->string('payment_transaction_id')->nullable();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();

            // Shipping Info
            $table->string('shipping_name');
            $table->string('shipping_phone')->index();
            $table->string('shipping_email')->nullable();
            $table->text('shipping_address');
            $table->string('shipping_city')->default('Dhaka');
            $table->string('shipping_zone')->nullable();

            // Courier Tracking
            $table->string('courier_name')->nullable(); // Steadfast, Pathao, RedX, Paperfly
            $table->string('courier_tracking_id')->nullable()->index();
            $table->string('courier_consignment_id')->nullable();
            $table->string('courier_status')->nullable();

            // Risk & Fraud Check
            $table->boolean('is_fraud_suspect')->default(false);
            $table->integer('fraud_score')->default(0); // 0-100 (high = danger)
            $table->string('fraud_reason')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            // Administrative
            $table->text('admin_note')->nullable();
            $table->text('customer_note')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Order Items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('product_name');
            $table->string('variant_name')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('unit_cost', 12, 2)->default(0); // Purchase price at order time (for profit/loss)
            $table->decimal('unit_price', 12, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        // 3. Order Status Logs
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('note')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Courier Consignments
        Schema::create('courier_consignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('courier_name'); // Steadfast, Pathao, RedX
            $table->string('consignment_id')->unique()->index();
            $table->string('tracking_code')->nullable()->index();
            $table->decimal('cod_amount', 12, 2);
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->string('status')->default('created');
            $table->json('response_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_consignments');
        Schema::dropIfExists('order_status_logs');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};

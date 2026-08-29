<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique()->index();
            $table->string('barcode')->nullable()->index();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sub_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('child_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            
            // Tech Specifications (for PCB & Gadgets)
            $table->string('pcb_model')->nullable();
            $table->string('voltage')->nullable(); // e.g. "3.3V - 5V DC"
            $table->string('warranty')->nullable(); // e.g. "1 Year Replacement"
            $table->string('dimensions')->nullable(); // e.g. "53.4 x 68.6 mm"
            $table->string('weight')->nullable(); // e.g. "25g"
            $table->string('chipset')->nullable(); // e.g. "ATmega328P / ESP32-WROOM-32"
            $table->json('specifications')->nullable(); // key-value pairs

            // Financial & Stock
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('alert_threshold')->default(5);
            $table->boolean('has_variants')->default(false);

            // Descriptions & Media
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('datasheet_pdf')->nullable();

            // Status flags
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_flash_sale')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('views_count')->default(0);
            $table->integer('sales_count')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });

        // Product Variants
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('color_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('size_id')->nullable()->constrained()->nullOnDelete();
            $table->string('variant_name'); // e.g. "Black - 16GB / DIP-28"
            $table->string('sku')->unique()->index();
            $table->string('barcode')->nullable()->index();
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('alert_threshold')->default(3);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Product Gallery Images
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
    }
};

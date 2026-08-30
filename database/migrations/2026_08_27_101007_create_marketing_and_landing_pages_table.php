<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Coupons
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->index();
            $table->enum('discount_type', ['fixed', 'percentage'])->default('percentage');
            $table->decimal('discount_value', 12, 2);
            $table->decimal('min_order_amount', 12, 2)->default(0);
            $table->decimal('max_discount_amount', 12, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_per_user')->default(1);
            $table->integer('times_used')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Landing Pages (Drag/Drop Block Single Product Flash Sales)
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('headline')->nullable();
            $table->string('sub_headline')->nullable();
            $table->string('video_url')->nullable();
            $table->json('builder_blocks')->nullable(); // block-based content layout
            $table->json('features_list')->nullable();
            $table->json('testimonials')->nullable();
            $table->string('theme_color')->default('#0ea5e9'); // Primary accent
            $table->string('fb_pixel_id')->nullable();
            $table->string('custom_domain')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('conversions_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Campaigns
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('banner_image')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Banners
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('image');
            $table->string('link')->nullable();
            $table->enum('placement', [
                'hero_slider',
                'category_header',
                'promo_popup',
                'sidebar_ad',
                'footer_banner',
            ])->default('hero_slider');
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('landing_pages');
        Schema::dropIfExists('coupons');
    }
};

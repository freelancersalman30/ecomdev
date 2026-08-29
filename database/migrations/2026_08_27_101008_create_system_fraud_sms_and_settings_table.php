<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Fraud & Risk Checks
        Schema::create('fraud_checks', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->index();
            $table->string('ip_address')->nullable()->index();
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->decimal('courier_success_rate', 5, 2)->default(100.00);
            $table->integer('total_parcels')->default(0);
            $table->integer('delivered_parcels')->default(0);
            $table->integer('cancelled_parcels')->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_blacklisted')->default(false);
            $table->timestamps();
        });

        // 2. SMS Logs
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('gateway'); // BulkSMS, Greenweb, Twilio, Onnorokom
            $table->string('phone')->index();
            $table->text('message');
            $table->integer('character_count')->default(0);
            $table->integer('sms_parts')->default(1);
            $table->enum('status', ['sent', 'failed', 'pending'])->default('sent');
            $table->string('response_id')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        // 3. System General Settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('general')->index(); // general, shipping, email, invoice
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // 4. Third-Party API Hub Settings
        Schema::create('api_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider'); // steadfast, pathao, redx, bkash, nagad, sslcommerz, bulksms, greenweb, fb_capi
            $table->string('type'); // courier, payment, sms, tracking, fraud
            $table->string('title');
            $table->json('credentials')->nullable(); // API keys, secrets, webhooks
            $table->boolean('is_sandbox')->default(true);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // 5. SEO Settings & Sitemap
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            $table->string('meta_title');
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('og_image')->nullable();
            $table->string('canonical_url')->nullable();
            $table->longText('robots_txt')->nullable();
            $table->boolean('sitemap_auto_ping')->default(true);
            $table->timestamp('last_sitemap_generated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
        Schema::dropIfExists('api_settings');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('fraud_checks');
    }
};

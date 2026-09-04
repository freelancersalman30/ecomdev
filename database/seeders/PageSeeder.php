<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Terms & Warranty Policy',
                'slug' => 'terms-and-warranty',
                'placement' => 'both',
                'is_active' => true,
                'sort_order' => 1,
                'meta_title' => 'Terms of Service & Warranty Policy | DREAMERS PCB',
                'meta_description' => 'Read our comprehensive terms of service, hardware warranty claim guidelines, and replacement rules.',
                'content' => '<h2>1. Hardware Warranty Guidelines</h2>
<p>All microcontrollers, ICs, development boards, and modules sold at DREAMERS PCB undergo rigorous quality check procedures prior to packaging and dispatch.</p>
<ul>
    <li><strong>Standard Warranty Period:</strong> Hardware components carry manufacturer warranty or standard 30-day testing warranty unless specified otherwise.</li>
    <li><strong>What is Covered:</strong> Factory defects, dead-on-arrival (DOA) units, and non-functional internal components.</li>
    <li><strong>What is NOT Covered:</strong> Physical burning, reverse voltage polarity, short circuits caused by improper soldering, ESD discharge, or water damage.</li>
</ul>

<h2>2. How to Claim Warranty</h2>
<p>To claim warranty for an eligible product:</p>
<ol>
    <li>Navigate to our <strong>Warranty Check</strong> page or use the customer mobile app.</li>
    <li>Enter your product serial number or Order ID.</li>
    <li>Submit a clear photo/video showing the issue and your invoice receipt.</li>
    <li>Our engineering support team will review and approve replacement or RMA within 48 hours.</li>
</ol>

<h2>3. General Terms of Use</h2>
<p>By placing an order on this website, you agree to provide authentic contact and delivery details. We reserve the right to verify high-value orders prior to dispatch.</p>',
            ],
            [
                'title' => 'Delivery & Shipping Policy',
                'slug' => 'delivery-policy',
                'placement' => 'footer',
                'is_active' => true,
                'sort_order' => 2,
                'meta_title' => 'Nationwide Fast Delivery & Shipping Policy | DREAMERS PCB',
                'meta_description' => 'Fast delivery inside Dhaka within 24-48 hours and nationwide courier across 64 districts in Bangladesh.',
                'content' => '<h2>1. Delivery Zones & Timelines</h2>
<p>We deliver nationwide across all 64 districts in Bangladesh using top-tier courier services (Steadfast, Pathao, RedX).</p>
<ul>
    <li><strong>Inside Dhaka City:</strong> 24 to 48 hours (Standard rate: ৳60).</li>
    <li><strong>Outside Dhaka / Nationwide:</strong> 48 to 72 hours (Standard rate: ৳120).</li>
    <li><strong>Express Same-Day Delivery:</strong> Available for select areas in Dhaka when ordered before 12:00 PM.</li>
</ul>

<h2>2. Order Packaging & Safety</h2>
<p>All sensitive electronic chips and delicate components are sealed inside <strong>anti-static ESD bags</strong> and bubble-wrapped in reinforced corrugated boxes to ensure 100% damage-free transit.</p>

<h2>3. Real-Time Tracking</h2>
<p>Once your order is handed over to the courier, you will receive an automated SMS with your live tracking consignment number. You can also track your parcel directly on our <strong>Track My Order</strong> page.</p>',
            ],
            [
                'title' => 'Refunds & Returns Policy',
                'slug' => 'refunds-and-returns',
                'placement' => 'footer',
                'is_active' => true,
                'sort_order' => 3,
                'meta_title' => 'Easy 7-Day Refund and Return Policy | DREAMERS PCB',
                'meta_description' => 'Understand our 7-day hassle-free return and refund policy for electronic components and tools.',
                'content' => '<h2>1. 7-Day Hassle-Free Returns</h2>
<p>If you receive an incorrect item, a defective part, or missing items in your parcel, you can request an exchange or full refund within <strong>7 days</strong> of delivery.</p>

<h2>2. Eligibility Criteria</h2>
<ul>
    <li>The item must be in its original packaging with all included accessories and pin headers intact.</li>
    <li>Products that have been soldered, burnt, or modified are not eligible for standard returns.</li>
    <li>Original order invoice must be presented.</li>
</ul>

<h2>3. Refund Processing</h2>
<p>Approved refunds are processed back to your original payment method (bKash, Nagad, Bank Transfer, or Store Credit) within <strong>3 to 5 business days</strong> after item inspection at our service center.</p>',
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'placement' => 'footer',
                'is_active' => true,
                'sort_order' => 4,
                'meta_title' => 'Customer Privacy Policy | DREAMERS PCB',
                'meta_description' => 'Learn how we protect and respect your personal information and online transactions.',
                'content' => '<h2>1. Information We Collect</h2>
<p>We collect essential information required to fulfill your orders, including your name, shipping address, contact phone number, and email address.</p>

<h2>2. Data Protection & Security</h2>
<p>Your personal and transaction data is encrypted using 256-bit SSL protocols. We never sell, rent, or trade your personal information with third parties for marketing purposes.</p>

<h2>3. Payment Security</h2>
<p>All online payments (Cards, MFS) are processed directly through bank-grade secured payment gateways. We do not store your credit card numbers or PINs on our servers.</p>',
            ],
        ];

        foreach ($pages as $data) {
            Page::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}

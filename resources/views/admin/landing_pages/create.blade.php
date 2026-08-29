@extends('layouts.admin')

@section('title', 'Build Single-Product Landing Page')
@section('page-title', 'Create Flash Gadget Landing Page')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <form method="POST" action="{{ route('admin.landing-pages.store') }}" class="space-y-6">
        @csrf

        <!-- 1. Product Selection & Title -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="package" class="w-4 h-4 text-emerald-500"></i>
                <span>Product & Campaign Setup</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Campaign Page Title *</label>
                    <input type="text" name="title" required placeholder="e.g. ESP32-CAM AI Face Recognition Kit - Exclusive Flash Offer" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Select Featured Product *</label>
                    <select name="product_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none">
                        <option value="">Select a Product</option>
                        @foreach($products as $prod)
                        <option value="{{ $prod->id }}">{{ $prod->name }} (Price: ৳{{ $prod->effective_price }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. Copywriting & Visuals -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="type" class="w-4 h-4 text-sky-500"></i>
                <span>Headlines, Video & Key Selling Bullet Points</span>
            </h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">High-Impact Catchy Headline</label>
                    <input type="text" name="headline" placeholder="Build your own AI security system in under 10 minutes!" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Sub-Headline / Value Proposition</label>
                    <textarea name="sub_headline" rows="2" placeholder="Explain the key pain point solved by this gadget..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">YouTube Video Embed URL (Optional Demo Video)</label>
                    <input type="url" name="video_url" placeholder="https://www.youtube.com/embed/..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Key Selling Points (One per line)</label>
                    <textarea name="features" rows="5" placeholder="Dual-core 32-bit CPU with Wi-Fi & Bluetooth&#10;Original OV2640 2MP Camera Sensor included&#10;TF Card slot for offline video logging&#10;Free source code sample & documentation included" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none"></textarea>
                </div>
            </div>
        </div>

        <!-- 3. Tracking & Theme Accent -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="sliders" class="w-4 h-4 text-purple-500"></i>
                <span>Tracking & Theme Customization</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Facebook Pixel ID / CAPI</label>
                    <input type="text" name="fb_pixel_id" placeholder="987654321012345" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Primary Theme Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="theme_color" value="#0ea5e9" class="w-12 h-10 rounded-xl cursor-pointer border p-0.5">
                        <span class="text-xs text-slate-500">Button and accent color</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.landing-pages.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-lg transition">
                Build & Launch Landing Page
            </button>
        </div>

    </form>

</div>
@endsection

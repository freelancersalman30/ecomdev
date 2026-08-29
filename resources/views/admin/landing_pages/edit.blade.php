@extends('layouts.admin')

@section('title', 'Edit Landing Page: ' . $landingPage->title)
@section('page-title', 'Edit Landing Page')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <form method="POST" action="{{ route('admin.landing-pages.update', $landingPage->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Landing Page Setup</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Title *</label>
                    <input type="text" name="title" value="{{ $landingPage->title }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Featured Product *</label>
                    <select name="product_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                        @foreach($products as $prod)
                        <option value="{{ $prod->id }}" {{ $landingPage->product_id == $prod->id ? 'selected' : '' }}>{{ $prod->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Copywriting & Media</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Headline</label>
                    <input type="text" name="headline" value="{{ $landingPage->headline }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Sub-Headline</label>
                    <textarea name="sub_headline" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">{{ $landingPage->sub_headline }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">YouTube Video Embed URL</label>
                    <input type="url" name="video_url" value="{{ $landingPage->video_url }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Key Selling Points (One per line)</label>
                    <textarea name="features" rows="5" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">{{ is_array($landingPage->features_list) ? implode("\n", $landingPage->features_list) : '' }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Pixel & Styling</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Facebook Pixel ID</label>
                    <input type="text" name="fb_pixel_id" value="{{ $landingPage->fb_pixel_id }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Theme Accent Color</label>
                    <input type="color" name="theme_color" value="{{ $landingPage->theme_color ?? '#0ea5e9' }}" class="w-12 h-10 rounded-xl cursor-pointer border p-0.5">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.landing-pages.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-lg transition">
                Update Landing Page
            </button>
        </div>

    </form>

</div>
@endsection

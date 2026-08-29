@extends('layouts.admin')

@section('title', 'Footer & Contact Information CRUD')
@section('page-title', 'Footer Information & CMS Settings')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 text-xs font-bold flex items-center gap-2">
        <i data-lucide="check-circle" class="w-4 h-4"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.footer.update') }}" class="space-y-6">
        @csrf

        <!-- 1. Company Summary & Slogan -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="building-2" class="w-4 h-4 text-emerald-500"></i>
                <span>Store Bio & Company About Information</span>
            </h3>

            <div class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Footer About / Bio Description</label>
                    <textarea name="footer_about" rows="3" placeholder="Brief 2-3 sentence overview about DREAMERS PCB..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">{{ $settings['footer_about'] ?? "Bangladesh's enterprise online superstore for hardware developers, robotics researchers, and electronics enthusiasts." }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Trade License / BIN Number</label>
                        <input type="text" name="footer_trade_license" value="{{ $settings['footer_trade_license'] ?? 'TRAD/DSCC/012948-2025' }}" placeholder="TRAD/DSCC/..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Copyright Notice Text</label>
                        <input type="text" name="footer_copyright" value="{{ $settings['footer_copyright'] ?? '© ' . date('Y') . ' DREAMERS PCB. All rights reserved. Built for high-speed electronics e-commerce.' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Contact Numbers & Addresses -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="phone-call" class="w-4 h-4 text-sky-500"></i>
                <span>Hotlines, WhatsApp & Store Addresses</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Primary Hotline *</label>
                    <input type="text" name="footer_hotline" value="{{ $settings['footer_hotline'] ?? '+880 1700-112233' }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Secondary Hotline / Telephone</label>
                    <input type="text" name="footer_phone_secondary" value="{{ $settings['footer_phone_secondary'] ?? '+880 1800-445566' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Official WhatsApp Support</label>
                    <input type="text" name="footer_whatsapp" value="{{ $settings['footer_whatsapp'] ?? '+880 1700-112233' }}" placeholder="+88017..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Customer Support Email</label>
                    <input type="email" name="footer_email" value="{{ $settings['footer_email'] ?? 'support@dreamerspcb.com' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Head Office / Engineering Lab Address</label>
                    <textarea name="footer_address_office" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">{{ $settings['footer_address_office'] ?? 'Multiplan Center, Level 8, Suite 812, Elephant Road, Dhaka-1205' }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Retail Showroom & Pickup Point</label>
                    <textarea name="footer_address_showroom" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">{{ $settings['footer_address_showroom'] ?? 'Shop #402, Multiplan Center, Elephant Road, Dhaka' }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Operating Hours / Support Schedule</label>
                    <input type="text" name="footer_working_hours" value="{{ $settings['footer_working_hours'] ?? 'Saturday - Thursday: 10:00 AM - 08:00 PM (Friday Closed)' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
            </div>
        </div>

        <!-- 3. Social Media URLs -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="share-2" class="w-4 h-4 text-purple-500"></i>
                <span>Social Media & Community Channels</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Facebook Page URL</label>
                    <input type="url" name="footer_facebook_url" value="{{ $settings['footer_facebook_url'] ?? 'https://facebook.com/dreamerspcb' }}" placeholder="https://facebook.com/..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">YouTube Channel URL</label>
                    <input type="url" name="footer_youtube_url" value="{{ $settings['footer_youtube_url'] ?? 'https://youtube.com/@dreamerspcb' }}" placeholder="https://youtube.com/..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">LinkedIn Company URL</label>
                    <input type="url" name="footer_linkedin_url" value="{{ $settings['footer_linkedin_url'] ?? 'https://linkedin.com/company/dreamerspcb' }}" placeholder="https://linkedin.com/..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">GitHub Organization URL</label>
                    <input type="url" name="footer_github_url" value="{{ $settings['footer_github_url'] ?? 'https://github.com/dreamerspcb' }}" placeholder="https://github.com/..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Instagram Profile URL</label>
                    <input type="url" name="footer_instagram_url" value="{{ $settings['footer_instagram_url'] ?? 'https://instagram.com/dreamerspcb' }}" placeholder="https://instagram.com/..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Discord / Community URL</label>
                    <input type="url" name="footer_discord_url" value="{{ $settings['footer_discord_url'] ?? 'https://discord.gg/dreamerspcb' }}" placeholder="https://discord.gg/..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
            </div>
        </div>

        <!-- 4. Payment Badges & Courier Partners -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="credit-card" class="w-4 h-4 text-amber-500"></i>
                <span>Payment Gateways & Courier Partners Info</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Accepted Payment Methods (Comma Separated)</label>
                    <input type="text" name="footer_payment_methods" value="{{ $settings['footer_payment_methods'] ?? 'bKash, Nagad, Rocket, Cash on Delivery, Bank Transfer, Visa / Mastercard' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Courier Logistics Partners</label>
                    <input type="text" name="footer_courier_partners" value="{{ $settings['footer_courier_partners'] ?? 'Steadfast Courier, Pathao Express, RedX' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                </div>
            </div>
        </div>

        <!-- 5. Custom Footer Quick Links -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="link-2" class="w-4 h-4 text-emerald-500"></i>
                <span>Custom Quick Links</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="flex items-center gap-2">
                    <input type="text" name="footer_custom_link1_title" value="{{ $settings['footer_custom_link1_title'] ?? 'Terms & Warranty' }}" placeholder="Link Title" class="w-1/2 px-3 py-2 rounded-xl border dark:border-slate-700 bg-slate-50 dark:bg-slate-800 outline-none">
                    <input type="text" name="footer_custom_link1_url" value="{{ $settings['footer_custom_link1_url'] ?? '#' }}" placeholder="URL or Route" class="w-1/2 px-3 py-2 rounded-xl border dark:border-slate-700 bg-slate-50 dark:bg-slate-800 outline-none">
                </div>

                <div class="flex items-center gap-2">
                    <input type="text" name="footer_custom_link2_title" value="{{ $settings['footer_custom_link2_title'] ?? 'Delivery Policy' }}" placeholder="Link Title" class="w-1/2 px-3 py-2 rounded-xl border dark:border-slate-700 bg-slate-50 dark:bg-slate-800 outline-none">
                    <input type="text" name="footer_custom_link2_url" value="{{ $settings['footer_custom_link2_url'] ?? '#' }}" placeholder="URL or Route" class="w-1/2 px-3 py-2 rounded-xl border dark:border-slate-700 bg-slate-50 dark:bg-slate-800 outline-none">
                </div>

                <div class="flex items-center gap-2">
                    <input type="text" name="footer_custom_link3_title" value="{{ $settings['footer_custom_link3_title'] ?? 'Refunds & Returns' }}" placeholder="Link Title" class="w-1/2 px-3 py-2 rounded-xl border dark:border-slate-700 bg-slate-50 dark:bg-slate-800 outline-none">
                    <input type="text" name="footer_custom_link3_url" value="{{ $settings['footer_custom_link3_url'] ?? '#' }}" placeholder="URL or Route" class="w-1/2 px-3 py-2 rounded-xl border dark:border-slate-700 bg-slate-50 dark:bg-slate-800 outline-none">
                </div>

                <div class="flex items-center gap-2">
                    <input type="text" name="footer_custom_link4_title" value="{{ $settings['footer_custom_link4_title'] ?? 'Privacy Notice' }}" placeholder="Link Title" class="w-1/2 px-3 py-2 rounded-xl border dark:border-slate-700 bg-slate-50 dark:bg-slate-800 outline-none">
                    <input type="text" name="footer_custom_link4_url" value="{{ $settings['footer_custom_link4_url'] ?? '#' }}" placeholder="URL or Route" class="w-1/2 px-3 py-2 rounded-xl border dark:border-slate-700 bg-slate-50 dark:bg-slate-800 outline-none">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-lg shadow-emerald-500/20 transition flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Save Footer Settings</span>
            </button>
        </div>

    </form>

</div>
@endsection

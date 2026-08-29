@extends('layouts.customer')

@section('title', 'My Profile & Addresses - DREAMERS PCB')

@section('customer_content')
<div class="space-y-6">

    <!-- Profile Details & Default Address Form -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <div class="border-b pb-4">
            <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i data-lucide="user" class="w-5 h-5 text-daraz-orange"></i>
                <span>Profile & Default Shipping Address</span>
            </h2>
            <p class="text-xs text-slate-400">Update your recipient contact details and default courier address</p>
        </div>

        @if(session('success'))
        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('customer.profile.update') }}" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold outline-none focus:ring-2 focus:ring-daraz-orange/20">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Mobile Phone (Primary)</label>
                    <input type="text" value="{{ $customer->phone }}" disabled class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-xs font-mono font-bold text-slate-500 cursor-not-allowed">
                    <span class="text-[10px] text-slate-400">Phone number is locked as account identifier</span>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs outline-none focus:ring-2 focus:ring-daraz-orange/20">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">City / District</label>
                    <input type="text" name="city" value="{{ old('city', $customer->city) }}" placeholder="e.g. Dhaka" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs outline-none">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Default Shipping Address (House / Road / Area)</label>
                <textarea name="address" rows="3" placeholder="Full delivery address for automatic checkout fill..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs outline-none focus:ring-2 focus:ring-daraz-orange/20">{{ old('address', $customer->address) }}</textarea>
            </div>

            <button type="submit" class="px-6 py-2.5 rounded-xl bg-daraz-orange hover:bg-daraz-orangeHover text-white font-bold text-xs shadow-md transition">
                Save Profile Changes
            </button>
        </form>
    </div>

    <!-- Change Password Form -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <div class="border-b pb-4">
            <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i data-lucide="lock" class="w-5 h-5 text-emerald-600"></i>
                <span>Change Account Password</span>
            </h2>
            <p class="text-xs text-slate-400">Ensure your account is protected with a secure password</p>
        </div>

        @if($errors->any())
        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
            @foreach($errors->all() as $err)
            <p>&bull; {{ $err }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('customer.password.update') }}" class="space-y-4 text-xs max-w-md">
            @csrf

            <div>
                <label class="block font-bold text-slate-700 mb-1">Current Password *</label>
                <input type="password" name="current_password" required placeholder="••••••••" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">New Password (Min 6 chars) *</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Confirm New Password *</label>
                <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>

            <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition">
                Update Password
            </button>
        </form>
    </div>

</div>
@endsection

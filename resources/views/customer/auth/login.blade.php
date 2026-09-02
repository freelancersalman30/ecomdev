@extends('layouts.app')

@section('title', 'Customer Login - DREAMERS PCB')

@section('content')
<div class="max-w-md mx-auto px-4 py-12 space-y-6">

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-6">
        
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-slate-950 via-slate-900 to-daraz-orange text-white flex items-center justify-center mx-auto shadow-md">
                <i data-lucide="user" class="w-6 h-6 text-emerald-400"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900">Customer Login</h1>
            <p class="text-xs text-slate-500">Sign in with your phone number or email address</p>
        </div>

        @if(session('error'))
        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold">
            {{ session('error') }}
        </div>
        @endif

        @if(session('success'))
        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('customer.login.submit') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Phone Number or Email *</label>
                <input 
                    type="text" 
                    name="login" 
                    value="{{ old('login') }}"
                    required 
                    placeholder="e.g. 01711223344 or customer@gmail.com" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold outline-none focus:ring-2 focus:ring-daraz-orange/20">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="text-xs font-bold text-slate-700">Password *</label>
                    <span class="text-[11px] text-daraz-orange hover:underline cursor-pointer">Forgot password?</span>
                </div>
                <input 
                    type="password" 
                    name="password" 
                    required 
                    placeholder="••••••••" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs outline-none focus:ring-2 focus:ring-daraz-orange/20">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                    <input type="checkbox" name="remember" class="rounded text-daraz-orange focus:ring-daraz-orange">
                    <span>Remember this device</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 rounded-2xl bg-gradient-to-r from-daraz-orange to-amber-500 hover:from-daraz-orangeHover hover:to-amber-600 text-white font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-daraz-orange/20 transition transform active:scale-95">
                Sign In to Account
            </button>
        </form>

        <!-- Register Link -->
        <div class="pt-2 border-t border-slate-100 text-center text-xs text-slate-500">
            Don't have a customer account yet?
            <a href="{{ route('customer.register') }}" class="font-bold text-daraz-orange hover:underline ml-1">Create Account &rarr;</a>
        </div>

    </div>

</div>
@endsection

@extends('layouts.app')

@section('title', 'Register Customer Account - DREAMERS PCB')

@section('content')
<div class="max-w-md mx-auto px-4 py-12 space-y-6">

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-6">
        
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-950 via-slate-900 to-emerald-600 text-white flex items-center justify-center mx-auto shadow-md">
                <i data-lucide="user-plus" class="w-6 h-6 text-emerald-400"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900">Create Customer Account</h1>
            <p class="text-xs text-slate-500">Join the maker community & get 50 bonus loyalty points</p>
        </div>

        @if($errors->any())
        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
            @foreach($errors->all() as $err)
            <p>&bull; {{ $err }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('customer.register.submit') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Full Name *</label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required 
                    placeholder="e.g. Salman Chowdhury" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold outline-none focus:ring-2 focus:ring-daraz-orange/20">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Mobile Phone (11 Digits) *</label>
                <input 
                    type="tel" 
                    name="phone" 
                    value="{{ old('phone') }}" 
                    required 
                    placeholder="01711223344" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-mono font-bold outline-none focus:ring-2 focus:ring-daraz-orange/20">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Email Address (Optional)</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    placeholder="salman@gmail.com" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs outline-none focus:ring-2 focus:ring-daraz-orange/20">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Password *</label>
                    <input 
                        type="password" 
                        name="password" 
                        required 
                        placeholder="Min 6 chars" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs outline-none focus:ring-2 focus:ring-daraz-orange/20">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Confirm Password *</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        required 
                        placeholder="Repeat password" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs outline-none focus:ring-2 focus:ring-daraz-orange/20">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">City / District</label>
                <input 
                    type="text" 
                    name="city" 
                    value="{{ old('city', 'Dhaka') }}" 
                    placeholder="e.g. Dhaka, Chittagong, Sylhet" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs outline-none">
            </div>

            <button type="submit" class="w-full py-3 rounded-2xl bg-gradient-to-r from-daraz-orange to-amber-500 hover:from-daraz-orangeHover hover:to-amber-600 text-white font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-daraz-orange/20 transition transform active:scale-95">
                Create Account & Join
            </button>
        </form>

        <div class="pt-2 border-t border-slate-100 text-center text-xs text-slate-500">
            Already have an account?
            <a href="{{ route('customer.login') }}" class="font-bold text-daraz-orange hover:underline ml-1">Sign In &rarr;</a>
        </div>

    </div>

</div>
@endsection

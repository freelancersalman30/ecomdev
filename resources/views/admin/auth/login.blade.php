<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - DREAMERS PCB Enterprise Hub</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pcb: {
                            950: '#030712',
                            900: '#0f172a',
                            800: '#1e293b',
                            emerald: '#10b981',
                            circuit: '#334155'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .circuit-bg {
            background-color: #030712;
            background-image: 
                radial-gradient(at 10% 20%, rgba(16, 185, 129, 0.08) 0px, transparent 50%),
                radial-gradient(at 90% 80%, rgba(6, 182, 212, 0.07) 0px, transparent 50%),
                linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 40px 40px, 40px 40px;
        }
    </style>
</head>
<body class="h-full circuit-bg text-slate-100 antialiased flex flex-col justify-center py-12 sm:px-6 lg:px-8">

    <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
        
        <!-- Brand Logo Header -->
        <div class="text-center space-y-3">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-500 via-teal-500 to-emerald-300 flex items-center justify-center text-slate-950 shadow-xl shadow-emerald-500/20 group-hover:scale-105 transition transform duration-200">
                    <i data-lucide="cpu" class="w-8 h-8 stroke-[2.2]"></i>
                </div>
            </a>
            <div>
                <h1 class="text-2xl font-black tracking-tight text-white flex items-center justify-center gap-1.5">
                    DREAMERS <span class="text-emerald-400">PCB</span>
                </h1>
                <p class="text-xs uppercase tracking-widest text-slate-400 font-bold mt-1 font-mono">
                    [ ENTERPRISE MANAGEMENT PORTAL ]
                </p>
            </div>
        </div>

        <!-- Main Card -->
        <div class="mt-8 bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl shadow-black/80 space-y-6" x-data="{ showPassword: false, email: '{{ old('email', '') }}', password: '' }">
            
            <div class="border-b border-slate-800/80 pb-4">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-5 h-5 text-emerald-400"></i>
                    <span>Administrator Authentication</span>
                </h2>
                <p class="text-xs text-slate-400 mt-1">Authorized personnel and staff access only.</p>
            </div>

            <!-- Error Alerts -->
            @if(session('error'))
            <div class="p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-start gap-2.5 font-medium">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-400 mt-0.5 flex-shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            @if(session('success'))
            <div class="p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs flex items-start gap-2.5 font-medium">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400 mt-0.5 flex-shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                @foreach($errors->all() as $err)
                    <div class="flex items-center gap-2">
                        <i data-lucide="x" class="w-3.5 h-3.5 text-rose-400"></i>
                        <span>{{ $err }}</span>
                    </div>
                @endforeach
            </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>Staff Email Address *</span>
                        <span class="text-[10px] text-slate-500 font-mono">auth:web</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            autocomplete="email" 
                            required 
                            x-model="email"
                            placeholder="admin@dreamerspcb.com"
                            class="block w-full pl-10 pr-4 py-2.5 text-xs font-medium rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 placeholder:text-slate-600 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition font-mono">
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300 mb-1.5 flex items-center justify-between">
                        <span>Secret Password *</span>
                        <button type="button" @click="showPassword = !showPassword" class="text-[11px] text-emerald-400 hover:text-emerald-300 transition">
                            <span x-text="showPassword ? 'Hide Secret' : 'Show Secret'"></span>
                        </button>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input 
                            id="password" 
                            name="password" 
                            :type="showPassword ? 'text' : 'password'" 
                            autocomplete="current-password" 
                            required 
                            x-model="password"
                            placeholder="••••••••••••"
                            class="block w-full pl-10 pr-10 py-2.5 text-xs font-medium rounded-xl bg-slate-950/70 border border-slate-700 text-slate-100 placeholder:text-slate-600 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition font-mono">
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition">
                            <i :data-lucide="showPassword ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer select-none text-slate-400 hover:text-slate-300">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-emerald-500 focus:ring-emerald-500/30 focus:ring-offset-slate-900">
                        <span>Remember this secure device</span>
                    </label>
                    <span class="text-[10px] text-slate-600 font-mono">TLS 1.3</span>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-400 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 transition transform active:scale-[0.98] flex items-center justify-center gap-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Authenticate & Access Dashboard</span>
                </button>
            </form>

            <!-- Quick Demo Credentials Box with 1-Click Auto Fill -->
            <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-emerald-500/20 text-xs space-y-2">
                <div class="flex items-center justify-between text-[11px]">
                    <span class="font-bold text-emerald-400 flex items-center gap-1.5">
                        <i data-lucide="terminal" class="w-3.5 h-3.5"></i>
                        <span>Default Super Admin Credentials</span>
                    </span>
                    <button 
                        type="button" 
                        @click="email = 'admin@dreamerspcb.com'; password = 'password'; $nextTick(() => lucide.createIcons());"
                        class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500/30 text-[10px] font-bold font-mono transition">
                        Quick Fill
                    </button>
                </div>
                <div class="font-mono text-[11px] text-slate-400 space-y-0.5 bg-slate-900/60 p-2 rounded-xl border border-slate-800">
                    <p>Email: <span class="text-white font-semibold">admin@dreamerspcb.com</span></p>
                    <p>Pass: <span class="text-white font-semibold">password</span></p>
                </div>
            </div>

            <!-- Footer Links -->
            <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                <a href="{{ route('customer.login') }}" class="hover:text-emerald-400 transition flex items-center gap-1">
                    <i data-lucide="user" class="w-3.5 h-3.5"></i>
                    <span>Customer Login &rarr;</span>
                </a>
                <a href="{{ route('home') }}" class="hover:text-white transition flex items-center gap-1 text-slate-500">
                    <i data-lucide="store" class="w-3.5 h-3.5"></i>
                    <span>Return to Store</span>
                </a>
            </div>

        </div>

        <p class="text-center text-slate-600 text-xs mt-6 font-mono">
            &copy; {{ date('Y') }} DREAMERS PCB Enterprise Suite. Internal Use Only.
        </p>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>

@extends('layouts.admin')

@section('title', 'Email Configuration')
@section('page-title', 'SMTP Email & Notification Gateway')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Breadcrumb / Header info --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="mail-check" class="w-5 h-5 text-emerald-500"></i>
                <span>Email & Notification Gateway</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Configure transactional SMTP credentials for customer orders, reset passwords, and system alerts.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold {{ !empty($emailSettings['mail_host']) ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800' }}">
                <span class="w-2 h-2 rounded-full {{ !empty($emailSettings['mail_host']) ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' }}"></span>
                <span>Driver: {{ strtoupper($emailSettings['mail_driver'] ?? config('mail.default', 'smtp')) }}</span>
            </span>
        </div>
    </div>

    {{-- Quick Provider Presets Bar --}}
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white rounded-2xl p-5 border border-slate-800 shadow-sm space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="zap" class="w-4 h-4 text-amber-400"></i>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-200">1-Click Provider Quick Presets</h3>
            </div>
            <span class="text-[11px] text-slate-400">Click to autofill recommended server host, port & encryption</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
            <button type="button" onclick="applyPreset('gmail')" class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-emerald-600 border border-slate-700 hover:border-emerald-500 text-xs font-semibold transition text-left flex items-center gap-2 group">
                <i data-lucide="mail" class="w-3.5 h-3.5 text-red-400 group-hover:text-white"></i>
                <span>Gmail / GSuite</span>
            </button>
            <button type="button" onclick="applyPreset('cpanel')" class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-emerald-600 border border-slate-700 hover:border-emerald-500 text-xs font-semibold transition text-left flex items-center gap-2 group">
                <i data-lucide="server" class="w-3.5 h-3.5 text-orange-400 group-hover:text-white"></i>
                <span>cPanel Webmail</span>
            </button>
            <button type="button" onclick="applyPreset('mailtrap')" class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-emerald-600 border border-slate-700 hover:border-emerald-500 text-xs font-semibold transition text-left flex items-center gap-2 group">
                <i data-lucide="shield" class="w-3.5 h-3.5 text-emerald-400 group-hover:text-white"></i>
                <span>Mailtrap (Test)</span>
            </button>
            <button type="button" onclick="applyPreset('brevo')" class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-emerald-600 border border-slate-700 hover:border-emerald-500 text-xs font-semibold transition text-left flex items-center gap-2 group">
                <i data-lucide="send" class="w-3.5 h-3.5 text-blue-400 group-hover:text-white"></i>
                <span>Brevo (Sendinblue)</span>
            </button>
            <button type="button" onclick="applyPreset('mailgun')" class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-emerald-600 border border-slate-700 hover:border-emerald-500 text-xs font-semibold transition text-left flex items-center gap-2 group">
                <i data-lucide="flame" class="w-3.5 h-3.5 text-amber-400 group-hover:text-white"></i>
                <span>Mailgun</span>
            </button>
            <button type="button" onclick="applyPreset('ses')" class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-emerald-600 border border-slate-700 hover:border-emerald-500 text-xs font-semibold transition text-left flex items-center gap-2 group">
                <i data-lucide="cloud" class="w-3.5 h-3.5 text-yellow-400 group-hover:text-white"></i>
                <span>Amazon SES</span>
            </button>
        </div>
    </div>

    {{-- Main Settings Form --}}
    <form method="POST" action="{{ route('admin.settings.email.update') }}" class="space-y-6" id="emailSettingsForm">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="sliders" class="w-4 h-4 text-emerald-500"></i>
                    <span>SMTP Server Credentials</span>
                </h3>
                <span class="text-xs text-slate-400">All fields are loaded dynamically at runtime</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                {{-- Mail Driver --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Mail Driver / Protocol</label>
                    <select name="mail_driver" id="mail_driver" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-semibold outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <option value="smtp" {{ ($emailSettings['mail_driver'] ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP (Recommended)</option>
                        <option value="sendmail" {{ ($emailSettings['mail_driver'] ?? '') === 'sendmail' ? 'selected' : '' }}>Sendmail (Local Server)</option>
                        <option value="log" {{ ($emailSettings['mail_driver'] ?? '') === 'log' ? 'selected' : '' }}>Log (Testing Only)</option>
                    </select>
                </div>

                {{-- SMTP Host --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">SMTP Host Server</label>
                    <div class="relative">
                        <input type="text" name="mail_host" id="mail_host" value="{{ $emailSettings['mail_host'] ?? '' }}" placeholder="e.g. smtp.gmail.com or mail.yourdomain.com" class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-mono outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <i data-lucide="server" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                    </div>
                </div>

                {{-- SMTP Port --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">SMTP Port</label>
                    <input type="number" name="mail_port" id="mail_port" value="{{ $emailSettings['mail_port'] ?? '587' }}" placeholder="587 / 465 / 2525" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-mono outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    <p class="text-[10px] text-slate-400 mt-1">587 for TLS, 465 for SSL, 2525 for Sandbox</p>
                </div>

                {{-- Encryption --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Encryption Type</label>
                    <select name="mail_encryption" id="mail_encryption" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-semibold outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <option value="tls" {{ strtolower($emailSettings['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS (Port 587)</option>
                        <option value="ssl" {{ strtolower($emailSettings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL / SMTPS (Port 465)</option>
                        <option value="null" {{ strtolower($emailSettings['mail_encryption'] ?? '') === 'null' ? 'selected' : '' }}>None / Plain (Port 25/2525)</option>
                    </select>
                </div>

                {{-- Empty spacing for layout alignment --}}
                <div class="hidden md:block"></div>

                {{-- SMTP Username --}}
                <div class="sm:col-span-1 md:col-span-1">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">SMTP Username / Email</label>
                    <div class="relative">
                        <input type="text" name="mail_username" id="mail_username" value="{{ $emailSettings['mail_username'] ?? '' }}" placeholder="your-email@example.com" class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-mono outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                    </div>
                </div>

                {{-- SMTP Password with Toggle --}}
                <div class="sm:col-span-1 md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">SMTP Password / App Password</label>
                    <div class="relative">
                        <input type="password" name="mail_password" id="mail_password" value="{{ $emailSettings['mail_password'] ?? '' }}" placeholder="Enter password or 16-digit Google App Password" class="w-full pl-9 pr-10 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-mono outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <i data-lucide="key" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                            <i data-lucide="eye" id="passwordEyeIcon" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                {{-- From Email Address --}}
                <div class="sm:col-span-1 md:col-span-1">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">From Email Address</label>
                    <div class="relative">
                        <input type="email" name="mail_from_address" id="mail_from_address" value="{{ $emailSettings['mail_from_address'] ?? 'noreply@dreamerspcb.com' }}" placeholder="noreply@yourdomain.com" class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <i data-lucide="at-sign" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                    </div>
                </div>

                {{-- From Sender Name --}}
                <div class="sm:col-span-1 md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">From Sender Name</label>
                    <div class="relative">
                        <input type="text" name="mail_from_name" id="mail_from_name" value="{{ $emailSettings['mail_from_name'] ?? 'DREAMERS PCB Support' }}" placeholder="DREAMERS PCB Support" class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        <i data-lucide="badge-check" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Save SMTP Settings</span>
                </button>
            </div>
        </div>
    </form>

    {{-- Send Test Email Section --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="send" class="w-4 h-4 text-sky-500"></i>
                <span>Test Email Delivery Gateway</span>
            </h3>
            <span class="text-xs text-slate-400">Verifies live handshake with your SMTP server</span>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
            Enter an email address where you'd like to receive a live test notification. Make sure to click <strong class="text-slate-700 dark:text-slate-200">Save SMTP Settings</strong> first if you recently changed credentials.
        </p>

        <form method="POST" action="{{ route('admin.settings.email.test') }}" class="flex flex-col sm:flex-row gap-3 pt-1" id="testEmailForm">
            @csrf
            <div class="relative flex-1">
                <input type="email" name="test_email" id="test_email" placeholder="Enter recipient email (e.g. yourname@gmail.com)" required class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
                <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
            </div>
            <button type="submit" id="btnSendTest" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs shadow-md shadow-sky-600/20 transition whitespace-nowrap flex items-center justify-center gap-2">
                <i data-lucide="send" class="w-4 h-4"></i>
                <span id="btnSendTestText">Send Test Email</span>
            </button>
        </form>
    </div>

    {{-- Help & Guidance Accordion --}}
    <div class="bg-slate-50 dark:bg-slate-900/60 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 space-y-4">
        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2">
            <i data-lucide="help-circle" class="w-4 h-4 text-amber-500"></i>
            <span>How to setup popular email services</span>
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-slate-600 dark:text-slate-400">
            <div class="bg-white dark:bg-slate-800/70 p-4 rounded-xl border border-slate-200 dark:border-slate-700/60 space-y-2">
                <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    <span>Using Gmail / Google Workspace</span>
                </div>
                <ol class="list-decimal list-inside space-y-1 text-[11px] leading-relaxed">
                    <li>Enable <strong>2-Step Verification</strong> in your Google Account.</li>
                    <li>Go to <a href="https://myaccount.google.com/apppasswords" target="_blank" class="text-emerald-500 underline font-medium">Google App Passwords</a>.</li>
                    <li>Create an app password named <em>"Ecom Mailer"</em> and copy the 16-letter code.</li>
                    <li>Paste the 16-letter code into <strong>SMTP Password</strong> above.</li>
                </ol>
            </div>
            <div class="bg-white dark:bg-slate-800/70 p-4 rounded-xl border border-slate-200 dark:border-slate-700/60 space-y-2">
                <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                    <span>Using cPanel / Domain Webmail</span>
                </div>
                <ol class="list-decimal list-inside space-y-1 text-[11px] leading-relaxed">
                    <li>In cPanel, open <strong>Email Accounts</strong> &rarr; <strong>Connect Devices</strong>.</li>
                    <li>Look for <strong>Outgoing Server</strong> (usually <code>mail.yourdomain.com</code>).</li>
                    <li>Set <strong>Port</strong> to <code>465</code> with <strong>SSL</strong> (or <code>587</code> with TLS).</li>
                    <li>Use your full email as the username and your email account password.</li>
                </ol>
            </div>
        </div>
    </div>

</div>

<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('mail_password');
        const icon = document.getElementById('passwordEyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            if (icon) icon.setAttribute('data-lucide', 'eye');
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function applyPreset(provider) {
        const presets = {
            gmail: {
                driver: 'smtp',
                host: 'smtp.gmail.com',
                port: '587',
                encryption: 'tls',
            },
            cpanel: {
                driver: 'smtp',
                host: 'mail.' + (window.location.hostname.replace('www.', '') || 'yourdomain.com'),
                port: '465',
                encryption: 'ssl',
            },
            mailtrap: {
                driver: 'smtp',
                host: 'sandbox.smtp.mailtrap.io',
                port: '2525',
                encryption: 'tls',
            },
            brevo: {
                driver: 'smtp',
                host: 'smtp-relay.brevo.com',
                port: '587',
                encryption: 'tls',
            },
            mailgun: {
                driver: 'smtp',
                host: 'smtp.mailgun.org',
                port: '587',
                encryption: 'tls',
            },
            ses: {
                driver: 'smtp',
                host: 'email-smtp.us-east-1.amazonaws.com',
                port: '587',
                encryption: 'tls',
            }
        };

        if (presets[provider]) {
            const p = presets[provider];
            document.getElementById('mail_driver').value = p.driver;
            document.getElementById('mail_host').value = p.host;
            document.getElementById('mail_port').value = p.port;
            document.getElementById('mail_encryption').value = p.encryption;

            // Flash visual feedback
            const hostInput = document.getElementById('mail_host');
            hostInput.classList.add('ring-2', 'ring-emerald-500');
            setTimeout(() => {
                hostInput.classList.remove('ring-2', 'ring-emerald-500');
            }, 1000);
        }
    }

    document.getElementById('testEmailForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('btnSendTest');
        const text = document.getElementById('btnSendTestText');
        if (btn && text) {
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            text.textContent = 'Connecting & Sending...';
        }
    });
</script>
@endsection

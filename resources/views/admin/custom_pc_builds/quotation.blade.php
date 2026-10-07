<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation - {{ $customPcBuild->build_code }} - {{ $customPcBuild->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .code-font {
            font-family: 'JetBrains Mono', monospace;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
                padding: 0 !important;
            }
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen py-8 px-4">

    <!-- Top Floating Print Action Bar (Hidden on Print) -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.custom-pc-builds.show', $customPcBuild->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">
                &larr; Back to PC Profile
            </a>
            <span class="text-xs text-slate-500 font-mono">Quotation #{{ $customPcBuild->build_code }}</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                <span>Print / Save as PDF</span>
            </button>
        </div>
    </div>

    <!-- Printable Sheet Container -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-3xl shadow-xl border border-slate-200 space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b-2 border-slate-900 gap-4">
            <div>
                <div class="text-2xl font-black tracking-tighter text-slate-900 flex items-center gap-2">
                    <span class="px-2 py-1 bg-slate-900 text-white rounded-lg text-lg">DREAMERS</span>
                    <span class="text-emerald-600">PCB</span>
                </div>
                <div class="text-xs font-bold text-slate-500 mt-1 uppercase tracking-wider">Custom PC & Hardware Engineering</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Multiplan Center / Eastern Plus, Dhaka, Bangladesh</div>
                <div class="text-[11px] text-slate-400">Hotline: +880 1700-000000 • Email: sales@dreamerspcb.com</div>
            </div>

            <div class="sm:text-right">
                <div class="inline-block px-3 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-extrabold uppercase tracking-widest border border-emerald-200">
                    Official PC Quotation
                </div>
                <div class="text-xs text-slate-500 font-mono mt-2">
                    <div>Ref No: <strong class="text-slate-900">{{ $customPcBuild->build_code }}</strong></div>
                    <div>Date: <strong class="text-slate-900">{{ now()->format('d M Y') }}</strong></div>
                    <div>Valid Until: <strong class="text-slate-900">{{ now()->addDays(7)->format('d M Y') }}</strong></div>
                </div>
            </div>
        </div>

        <!-- PC Package Highlights -->
        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 space-y-2">
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                <div>
                    <h2 class="text-base font-black text-slate-900">{{ $customPcBuild->title }}</h2>
                    <div class="text-xs text-slate-500 mt-0.5">
                        Category: <strong class="text-slate-800">{{ $customPcBuild->category ?? 'Gaming Rig' }}</strong> • 
                        Tier: <strong class="text-slate-800">{{ $customPcBuild->performance_level ?? 'Mid-Range' }}</strong> • 
                        Power Load: <strong class="text-amber-600">{{ $customPcBuild->estimated_wattage ?: 450 }}W Est.</strong>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Combo Package Price</span>
                    <div class="text-xl font-black text-emerald-600 code-font">
                        ৳{{ number_format($customPcBuild->final_price, 2) }}
                    </div>
                </div>
            </div>
            @if($customPcBuild->description)
            <p class="text-xs text-slate-600 pt-2 border-t border-slate-200 italic">{{ $customPcBuild->description }}</p>
            @endif
        </div>

        <!-- Components Breakdown Table -->
        <div>
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-900 text-white font-bold uppercase text-[10px] tracking-wider">
                        <th class="p-3 rounded-l-xl">Slot</th>
                        <th class="p-3">Component / Specification</th>
                        <th class="p-3">Part SKU</th>
                        <th class="p-3">Warranty</th>
                        <th class="p-3 text-center">Qty</th>
                        <th class="p-3 text-right">Unit Price</th>
                        <th class="p-3 text-right rounded-r-xl">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $components = is_array($customPcBuild->components) ? $customPcBuild->components : [];
                    @endphp
                    @forelse($components as $c)
                    @php
                        $has = !empty($c['product_id']) || !empty($c['custom_item_name']);
                        $sub = (float)($c['unit_price'] ?? 0) * (int)($c['quantity'] ?? 1);
                    @endphp
                    @if($has)
                    <tr>
                        <td class="p-3 font-bold text-slate-800">{{ $c['slot_name'] ?? 'Part' }}</td>
                        <td class="p-3 font-semibold text-slate-900">{{ $c['custom_item_name'] ?? $c['product_name'] }}</td>
                        <td class="p-3 font-mono text-slate-500">{{ $c['sku'] ?? '-' }}</td>
                        <td class="p-3 text-emerald-700 font-semibold">{{ $c['warranty'] ?? 'Standard' }}</td>
                        <td class="p-3 text-center font-mono font-bold">{{ $c['quantity'] ?? 1 }}</td>
                        <td class="p-3 text-right font-mono code-font">৳{{ number_format((float)($c['unit_price'] ?? 0), 2) }}</td>
                        <td class="p-3 text-right font-mono font-bold code-font text-slate-900">৳{{ number_format($sub, 2) }}</td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="7" class="p-4 text-center text-slate-400">No components listed.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Financial Calculation Summary -->
        <div class="flex justify-end">
            <div class="w-full sm:w-72 bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2 text-xs">
                <div class="flex justify-between text-slate-500">
                    <span>Components Regular Total:</span>
                    <span class="font-bold text-slate-900 code-font">৳{{ number_format($customPcBuild->regular_price, 2) }}</span>
                </div>
                @if($customPcBuild->regular_price > $customPcBuild->final_price)
                <div class="flex justify-between text-rose-600 font-semibold">
                    <span>Bundle Combo Savings:</span>
                    <span class="code-font">- ৳{{ number_format($customPcBuild->regular_price - $customPcBuild->final_price, 2) }}</span>
                </div>
                @endif
                <div class="pt-2 border-t-2 border-slate-900 flex justify-between items-baseline">
                    <span class="font-black text-slate-900 text-xs uppercase">Net Payable Amount:</span>
                    <span class="text-base font-black text-emerald-600 code-font">৳{{ number_format($customPcBuild->final_price, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Terms & Conditions -->
        <div class="pt-4 border-t border-slate-200 text-[11px] text-slate-500 space-y-1">
            <div class="font-bold text-slate-700 uppercase tracking-wider text-[10px]">Terms & Conditions:</div>
            <p>1. This quotation is valid for 7 calendar days from the date of issue and is subject to component availability.</p>
            <p>2. Official brand warranty applies to individual hardware parts per manufacturer warranty policies.</p>
            <p>3. Lifetime free PC maintenance and technical diagnostic support provided by DREAMERS PCB.</p>
        </div>

        <!-- Signatures -->
        <div class="pt-8 flex justify-between items-end text-xs text-slate-500">
            <div class="text-center">
                <div class="w-44 border-b border-slate-400 mb-1"></div>
                <span>Customer Signature</span>
            </div>
            <div class="text-center">
                <div class="w-44 border-b border-slate-900 mb-1"></div>
                <span class="font-bold text-slate-900">Authorized Signature</span>
                <div class="text-[10px] text-emerald-600 font-bold">DREAMERS PCB</div>
            </div>
        </div>

    </div>

</body>
</html>

@extends('layouts.admin')

@section('title', 'Scan & Auto-Entry Products Workstation')
@section('page-title', 'Barcode Scanner & Rapid Auto-Entry Hub')

@push('styles')
<style>
    #camera-reader {
        width: 100% !important;
        border: none !important;
    }
    #camera-reader video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        border-radius: 1rem;
    }
    #camera-reader__scan_region {
        background: transparent !important;
    }
    .scan-laser {
        animation: scanBeam 2s ease-in-out infinite alternate;
    }
    @keyframes scanBeam {
        0% { top: 10%; opacity: 0.8; }
        100% { top: 90%; opacity: 0.8; }
    }
</style>
@endpush

@section('content')
<div x-data="scannerApp()" x-init="initScanner()" class="space-y-6">

    <!-- Top KPI & Operational Status Strip -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        
        <!-- 1. Total Scanned Today -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-bold uppercase tracking-wider">Session Scans</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                    <i data-lucide="scan-line" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <div class="text-2xl font-black text-slate-900 dark:text-white code-font" x-text="sessionScannedCount">0</div>
                <span class="text-xs text-emerald-600 dark:text-emerald-400 font-bold" x-text="sessionTotalUnits + ' total units'">0 units</span>
            </div>
            <div class="absolute bottom-0 inset-x-0 h-1 bg-emerald-500"></div>
        </div>

        <!-- 2. Catalog Total -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-bold uppercase tracking-wider">Total Catalog</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center">
                    <i data-lucide="package" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-900 dark:text-white code-font">{{ $stats['total_products'] }}</div>
                <div class="text-[11px] text-slate-400">{{ $stats['in_stock'] }} In Stock &bull; {{ $stats['low_stock'] }} Low Stock</div>
            </div>
            <div class="absolute bottom-0 inset-x-0 h-1 bg-slate-400/40"></div>
        </div>

        <!-- 3. Hardware Gun Status -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-bold uppercase tracking-wider text-teal-600 dark:text-teal-400">Hardware Gun</span>
                <div class="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-500 flex items-center justify-center">
                    <i data-lucide="zap" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <span class="text-xs font-bold text-slate-900 dark:text-white">Active & Listening</span>
            </div>
            <div class="text-[10px] text-slate-400 mt-1">USB / Bluetooth Laser Ready</div>
            <div class="absolute bottom-0 inset-x-0 h-1 bg-teal-500"></div>
        </div>

        <!-- 4. Sound & Feedback -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">Audio Feedback</span>
                <button @click="soundEnabled = !soundEnabled" class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center hover:bg-purple-500/20 transition">
                    <i :data-lucide="soundEnabled ? 'volume-2' : 'volume-x'" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="mt-2 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-900 dark:text-white" x-text="soundEnabled ? 'Sound Enabled' : 'Muted'">Sound Enabled</span>
                <button @click="playBeep(880)" class="text-[10px] px-2 py-0.5 rounded-lg bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 font-bold hover:opacity-80">Test Audio</button>
            </div>
            <div class="absolute bottom-0 inset-x-0 h-1 bg-purple-500"></div>
        </div>

    </div>

    <!-- MAIN TWO-COLUMN WORKSTATION -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- LEFT COLUMN: Live Camera Viewport & Auto Controls (5 Cols) -->
        <div class="lg:col-span-5 space-y-4">
            
            <!-- Scanner Mode Switcher Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-500 flex items-center justify-center">
                            <i data-lucide="sliders" class="w-4 h-4"></i>
                        </div>
                        <h3 class="font-bold text-xs uppercase tracking-wider text-slate-900 dark:text-white">Auto-Scan Workflow</h3>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-500 uppercase">Live Engine</span>
                </div>

                <!-- Operating Mode Selector -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400">Scan Action Trigger</label>
                    <div class="grid grid-cols-3 gap-2 text-xs font-semibold">
                        <button 
                            type="button" 
                            @click="scanMode = 'stock_in'" 
                            :class="scanMode === 'stock_in' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200'"
                            class="p-2.5 rounded-xl transition text-center flex flex-col items-center gap-1">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            <span class="text-[11px] leading-tight">Auto Stock-In</span>
                        </button>
                        
                        <button 
                            type="button" 
                            @click="scanMode = 'fast_entry'" 
                            :class="scanMode === 'fast_entry' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200'"
                            class="p-2.5 rounded-xl transition text-center flex flex-col items-center gap-1">
                            <i data-lucide="sparkles" class="w-4 h-4"></i>
                            <span class="text-[11px] leading-tight">Rapid Entry</span>
                        </button>

                        <button 
                            type="button" 
                            @click="scanMode = 'batch_queue'" 
                            :class="scanMode === 'batch_queue' ? 'bg-emerald-500 text-slate-950 font-bold shadow-md shadow-emerald-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200'"
                            class="p-2.5 rounded-xl transition text-center flex flex-col items-center gap-1">
                            <i data-lucide="layers" class="w-4 h-4"></i>
                            <span class="text-[11px] leading-tight">Batch Queue</span>
                        </button>
                    </div>
                </div>

                <!-- Batch Quantity Multiplier -->
                <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-xs text-slate-500">Scan Multiplier (Units/Scan):</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" @click="batchIncrement = Math.max(1, batchIncrement - 1)" class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-200 flex items-center justify-center">-</button>
                        <input type="number" x-model.number="batchIncrement" min="1" max="500" class="w-14 text-center py-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold code-font">
                        <button type="button" @click="batchIncrement = batchIncrement + 1" class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-200 flex items-center justify-center">+</button>
                    </div>
                </div>
            </div>

            <!-- Interactive Live Camera Scanner Viewport -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-500 flex items-center justify-center">
                            <i data-lucide="camera" class="w-4 h-4"></i>
                        </div>
                        <h3 class="font-bold text-xs uppercase tracking-wider text-slate-900 dark:text-white">Mobile & Web Camera</h3>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Torch / Flashlight Button (when supported) -->
                        <button 
                            x-show="cameraActive && torchSupported" 
                            @click="toggleTorch()" 
                            :class="isTorchOn ? 'bg-amber-400 text-slate-950 font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-white'"
                            class="p-1.5 rounded-xl transition" 
                            title="Toggle Flashlight / Torch">
                            <i data-lucide="flashlight" class="w-4 h-4"></i>
                        </button>

                        <!-- Flip / Switch Camera Button -->
                        <button 
                            x-show="cameraActive && availableCameras.length > 1" 
                            @click="switchCamera()" 
                            class="p-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-white transition" 
                            title="Switch Front/Rear Lens">
                            <i data-lucide="switch-camera" class="w-4 h-4"></i>
                        </button>

                        <button 
                            @click="toggleCamera()" 
                            :class="cameraActive ? 'bg-rose-500 hover:bg-rose-600 text-white' : 'bg-emerald-600 hover:bg-emerald-500 text-white'"
                            class="px-3 py-1.5 rounded-xl font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
                            <i :data-lucide="cameraActive ? 'video-off' : 'video'" class="w-3.5 h-3.5"></i>
                            <span x-text="cameraActive ? 'Stop Camera' : 'Start Camera'">Start Camera</span>
                        </button>
                    </div>
                </div>

                <!-- Camera Lens Selector (when multiple lenses detected) -->
                <div x-show="availableCameras.length > 1" class="flex items-center gap-2">
                    <label class="text-[11px] font-bold text-slate-400 whitespace-nowrap">Camera Lens:</label>
                    <select 
                        x-model="selectedCameraId" 
                        @change="onCameraSelected()" 
                        class="w-full px-2.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-[11px] text-slate-800 dark:text-slate-200 outline-none">
                        <template x-for="cam in availableCameras" :key="cam.id">
                            <option :value="cam.id" x-text="cam.label || ('Camera ' + cam.id)"></option>
                        </template>
                    </select>
                </div>

                <!-- Camera Container -->
                <div class="relative rounded-2xl overflow-hidden bg-slate-950 border border-slate-800 min-h-[220px] flex items-center justify-center">
                    <div id="camera-reader" class="w-full h-full min-h-[220px]"></div>
                    
                    <!-- Scan Guide Overlay -->
                    <div x-show="cameraActive" class="absolute inset-0 pointer-events-none flex flex-col items-center justify-center p-4">
                        <div class="w-56 h-36 border-2 border-dashed border-emerald-400 rounded-2xl relative flex items-center justify-center shadow-2xl shadow-emerald-500/30 overflow-hidden bg-emerald-500/5">
                            <div class="absolute left-0 right-0 h-0.5 bg-emerald-400 scan-laser shadow-[0_0_12px_#34d399]"></div>
                            <span class="absolute bottom-2 text-[9px] text-emerald-300 font-extrabold uppercase tracking-widest bg-slate-950/90 px-2 py-0.5 rounded-md border border-emerald-500/30">Target Barcode / QR</span>
                        </div>
                    </div>

                    <!-- Inactive Placeholder -->
                    <div x-show="!cameraActive" class="text-center p-6 space-y-2 text-slate-500">
                        <i data-lucide="qr-code" class="w-12 h-12 mx-auto stroke-1 text-slate-600"></i>
                        <p class="text-xs font-semibold text-slate-400">Mobile Barcode Scanner Ready</p>
                        <p class="text-[11px] text-slate-500">Tap "Start Camera" to scan 1D (EAN/UPC/Code128) & 2D (QR) barcodes.</p>
                    </div>
                </div>

                <!-- HTTPS Notice for Mobile Camera Access -->
                <div x-show="isInsecureContext" class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-300 dark:border-amber-800 text-[11px] text-amber-800 dark:text-amber-300 flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4 flex-shrink-0 text-amber-500"></i>
                    <span>Mobile browsers require HTTPS to grant camera access. On local testing, use <code>localhost</code> or a secure tunnel.</span>
                </div>
            </div>

            <!-- Manual Barcode Input Search Box -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Manual Barcode / SKU Trigger</label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <i data-lucide="scan-barcode" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input 
                            type="text" 
                            x-model="manualInput" 
                            @keydown.enter.prevent="processScannedCode(manualInput)"
                            placeholder="Type barcode or SKU and press Enter..." 
                            class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <button 
                        @click="processScannedCode(manualInput)"
                        class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-bold text-xs transition flex items-center gap-1.5 shrink-0">
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        <span>Process</span>
                    </button>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: Real-Time Scanned Items Ledger & Actions (7 Cols) -->
        <div class="lg:col-span-7 space-y-4">

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col min-h-[580px]">
                
                <!-- Table Header & Action Strip -->
                <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-slate-50/50 dark:bg-slate-850/50">
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="list-checks" class="w-4 h-4 text-emerald-500"></i>
                            <span>Scanned Items Stream</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500" x-text="scannedItems.length + ' items'">0 items</span>
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Real-time inventory ledger updated with every laser gun or camera scan.</p>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button 
                            x-show="scannedItems.length > 0"
                            @click="commitBatch()" 
                            :disabled="isCommitting"
                            class="flex-1 sm:flex-initial px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-md shadow-emerald-600/20 disabled:opacity-50">
                            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                            <span x-text="isCommitting ? 'Saving Batch...' : 'Commit All to Stock'">Commit All to Stock</span>
                        </button>

                        <button 
                            x-show="scannedItems.length > 0"
                            @click="clearSession()" 
                            class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-rose-500 hover:bg-rose-500/10 transition" title="Clear Scanned List">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Scanned Items Table Body -->
                <div class="flex-1 overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50 dark:bg-slate-800/40 text-[11px] uppercase font-bold text-slate-400 border-b border-slate-100 dark:border-slate-800">
                            <tr>
                                <th class="px-4 py-3">Product / Barcode</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                <th class="px-4 py-3">Cost / Sell Price</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <template x-for="(item, index) in scannedItems" :key="index">
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                    
                                    <!-- Product & Barcode -->
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-900 dark:text-white line-clamp-1 max-w-[200px]" x-text="item.name"></div>
                                        <div class="font-mono text-[10px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                                            <span class="text-emerald-600 dark:text-emerald-400 font-bold" x-text="item.barcode"></span>
                                            <span>&bull;</span>
                                            <span x-text="item.sku"></span>
                                        </div>
                                    </td>

                                    <!-- Category -->
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                        <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-[10px] font-semibold" x-text="item.category_name"></span>
                                    </td>

                                    <!-- Quantity Adjustment Controls -->
                                    <td class="px-4 py-3 text-center">
                                        <div class="inline-flex items-center gap-1 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl">
                                            <button @click="adjustItemQty(index, -1)" class="w-5 h-5 rounded-lg bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold flex items-center justify-center hover:bg-slate-200 text-xs">-</button>
                                            <span class="font-mono font-bold text-slate-900 dark:text-white px-1.5 code-font" x-text="item.quantity">1</span>
                                            <button @click="adjustItemQty(index, 1)" class="w-5 h-5 rounded-lg bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold flex items-center justify-center hover:bg-slate-200 text-xs">+</button>
                                        </div>
                                    </td>

                                    <!-- Cost & Selling Price -->
                                    <td class="px-4 py-3 font-mono">
                                        <div class="text-slate-900 dark:text-white font-semibold">৳<span x-text="Number(item.selling_price).toFixed(0)"></span></div>
                                        <div class="text-[10px] text-slate-400">Cost: ৳<span x-text="Number(item.purchase_price).toFixed(0)"></span></div>
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="px-4 py-3 text-center">
                                        <span 
                                            :class="item.is_existing ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300'"
                                            class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                            x-text="item.is_existing ? 'Stock-In' : 'New Entry'">
                                        </span>
                                    </td>

                                    <!-- Actions (Print Sticker / Remove) -->
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button 
                                                x-show="item.product_id"
                                                @click="openPrintLabel(item.product_id)" 
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition" 
                                                title="Print Thermal Barcode Sticker">
                                                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                            </button>
                                            <button 
                                                @click="removeItem(index)" 
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-500/10 transition" 
                                                title="Remove Item">
                                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </div>
                                    </td>

                                </tr>
                            </template>

                            <tr x-show="scannedItems.length === 0">
                                <td colspan="6" class="px-4 py-16 text-center text-slate-400">
                                    <i data-lucide="scan-barcode" class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-700 mb-2 stroke-1"></i>
                                    <div class="font-bold text-slate-600 dark:text-slate-300">No items scanned yet</div>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                        Scan any hardware barcode using your laser gun or camera. Existing items will automatically stock in, and new barcodes will trigger the instant cataloging engine!
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Summary Bar -->
                <div x-show="scannedItems.length > 0" class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-850/50 flex items-center justify-between text-xs">
                    <div class="text-slate-500">
                        Total Units: <strong class="text-slate-900 dark:text-white" x-text="sessionTotalUnits"></strong>
                    </div>
                    <div class="text-slate-500">
                        Total Est. Value: <strong class="text-emerald-600 dark:text-emerald-400 code-font">৳<span x-text="sessionTotalValue.toFixed(2)"></span></strong>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- MODAL: Rapid Product Auto-Entry (Triggers when unknown barcode is scanned) -->
    <div x-cloak x-show="entryModalOpen" x-transition.opacity class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="entryModalOpen = false" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl max-w-lg w-full p-6 space-y-4">
            
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-purple-500/20 text-purple-500 flex items-center justify-center">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white">New Barcode Detected</h3>
                        <span class="text-[10px] text-slate-400">Fast 1-Click Product Registration</span>
                    </div>
                </div>
                <button @click="entryModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form @submit.prevent="submitQuickStore()" class="space-y-3.5 text-xs">
                
                <!-- Barcode & SKU Row -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Scanned Barcode *</label>
                        <input type="text" x-model="modalProduct.barcode" required readonly class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 font-mono font-bold text-slate-700 dark:text-slate-300 text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Auto SKU *</label>
                        <input type="text" x-model="modalProduct.sku" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 font-mono text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- Product Name -->
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Product Name *</label>
                    <input type="text" x-model="modalProduct.name" required placeholder="e.g. STM32 Nucleo Development Board" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Category & Brand -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Category *</label>
                        <select x-model="modalProduct.category_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Brand</label>
                        <select x-model="modalProduct.brand_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">-- Generic / Default --</option>
                            @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Pricing & Initial Stock -->
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Purchase Cost (৳) *</label>
                        <input type="number" step="0.01" min="0" x-model.number="modalProduct.purchase_price" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Selling Price (৳) *</label>
                        <input type="number" step="0.01" min="0" x-model.number="modalProduct.selling_price" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Initial Stock *</label>
                        <input type="number" min="1" x-model.number="modalProduct.stock_quantity" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold code-font outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- Warranty Terms -->
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Warranty Coverage</label>
                    <input type="text" x-model="modalProduct.warranty" placeholder="1 Year Official Warranty" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="entryModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold">
                        Cancel
                    </button>
                    <button type="submit" :disabled="isSavingModal" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition shadow-sm flex items-center gap-1.5">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span x-text="isSavingModal ? 'Saving...' : 'Catalog Product & Add Stock'">Catalog Product & Add Stock</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function scannerApp() {
    return {
        scanMode: 'stock_in', // stock_in, fast_entry, batch_queue
        batchIncrement: 1,
        soundEnabled: true,
        cameraActive: false,
        html5QrCode: null,
        availableCameras: [],
        selectedCameraId: '',
        currentCameraIndex: 0,
        torchSupported: false,
        isTorchOn: false,
        isInsecureContext: false,
        isProcessingScan: false,
        manualInput: '',
        scannedItems: [],
        entryModalOpen: false,
        isSavingModal: false,
        isCommitting: false,
        lastScanTime: 0,
        keyBuffer: '',
        lastKeyTime: 0,
        categoriesList: @json($categories),

        modalProduct: {
            barcode: '',
            sku: '',
            name: '',
            category_id: '',
            brand_id: '',
            purchase_price: 0,
            selling_price: 0,
            stock_quantity: 1,
            warranty: '1 Year Official Warranty'
        },

        get sessionScannedCount() {
            return this.scannedItems.length;
        },

        get sessionTotalUnits() {
            return this.scannedItems.reduce((sum, item) => sum + Number(item.quantity || 0), 0);
        },

        get sessionTotalValue() {
            return this.scannedItems.reduce((sum, item) => sum + (Number(item.quantity || 0) * Number(item.selling_price || 0)), 0);
        },

        initScanner() {
            // Check Secure Context (HTTPS or Localhost)
            if (window.isSecureContext === false && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                this.isInsecureContext = true;
            }

            // Setup Global Hardware Gun Scanner Interceptor
            window.addEventListener('keydown', (e) => {
                const now = Date.now();
                const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
                const isInputActive = activeTag === 'input' || activeTag === 'textarea' || activeTag === 'select';

                if (e.key === 'Enter') {
                    if (this.keyBuffer.length >= 3) {
                        const code = this.keyBuffer.trim();
                        this.keyBuffer = '';
                        this.processScannedCode(code);
                        if (!isInputActive) {
                            e.preventDefault();
                        }
                    }
                    this.keyBuffer = '';
                    return;
                }

                // Append printable single characters
                if (e.key.length === 1) {
                    if (now - this.lastKeyTime > 200) {
                        this.keyBuffer = '';
                    }
                    this.keyBuffer += e.key;
                    this.lastKeyTime = now;
                }
            });
        },

        // Web Audio Synthesizer & Mobile Haptics
        playBeep(freq = 1000, type = 'sine', duration = 0.1) {
            // Mobile Haptic Vibration
            if (navigator.vibrate) {
                try { navigator.vibrate(70); } catch(e) {}
            }

            if (!this.soundEnabled) return;
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = type;
                osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
                gain.gain.setValueAtTime(0.15, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + duration);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start();
                osc.stop(audioCtx.currentTime + duration);
            } catch (e) {}
        },

        playChime() {
            if (navigator.vibrate) {
                try { navigator.vibrate([60, 40, 90]); } catch(e) {}
            }
            this.playBeep(880, 'sine', 0.08);
            setTimeout(() => this.playBeep(1320, 'sine', 0.12), 90);
        },

        playError() {
            if (navigator.vibrate) {
                try { navigator.vibrate([150, 60, 150]); } catch(e) {}
            }
            this.playBeep(300, 'sawtooth', 0.2);
        },

        // Camera Toggle
        toggleCamera() {
            if (this.cameraActive) {
                this.stopCamera();
            } else {
                this.startCamera();
            }
        },

        async startCamera() {
            if (!window.Html5Qrcode) {
                alert('Camera library loading. Please check internet connection and try again.');
                return;
            }

            try {
                // Discover cameras
                const devices = await Html5Qrcode.getCameras();
                if (devices && devices.length > 0) {
                    this.availableCameras = devices;
                    if (!this.selectedCameraId) {
                        // Prefer rear / back / environment camera
                        const backCam = devices.find(d => 
                            d.label.toLowerCase().includes('back') || 
                            d.label.toLowerCase().includes('rear') ||
                            d.label.toLowerCase().includes('environment')
                        );
                        this.selectedCameraId = backCam ? backCam.id : devices[0].id;
                        this.currentCameraIndex = devices.indexOf(backCam || devices[0]);
                    }
                }
            } catch (e) {
                console.warn("Could not enumerate cameras beforehand", e);
            }

            // Explicitly support ALL 1D & 2D Barcode Formats for product retail/hardware
            const formatsToSupport = [
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.EAN_8,
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.CODE_93,
                Html5QrcodeSupportedFormats.UPC_A,
                Html5QrcodeSupportedFormats.UPC_E,
                Html5QrcodeSupportedFormats.UPC_EAN_EXTENSION,
                Html5QrcodeSupportedFormats.ITF,
                Html5QrcodeSupportedFormats.QR_CODE,
                Html5QrcodeSupportedFormats.DATA_MATRIX,
                Html5QrcodeSupportedFormats.CODABAR
            ];

            try {
                if (this.html5QrCode) {
                    try { await this.html5QrCode.stop(); } catch(e) {}
                }

                this.html5QrCode = new Html5Qrcode("camera-reader", {
                    formatsToSupport: formatsToSupport,
                    experimentalFeatures: {
                        useBarCodeDetectorIfSupported: true
                    },
                    verbose: false
                });

                const cameraConfig = this.selectedCameraId 
                    ? { deviceId: { exact: this.selectedCameraId } }
                    : { facingMode: "environment" };

                const scanConfig = {
                    fps: 15,
                    qrbox: (viewfinderWidth, viewfinderHeight) => {
                        const minDim = Math.min(viewfinderWidth, viewfinderHeight);
                        const width = Math.floor(minDim * 0.88);
                        const height = Math.floor(minDim * 0.55);
                        return { 
                            width: Math.max(220, Math.min(width, 340)), 
                            height: Math.max(120, Math.min(height, 200)) 
                        };
                    },
                    aspectRatio: 1.777778,
                    showTorchButtonIfSupported: true,
                    videoConstraints: {
                        facingMode: "environment",
                        focusMode: "continuous"
                    }
                };

                await this.html5QrCode.start(
                    cameraConfig,
                    scanConfig,
                    (decodedText) => {
                        const now = Date.now();
                        if (now - this.lastScanTime > 1200 && !this.isProcessingScan && !this.entryModalOpen) {
                            this.lastScanTime = now;
                            this.processScannedCode(decodedText);
                        }
                    },
                    (errorMessage) => {}
                );

                this.cameraActive = true;

                // Check Torch / Flashlight capabilities
                setTimeout(() => {
                    try {
                        const capabilities = this.html5QrCode.getRunningTrackCapabilities();
                        this.torchSupported = !!capabilities.torch;
                    } catch(e) {
                        this.torchSupported = false;
                    }
                }, 800);

            } catch (err) {
                console.error("Camera start error", err);
                this.cameraActive = false;
                alert("Camera permission denied or camera unavailable.\n\nTip: On mobile devices, ensure your browser is allowed camera permissions and you are accessing via HTTPS or localhost.");
            }
        },

        async stopCamera() {
            if (this.html5QrCode) {
                try {
                    await this.html5QrCode.stop();
                } catch(e) {}
            }
            this.cameraActive = false;
            this.isTorchOn = false;
            this.torchSupported = false;
        },

        async switchCamera() {
            if (this.availableCameras.length <= 1) return;
            this.currentCameraIndex = (this.currentCameraIndex + 1) % this.availableCameras.length;
            this.selectedCameraId = this.availableCameras[this.currentCameraIndex].id;
            await this.stopCamera();
            await this.startCamera();
        },

        async onCameraSelected() {
            if (this.cameraActive) {
                await this.stopCamera();
                await this.startCamera();
            }
        },

        async toggleTorch() {
            if (!this.html5QrCode || !this.torchSupported) return;
            this.isTorchOn = !this.isTorchOn;
            try {
                await this.html5QrCode.applyVideoConstraints({
                    advanced: [{ torch: this.isTorchOn }]
                });
            } catch(e) {
                console.warn("Torch toggle error", e);
            }
        },

        // Master Code Processor with Concurrency Guard
        async processScannedCode(code) {
            const cleanCode = (code || '').trim();
            if (!cleanCode || this.isProcessingScan) return;

            this.isProcessingScan = true;
            this.manualInput = '';

            try {
                const res = await fetch('{{ route("admin.products.scanner.lookup") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ code: cleanCode })
                });

                const data = await res.json();

                if (data.exists) {
                    await this.handleExistingProduct(data.product);
                } else {
                    this.handleNewBarcode(cleanCode, data.suggestion);
                }

            } catch (err) {
                console.error("Lookup failed", err);
                this.playError();
            } finally {
                setTimeout(() => {
                    this.isProcessingScan = false;
                }, 500);
            }
        },

        // Handler for Existing Product
        async handleExistingProduct(prod) {
            const qty = this.batchIncrement || 1;

            if (this.scanMode === 'stock_in') {
                try {
                    const res = await fetch('{{ route("admin.products.scanner.stock_in") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            product_id: prod.id,
                            quantity: qty
                        })
                    });

                    const resData = await res.json();
                    if (resData.success) {
                        this.playBeep(1200, 'sine', 0.1);
                        this.addOrUpdateSessionLedger({
                            product_id: prod.id,
                            name: prod.name,
                            barcode: prod.barcode || prod.sku,
                            sku: prod.sku,
                            category_id: prod.category_id,
                            category_name: prod.category_name,
                            quantity: qty,
                            purchase_price: prod.purchase_price,
                            selling_price: prod.selling_price,
                            is_existing: true
                        });
                    }
                } catch (e) {
                    this.playError();
                }
            } else {
                this.playBeep(1000, 'sine', 0.08);
                this.addOrUpdateSessionLedger({
                    product_id: prod.id,
                    name: prod.name,
                    barcode: prod.barcode || prod.sku,
                    sku: prod.sku,
                    category_id: prod.category_id,
                    category_name: prod.category_name,
                    quantity: qty,
                    purchase_price: prod.purchase_price,
                    selling_price: prod.selling_price,
                    is_existing: true
                });
            }
        },

        // Handler for New Unregistered Barcode
        handleNewBarcode(barcode, suggestion) {
            this.playChime();

            const defaultCat = this.categoriesList.length > 0 ? this.categoriesList[0].id : '';

            this.modalProduct = {
                barcode: barcode,
                sku: suggestion ? suggestion.sku : ('PCB-' + barcode),
                name: suggestion ? suggestion.name : ('Component ' + barcode),
                category_id: defaultCat,
                brand_id: '',
                purchase_price: 100,
                selling_price: 150,
                stock_quantity: this.batchIncrement || 1,
                warranty: '1 Year Official Warranty'
            };

            this.entryModalOpen = true;
        },

        // Submit Quick Registration
        async submitQuickStore() {
            this.isSavingModal = true;
            try {
                const res = await fetch('{{ route("admin.products.scanner.quick_store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.modalProduct)
                });

                const data = await res.json();
                if (data.success) {
                    this.playBeep(1500, 'sine', 0.15);
                    this.entryModalOpen = false;
                    
                    const prod = data.product;
                    this.addOrUpdateSessionLedger({
                        product_id: prod.id,
                        name: prod.name,
                        barcode: prod.barcode,
                        sku: prod.sku,
                        category_id: this.modalProduct.category_id,
                        category_name: prod.category_name,
                        quantity: Number(this.modalProduct.stock_quantity || 1),
                        purchase_price: Number(this.modalProduct.purchase_price || 0),
                        selling_price: Number(this.modalProduct.selling_price || 0),
                        is_existing: false
                    });
                } else {
                    alert(data.message || 'Error saving product');
                    this.playError();
                }
            } catch (err) {
                console.error("Save error", err);
                this.playError();
            } finally {
                this.isSavingModal = false;
            }
        },

        // Session Ledger Manager
        addOrUpdateSessionLedger(item) {
            const existingIdx = this.scannedItems.findIndex(i => i.barcode === item.barcode);
            if (existingIdx >= 0) {
                this.scannedItems[existingIdx].quantity += item.quantity;
            } else {
                this.scannedItems.unshift(item);
            }
        },

        adjustItemQty(index, delta) {
            if (this.scannedItems[index]) {
                const newQty = this.scannedItems[index].quantity + delta;
                if (newQty <= 0) {
                    this.scannedItems.splice(index, 1);
                } else {
                    this.scannedItems[index].quantity = newQty;
                }
            }
        },

        removeItem(index) {
            this.scannedItems.splice(index, 1);
        },

        clearSession() {
            if (confirm('Clear the current scan stream?')) {
                this.scannedItems = [];
            }
        },

        // Batch Commit to Database
        async commitBatch() {
            if (this.scannedItems.length === 0) return;
            this.isCommitting = true;

            try {
                const res = await fetch('{{ route("admin.products.scanner.batch_commit") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ items: this.scannedItems })
                });

                const data = await res.json();
                if (data.success) {
                    this.playChime();
                    alert(data.message);
                    this.scannedItems = [];
                } else {
                    alert(data.message || 'Batch commit failed.');
                }
            } catch (e) {
                console.error(e);
                alert('Error submitting batch commit.');
            } finally {
                this.isCommitting = false;
            }
        },

        // Print Sticker Trigger
        openPrintLabel(productId) {
            window.open('/admin/products/' + productId + '/barcode-label', '_blank', 'width=450,height=400');
        }
    };
}
</script>
@endpush
@endsection

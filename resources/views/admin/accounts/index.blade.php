@extends('layouts.admin')

@section('title', 'Accounts & Funds Management')
@section('page-title', 'Accounts & Multi-Channel Fund Balances')

@section('content')
<div x-data="accountsManager()" class="space-y-6">

    <!-- Top Total Balance & Cash Flow Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Available Balance -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl group-hover:bg-emerald-500/20 transition"></div>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Fund Balance</span>
            <div class="text-2xl font-black text-emerald-500 code-font mt-2">৳{{ number_format($totalBalance, 2) }}</div>
            <div class="text-xs text-slate-400 mt-1">Across all active accounts</div>
        </div>

        <!-- This Month Inflow -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-sky-500/10 rounded-full blur-xl group-hover:bg-sky-500/20 transition"></div>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">This Month Inflow (Credit)</span>
            <div class="text-2xl font-black text-sky-500 code-font mt-2">৳{{ number_format($thisMonthIn, 2) }}</div>
            <div class="text-xs text-slate-400 mt-1">{{ now()->format('F Y') }} sales & deposits</div>
        </div>

        <!-- This Month Outflow -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-rose-500/10 rounded-full blur-xl group-hover:bg-rose-500/20 transition"></div>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">This Month Outflow (Debit)</span>
            <div class="text-2xl font-black text-rose-500 code-font mt-2">৳{{ number_format($thisMonthOut, 2) }}</div>
            <div class="text-xs text-slate-400 mt-1">Expenses & supplier payments</div>
        </div>

        <!-- This Year Inflow -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-purple-500/10 rounded-full blur-xl group-hover:bg-purple-500/20 transition"></div>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Yearly Total Inflow</span>
            <div class="text-2xl font-black text-purple-500 code-font mt-2">৳{{ number_format($thisYearIn, 2) }}</div>
            <div class="text-xs text-slate-400 mt-1">Total revenue {{ now()->year }}</div>
        </div>

    </div>

    <!-- Accounts Cards & Actions Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span>Active Payment & Bank Accounts</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-mono">
                    {{ $accounts->count() }} Total
                </span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Manage live balance, edit accounts, deposit funds, or perform internal transfers</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button @click="depositModalOpen = true" type="button" class="px-3.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                <span>Deposit Fund</span>
            </button>
            <button @click="transferModalOpen = true" type="button" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <i data-lucide="repeat" class="w-4 h-4"></i>
                <span>Internal Transfer</span>
            </button>
            <button @click="addModalOpen = true" type="button" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>+ Add Account</span>
            </button>
        </div>
    </div>

    <!-- Accounts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-5">
        @forelse($accounts as $account)
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border {{ $account->is_active ? 'border-slate-200 dark:border-slate-800' : 'border-slate-300 dark:border-slate-800 opacity-60' }} shadow-xs flex flex-col justify-between space-y-4 hover:border-emerald-500/50 hover:shadow-md transition">
            
            <div class="space-y-3">
                <!-- Top row: Type Icon, Title & Badges -->
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $account->account_type === 'cash' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400' : ($account->account_type === 'bank' ? 'bg-sky-100 text-sky-600 dark:bg-sky-950 dark:text-sky-400' : 'bg-purple-100 text-purple-600 dark:bg-purple-950 dark:text-purple-400') }}">
                            <i data-lucide="{{ $account->account_type === 'cash' ? 'banknote' : ($account->account_type === 'bank' ? 'landmark' : 'smartphone') }}" class="w-5 h-5"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <h4 class="font-bold text-sm text-slate-900 dark:text-white truncate" title="{{ $account->name }}">{{ $account->name }}</h4>
                                @if($account->is_default)
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase bg-amber-500/10 text-amber-500 border border-amber-500/20">Default</span>
                                @endif
                            </div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 font-mono tracking-wider">{{ str_replace('_', ' ', $account->account_type) }}</span>
                        </div>
                    </div>

                    <!-- Status Pill -->
                    <div>
                        @if($account->is_active)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Active</span>
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                            <span>Inactive</span>
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Account details info -->
                <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-2.5 text-xs space-y-1">
                    @if($account->account_number)
                    <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                        <span class="text-[10px] text-slate-400 uppercase">A/C No:</span>
                        <span class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ $account->account_number }}</span>
                    </div>
                    @endif
                    @if($account->bank_name)
                    <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                        <span class="text-[10px] text-slate-400 uppercase">Bank / Provider:</span>
                        <span class="font-medium text-slate-800 dark:text-slate-200 truncate max-w-[140px]" title="{{ $account->bank_name }}">{{ $account->bank_name }}</span>
                    </div>
                    @endif
                    @if($account->branch_name)
                    <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                        <span class="text-[10px] text-slate-400 uppercase">Branch:</span>
                        <span class="text-slate-700 dark:text-slate-300 truncate max-w-[140px]">{{ $account->branch_name }}</span>
                    </div>
                    @endif
                    @if(!$account->account_number && !$account->bank_name)
                    <div class="text-[11px] text-slate-400 italic">Direct cash counter / in-hand fund</div>
                    @endif
                </div>
            </div>

            <!-- Bottom: Balance & Actions -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Current Balance:</span>
                    <div class="text-lg font-black text-emerald-600 dark:text-emerald-400 code-font">
                        ৳{{ number_format($account->current_balance, 2) }}
                    </div>
                </div>

                <!-- Action Toolbar for this Account -->
                <div class="flex items-center justify-end gap-1.5 pt-1">
                    <!-- Edit Button -->
                    <button 
                        @click="openEdit({{ json_encode($account) }})" 
                        type="button" 
                        class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-slate-800 dark:hover:text-emerald-400 transition cursor-pointer" 
                        title="Edit Account Details">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </button>

                    <!-- Toggle Status Form -->
                    <form action="{{ route('admin.accounts.toggle', $account->id) }}" method="POST" class="inline">
                        @csrf
                        <button 
                            type="submit" 
                            class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-amber-50 hover:text-amber-600 dark:hover:bg-slate-800 dark:hover:text-amber-400 transition cursor-pointer" 
                            title="{{ $account->is_active ? 'Deactivate Account' : 'Activate Account' }}">
                            <i data-lucide="{{ $account->is_active ? 'pause-circle' : 'play-circle' }}" class="w-4 h-4"></i>
                        </button>
                    </form>

                    <!-- Delete Button -->
                    <button 
                        @click="openDelete({{ json_encode($account) }})" 
                        type="button" 
                        class="p-2 rounded-xl text-slate-400 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-slate-800 dark:hover:text-rose-400 transition cursor-pointer" 
                        title="Delete Account">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

        </div>
        @empty
        <div class="col-span-full p-8 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 text-slate-400">
            <i data-lucide="wallet" class="w-12 h-12 mx-auto text-slate-300 mb-2"></i>
            <p class="font-bold text-sm">No Accounts Configured</p>
            <p class="text-xs text-slate-500 mt-1">Click "+ Add Account" to configure Cash, Bank, or Mobile Banking accounts.</p>
        </div>
        @endforelse
    </div>

    <!-- Recent Transactions Ledger -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-wrap gap-2">
            <div>
                <h4 class="font-bold text-sm text-slate-900 dark:text-white">Recent Transaction Ledger</h4>
                <p class="text-xs text-slate-500">Live transaction history across all payment channels</p>
            </div>
            <div class="text-xs text-slate-400">Showing latest {{ $recentTransactions->count() }} transactions</div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="p-3.5">Date</th>
                        <th class="p-3.5">Account</th>
                        <th class="p-3.5">Type</th>
                        <th class="p-3.5">Amount</th>
                        <th class="p-3.5">Balance After</th>
                        <th class="p-3.5">Description / Note</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($recentTransactions as $trx)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <td class="p-3.5 font-mono text-slate-400 whitespace-nowrap">{{ $trx->transaction_date ? \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') : '' }}</td>
                        <td class="p-3.5 font-semibold text-slate-900 dark:text-white whitespace-nowrap">
                            {{ $trx->account->name ?? 'N/A' }}
                        </td>
                        <td class="p-3.5">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $trx->type === 'credit' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' }}">
                                {{ $trx->type }}
                            </span>
                        </td>
                        <td class="p-3.5 font-bold code-font {{ $trx->type === 'credit' ? 'text-emerald-500' : 'text-rose-500' }} whitespace-nowrap">
                            {{ $trx->type === 'credit' ? '+' : '-' }}৳{{ number_format($trx->amount, 2) }}
                        </td>
                        <td class="p-3.5 font-bold code-font text-slate-800 dark:text-slate-200 whitespace-nowrap">
                            ৳{{ number_format($trx->balance_after, 2) }}
                        </td>
                        <td class="p-3.5 text-slate-500 dark:text-slate-400">
                            {{ $trx->note ?? '-' }}
                            @if($trx->reference_no)
                            <span class="ml-1 text-[10px] font-mono text-slate-400">Ref: {{ $trx->reference_no }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">No account transactions recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL 1: ADD ACCOUNT -->
    <div x-cloak x-show="addModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.away="addModalOpen = false" class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Create New Account</h3>
                </div>
                <button @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">&times;</button>
            </div>
            
            <form method="POST" action="{{ route('admin.accounts.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Account Title / Name *</label>
                    <input type="text" name="name" required placeholder="e.g. BRAC Bank PLC or Cash Counter 1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Account Type *</label>
                        <select name="account_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                            <option value="cash">Cash In Drawer</option>
                            <option value="bank">Bank Account</option>
                            <option value="mobile_banking">Mobile Banking (bKash/Nagad)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Account Number</label>
                        <input type="text" name="account_number" placeholder="e.g. 1501203498001" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-mono bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Bank / Gateway Name</label>
                        <input type="text" name="bank_name" placeholder="e.g. Dutch-Bangla Bank or bKash" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Branch Name</label>
                        <input type="text" name="branch_name" placeholder="e.g. Gulshan Branch" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Opening Balance (৳)</label>
                    <input type="number" step="0.01" name="opening_balance" placeholder="0.00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs code-font font-bold text-emerald-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none">
                    <p class="text-[10px] text-slate-400 mt-1">Initial fund recorded automatically upon creation.</p>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="addModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 text-xs font-semibold transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Save Account</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: EDIT ACCOUNT -->
    <div x-cloak x-show="editModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.away="editModalOpen = false" class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-500 flex items-center justify-center">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Edit Account Details</h3>
                        <p class="text-[10px] text-slate-400" x-text="'Editing: ' + editAccountData.name"></p>
                    </div>
                </div>
                <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">&times;</button>
            </div>
            
            <form :action="editFormUrl" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Account Title / Name *</label>
                    <input type="text" name="name" x-model="editAccountData.name" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Account Type *</label>
                        <select name="account_type" x-model="editAccountData.account_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                            <option value="cash">Cash In Drawer</option>
                            <option value="bank">Bank Account</option>
                            <option value="mobile_banking">Mobile Banking (bKash/Nagad)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Account Number</label>
                        <input type="text" name="account_number" x-model="editAccountData.account_number" placeholder="A/C number" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-mono bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Bank / Gateway Name</label>
                        <input type="text" name="bank_name" x-model="editAccountData.bank_name" placeholder="Bank or Gateway" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Branch Name</label>
                        <input type="text" name="branch_name" x-model="editAccountData.branch_name" placeholder="Branch location" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none">
                    </div>
                </div>

                <!-- Switches: Default Account & Active Status -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3.5 space-y-3 border border-slate-100 dark:border-slate-800">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_default" value="1" x-model="editAccountData.is_default" class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">Set as Primary Default Account</span>
                    </label>

                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" x-model="editAccountData.is_active" class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">Account Active & Enabled</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 text-xs font-semibold transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Update Account</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: DELETE ACCOUNT CONFIRMATION -->
    <div x-cloak x-show="deleteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.away="deleteModalOpen = false" class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            <div class="flex items-center gap-3 text-rose-600">
                <div class="w-10 h-10 rounded-2xl bg-rose-100 dark:bg-rose-950 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 dark:text-rose-400"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Delete Account</h3>
                    <p class="text-xs text-slate-500">This action cannot be undone.</p>
                </div>
            </div>

            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                Are you sure you want to delete <strong class="text-slate-900 dark:text-white" x-text="deleteAccountData.name"></strong>? 
                All associated transaction logs and ledger history for this account will be cleaned up.
            </p>

            <form :action="deleteFormUrl" method="POST" class="pt-2 flex justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-xs transition">Confirm & Delete</button>
            </form>
        </div>
    </div>

    <!-- MODAL 4: DEPOSIT -->
    <div x-cloak x-show="depositModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.away="depositModalOpen = false" class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-500 flex items-center justify-center">
                        <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Deposit Funds to Account</h3>
                </div>
                <button @click="depositModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">&times;</button>
            </div>
            
            <form method="POST" action="{{ route('admin.accounts.deposit') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Target Account *</label>
                    <select name="account_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none">
                        @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }} (Balance: ৳{{ number_format($acc->current_balance, 2) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Deposit Amount (৳) *</label>
                    <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-emerald-600 code-font bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Date *</label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Note / Reference</label>
                    <input type="text" name="note" placeholder="Owner capital injection / Cash deposit" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none">
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="depositModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs shadow-xs transition">Record Deposit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 5: TRANSFER -->
    <div x-cloak x-show="transferModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.away="transferModalOpen = false" class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center">
                        <i data-lucide="repeat" class="w-4 h-4"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Internal Fund Transfer</h3>
                </div>
                <button @click="transferModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">&times;</button>
            </div>
            
            <form method="POST" action="{{ route('admin.accounts.transfer') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Transfer From (Source) *</label>
                    <select name="from_account_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none">
                        @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }} (৳{{ number_format($acc->current_balance, 2) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Transfer To (Destination) *</label>
                    <select name="to_account_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none">
                        @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }} (৳{{ number_format($acc->current_balance, 2) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Amount (৳) *</label>
                    <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold code-font bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Date *</label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white outline-none">
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="transferModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-xs transition">Execute Transfer</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function accountsManager() {
        return {
            addModalOpen: false,
            editModalOpen: false,
            deleteModalOpen: false,
            depositModalOpen: false,
            transferModalOpen: false,
            editAccountData: {},
            deleteAccountData: {},
            editFormUrl: '',
            deleteFormUrl: '',

            openEdit(account) {
                this.editAccountData = {
                    id: account.id,
                    name: account.name,
                    account_type: account.account_type,
                    account_number: account.account_number || '',
                    bank_name: account.bank_name || '',
                    branch_name: account.branch_name || '',
                    is_default: Boolean(account.is_default),
                    is_active: Boolean(account.is_active)
                };
                this.editFormUrl = `{{ url('/admin/accounts') }}/${account.id}`;
                this.editModalOpen = true;
                this.$nextTick(() => {
                    lucide.createIcons();
                });
            },

            openDelete(account) {
                this.deleteAccountData = account;
                this.deleteFormUrl = `{{ url('/admin/accounts') }}/${account.id}`;
                this.deleteModalOpen = true;
                this.$nextTick(() => {
                    lucide.createIcons();
                });
            }
        };
    }
</script>
@endsection

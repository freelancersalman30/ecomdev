@extends('layouts.admin')

@section('title', 'Accounts & Funds Management')
@section('page-title', 'Accounts & Multi-Channel Fund Balances')

@section('content')
<div class="space-y-6">

    <!-- Top Total Balance & Cash Flow Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Available Balance -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Fund Balance</span>
            <div class="text-2xl font-black text-emerald-500 code-font mt-2">৳{{ number_format($totalBalance, 2) }}</div>
            <div class="text-xs text-slate-400 mt-1">Across all {{ $accounts->count() }} active accounts</div>
        </div>

        <!-- This Month Inflow -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">This Month Inflow (Credit)</span>
            <div class="text-2xl font-black text-sky-500 code-font mt-2">৳{{ number_format($thisMonthIn, 2) }}</div>
            <div class="text-xs text-slate-400 mt-1">{{ now()->format('F Y') }} sales & deposits</div>
        </div>

        <!-- This Month Outflow -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">This Month Outflow (Debit)</span>
            <div class="text-2xl font-black text-rose-500 code-font mt-2">৳{{ number_format($thisMonthOut, 2) }}</div>
            <div class="text-xs text-slate-400 mt-1">Expenses & supplier payments</div>
        </div>

        <!-- This Year Inflow -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Yearly Total Inflow</span>
            <div class="text-2xl font-black text-purple-500 code-font mt-2">৳{{ number_format($thisYearIn, 2) }}</div>
            <div class="text-xs text-slate-400 mt-1">Total revenue {{ now()->year }}</div>
        </div>

    </div>

    <!-- Accounts Cards & Actions Toolbar -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Active Payment & Bank Accounts</h3>
            <p class="text-xs text-slate-500">Live balance tracking for Cash, Bank, and Mobile Banking accounts</p>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="document.getElementById('depositModal').style.display = 'flex'" class="px-3.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold shadow-md transition flex items-center gap-1.5">
                <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                <span>Deposit Fund</span>
            </button>
            <button onclick="document.getElementById('transferModal').style.display = 'flex'" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold shadow-md transition flex items-center gap-1.5">
                <i data-lucide="repeat" class="w-4 h-4"></i>
                <span>Internal Transfer</span>
            </button>
            <button onclick="document.getElementById('addAccountModal').style.display = 'flex'" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md transition flex items-center gap-1.5">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>+ Add Account</span>
            </button>
        </div>
    </div>

    <!-- Accounts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        @foreach($accounts as $account)
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between space-y-4 hover:border-emerald-500/50 transition">
            <div class="space-y-2">
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ $account->name }}</h4>
                        <span class="text-[10px] uppercase font-bold text-slate-400 font-mono">{{ str_replace('_', ' ', $account->account_type) }}</span>
                    </div>
                    <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300">
                        <i data-lucide="{{ $account->account_type === 'cash' ? 'banknote' : ($account->account_type === 'bank' ? 'landmark' : 'smartphone') }}" class="w-4 h-4"></i>
                    </div>
                </div>

                @if($account->account_number)
                <div class="text-[11px] font-mono text-slate-500">{{ $account->account_number }}</div>
                @endif
                @if($account->bank_name)
                <div class="text-[11px] text-slate-400">{{ $account->bank_name }} - {{ $account->branch_name }}</div>
                @endif
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                <span class="text-[10px] text-slate-400 uppercase font-semibold">Current Balance:</span>
                <div class="text-xl font-black text-emerald-600 dark:text-emerald-400 code-font">
                    ৳{{ number_format($account->current_balance, 2) }}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Recent Transactions Ledger -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white">
            Recent Account Transaction Ledger
        </div>
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase">
                <tr>
                    <th class="p-3">Date</th>
                    <th class="p-3">Account</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Amount</th>
                    <th class="p-3">Balance After</th>
                    <th class="p-3">Description / Ref</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($recentTransactions as $trx)
                <tr>
                    <td class="p-3 font-mono text-slate-400">{{ $trx->transaction_date->format('d M Y') }}</td>
                    <td class="p-3 font-semibold text-slate-900 dark:text-white">{{ $trx->account->name ?? '' }}</td>
                    <td class="p-3">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $trx->type === 'credit' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' }}">
                            {{ $trx->type }}
                        </span>
                    </td>
                    <td class="p-3 font-bold code-font {{ $trx->type === 'credit' ? 'text-emerald-500' : 'text-rose-500' }}">
                        {{ $trx->type === 'credit' ? '+' : '-' }}৳{{ number_format($trx->amount, 2) }}
                    </td>
                    <td class="p-3 font-bold code-font text-slate-700 dark:text-slate-300">
                        ৳{{ number_format($trx->balance_after, 2) }}
                    </td>
                    <td class="p-3 text-slate-500">{{ $trx->note }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-slate-400">No transactions recorded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODAL 1: ADD ACCOUNT -->
    <div id="addAccountModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Create New Account</h3>
                <button onclick="document.getElementById('addAccountModal').style.display = 'none'" class="text-slate-400">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.accounts.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Account Title *</label>
                    <input type="text" name="name" required placeholder="e.g. BRAC Bank PLC" class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Account Type *</label>
                    <select name="account_type" class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                        <option value="cash">Cash In Drawer</option>
                        <option value="bank">Bank Account</option>
                        <option value="mobile_banking">Mobile Banking (bKash/Nagad)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Opening Balance (৳)</label>
                    <input type="number" step="0.01" name="opening_balance" placeholder="0.00" class="w-full px-3 py-2 rounded-xl border text-xs code-font bg-white dark:bg-slate-800 outline-none">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('addAccountModal').style.display = 'none'" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-xs">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs">Save Account</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: DEPOSIT -->
    <div id="depositModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Deposit Funds to Account</h3>
                <button onclick="document.getElementById('depositModal').style.display = 'none'" class="text-slate-400">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.accounts.deposit') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Target Account *</label>
                    <select name="account_id" required class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                        @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }} (Balance: ৳{{ number_format($acc->current_balance, 2) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Deposit Amount (৳) *</label>
                    <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full px-3 py-2 rounded-xl border text-xs font-bold text-emerald-600 code-font bg-white dark:bg-slate-800 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Date *</label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Note / Reference</label>
                    <input type="text" name="note" placeholder="Owner capital injection / Cash deposit" class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('depositModal').style.display = 'none'" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-xs">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-sky-600 text-white font-bold text-xs">Record Deposit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: TRANSFER -->
    <div id="transferModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Internal Fund Transfer</h3>
                <button onclick="document.getElementById('transferModal').style.display = 'none'" class="text-slate-400">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.accounts.transfer') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Transfer From (Source) *</label>
                    <select name="from_account_id" required class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                        @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }} (৳{{ number_format($acc->current_balance, 2) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Transfer To (Destination) *</label>
                    <select name="to_account_id" required class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                        @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Amount (৳) *</label>
                    <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full px-3 py-2 rounded-xl border text-xs font-bold code-font bg-white dark:bg-slate-800 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Date *</label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('transferModal').style.display = 'none'" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-xs">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 text-white font-bold text-xs">Execute Transfer</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

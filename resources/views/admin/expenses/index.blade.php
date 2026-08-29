@extends('layouts.admin')

@section('title', 'Expenses & Budgeting')
@section('page-title', 'Expenses, Budgeting & Cost Accounting')

@section('content')
<div class="space-y-6">

    <!-- 4 SPECIFIED LOCALIZED BENGALI SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- 1. বর্তমানে তহবিলে অবশিষ্ট ব্যালেন্স -->
        <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-2xl p-5 shadow-lg shadow-emerald-600/20 space-y-2">
            <div class="flex items-center justify-between text-emerald-100">
                <span class="text-xs font-bold uppercase tracking-wider">Available Fund Balance</span>
                <i data-lucide="wallet" class="w-5 h-5"></i>
            </div>
            <div class="text-2xl font-black code-font">৳{{ number_format($availableFunds, 2) }}</div>
            <div class="text-xs font-semibold text-emerald-100/90 pt-1 border-t border-white/10">
                বর্তমানে তহবিলে অবশিষ্ট ব্যালেন্স
            </div>
        </div>

        <!-- 2. This Year (2026) - এই বছরে মোট খরচ হয়েছে -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold uppercase tracking-wider">This Year ({{ $currentYear }})</span>
                <i data-lucide="calendar" class="w-5 h-5 text-purple-500"></i>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white code-font">৳{{ number_format($thisYearExpense, 2) }}</div>
            <div class="text-xs font-medium text-slate-500 pt-1 border-t border-slate-100 dark:border-slate-800">
                এই বছরে মোট খরচ হয়েছে
            </div>
        </div>

        <!-- 3. This Month (August) - এই মাসে মোট খরচ হয়েছে -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold uppercase tracking-wider">This Month ({{ $currentMonthName }})</span>
                <i data-lucide="calendar-days" class="w-5 h-5 text-sky-500"></i>
            </div>
            <div class="text-2xl font-black text-sky-500 code-font">৳{{ number_format($thisMonthExpense, 2) }}</div>
            <div class="text-xs font-medium text-slate-500 pt-1 border-t border-slate-100 dark:border-slate-800">
                এই মাসে মোট খরচ হয়েছে
            </div>
        </div>

        <!-- 4. Today - আজকের মোট খরচ -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-2">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold uppercase tracking-wider">Today</span>
                <i data-lucide="clock" class="w-5 h-5 text-rose-500"></i>
            </div>
            <div class="text-2xl font-black text-rose-500 code-font">৳{{ number_format($todayExpense, 2) }}</div>
            <div class="text-xs font-medium text-slate-500 pt-1 border-t border-slate-100 dark:border-slate-800">
                আজকের মোট খরচ
            </div>
        </div>

    </div>

    <!-- Action Toolbar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        
        <!-- Filter Category & Period -->
        <form method="GET" action="{{ route('admin.expenses.index') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <select name="category_id" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                <option value="">All Expense Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <select name="time_filter" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                <option value="all">All Dates</option>
                <option value="today" {{ $timeFilter == 'today' ? 'selected' : '' }}>Today</option>
                <option value="this_month" {{ $timeFilter == 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="this_year" {{ $timeFilter == 'this_year' ? 'selected' : '' }}>This Year</option>
            </select>
        </form>

        <div class="flex items-center gap-2">
            <button onclick="document.getElementById('addCatModal').style.display = 'flex'" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition">
                + New Category
            </button>
            <button onclick="document.getElementById('addExpenseModal').style.display = 'flex'" class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <i data-lucide="minus-circle" class="w-4 h-4"></i>
                <span>+ Record Expense</span>
            </button>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3.5">Expense Title & Ref</th>
                        <th class="px-4 py-3.5">Category</th>
                        <th class="px-4 py-3.5">Paid From Account</th>
                        <th class="px-4 py-3.5">Amount</th>
                        <th class="px-4 py-3.5">Date</th>
                        <th class="px-4 py-3.5">Created By</th>
                        <th class="px-4 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-xs text-slate-900 dark:text-white">{{ $expense->title }}</div>
                            @if($expense->reference_no)
                            <div class="text-[10px] text-slate-400 font-mono">Ref: {{ $expense->reference_no }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                {{ $expense->category->name ?? 'General' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-slate-600 dark:text-slate-400">
                            {{ $expense->account->name ?? 'Cash' }}
                        </td>
                        <td class="px-4 py-3.5 font-bold code-text text-rose-500 code-font">
                            ৳{{ number_format($expense->amount, 2) }}
                        </td>
                        <td class="px-4 py-3.5 text-xs font-mono text-slate-400">
                            {{ $expense->expense_date->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3.5 text-xs text-slate-500">
                            {{ $expense->createdBy->name ?? 'Admin' }}
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Delete this expense?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-rose-500 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs">
                            No expenses recorded for this filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $expenses->links() }}
        </div>
    </div>

    <!-- MODAL 1: RECORD EXPENSE -->
    <div id="addExpenseModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Record Operating Expense</h3>
                <button onclick="document.getElementById('addExpenseModal').style.display = 'none'" class="text-slate-400">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.expenses.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Expense Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Courier poly flyers (500 pcs)" class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Category *</label>
                        <select name="expense_category_id" required class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Pay From Account *</label>
                        <select name="account_id" required class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                            @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} (৳{{ number_format($acc->current_balance, 2) }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Amount (৳) *</label>
                        <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full px-3 py-2 rounded-xl border text-xs font-bold text-rose-500 code-font bg-white dark:bg-slate-800 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Expense Date *</label>
                        <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Voucher / Receipt Ref</label>
                    <input type="text" name="reference_no" placeholder="INV-98123" class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Note (Optional)</label>
                    <textarea name="note" rows="2" placeholder="Expense description..." class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('addExpenseModal').style.display = 'none'" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 text-white font-bold text-xs shadow-md">Record & Deduct</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: ADD CATEGORY -->
    <div id="addCatModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">New Expense Category</h3>
                <button onclick="document.getElementById('addCatModal').style.display = 'none'" class="text-slate-400">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.expenses.category.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Category Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Meta Ads Campaign" class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Category Code</label>
                    <input type="text" name="code" placeholder="MKT" class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('addCatModal').style.display = 'none'" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-slate-800 text-white font-bold text-xs">Create Category</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

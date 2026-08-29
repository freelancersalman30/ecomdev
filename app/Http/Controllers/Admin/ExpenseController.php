<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->get('category_id');
        $timeFilter = $request->get('time_filter', 'all');

        $query = Expense::with(['account', 'category', 'createdBy'])->latest('expense_date');

        if ($categoryId) {
            $query->where('expense_category_id', $categoryId);
        }

        if ($timeFilter === 'today') {
            $query->whereDate('expense_date', Carbon::today());
        } elseif ($timeFilter === 'this_month') {
            $query->whereMonth('expense_date', Carbon::now()->month)->whereYear('expense_date', Carbon::now()->year);
        } elseif ($timeFilter === 'this_year') {
            $query->whereYear('expense_date', Carbon::now()->year);
        }

        $expenses = $query->paginate(20)->withQueryString();
        $categories = ExpenseCategory::where('is_active', true)->get();
        $accounts = Account::where('is_active', true)->get();

        // 4 Localized Summary Metrics
        $availableFunds = Account::where('is_active', true)->sum('current_balance');
        $thisYearExpense = Expense::whereYear('expense_date', Carbon::now()->year)->sum('amount');
        $thisMonthExpense = Expense::whereMonth('expense_date', Carbon::now()->month)->whereYear('expense_date', Carbon::now()->year)->sum('amount');
        $todayExpense = Expense::whereDate('expense_date', Carbon::today())->sum('amount');

        $currentYear = Carbon::now()->year;
        $currentMonthName = Carbon::now()->format('F');

        return view('admin.expenses.index', compact(
            'expenses', 'categories', 'accounts',
            'availableFunds', 'thisYearExpense', 'thisMonthExpense', 'todayExpense',
            'currentYear', 'currentMonthName', 'categoryId', 'timeFilter'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
        ]);

        DB::transaction(function () use ($request) {
            $account = Account::lockForUpdate()->find($request->account_id);
            $amount = (float)$request->amount;

            $expense = Expense::create([
                'account_id' => $request->account_id,
                'expense_category_id' => $request->expense_category_id,
                'title' => $request->title,
                'amount' => $amount,
                'expense_date' => $request->expense_date,
                'reference_no' => $request->reference_no,
                'receipt_image' => $request->receipt_image,
                'note' => $request->note,
                'created_by' => auth()->id(),
            ]);

            // Deduct from account balance
            $newBal = $account->current_balance - $amount;
            $account->current_balance = $newBal;
            $account->save();

            // Record transaction
            $account->transactions()->create([
                'type' => 'debit',
                'amount' => $amount,
                'balance_after' => $newBal,
                'source_type' => 'expense',
                'source_id' => $expense->id,
                'reference_no' => $request->reference_no,
                'note' => 'Expense: ' . $request->title,
                'transaction_date' => $request->expense_date,
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->back()->with('success', 'Expense recorded successfully!');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        ExpenseCategory::create([
            'name' => $request->name,
            'code' => $request->code,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Expense category created successfully!');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->back()->with('success', 'Expense deleted.');
    }
}

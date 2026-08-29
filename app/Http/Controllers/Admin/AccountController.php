<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::with('transactions')->get();
        $totalBalance = Account::where('is_active', true)->sum('current_balance');

        $thisMonthIn = Transaction::where('type', 'credit')
            ->whereMonth('transaction_date', Carbon::now()->month)
            ->whereYear('transaction_date', Carbon::now()->year)
            ->sum('amount');

        $thisMonthOut = Transaction::where('type', 'debit')
            ->whereMonth('transaction_date', Carbon::now()->month)
            ->whereYear('transaction_date', Carbon::now()->year)
            ->sum('amount');

        $thisYearIn = Transaction::where('type', 'credit')
            ->whereYear('transaction_date', Carbon::now()->year)
            ->sum('amount');

        $thisYearOut = Transaction::where('type', 'debit')
            ->whereYear('transaction_date', Carbon::now()->year)
            ->sum('amount');

        $recentTransactions = Transaction::with('account')->latest('transaction_date')->take(20)->get();

        return view('admin.accounts.index', compact(
            'accounts', 'totalBalance', 'thisMonthIn', 'thisMonthOut',
            'thisYearIn', 'thisYearOut', 'recentTransactions'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'account_type' => 'required|in:cash,bank,mobile_banking',
            'opening_balance' => 'nullable|numeric|min:0',
        ]);

        $openingBal = (float)($request->opening_balance ?? 0);

        $account = Account::create([
            'name' => $request->name,
            'account_type' => $request->account_type,
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'branch_name' => $request->branch_name,
            'opening_balance' => $openingBal,
            'current_balance' => $openingBal,
            'is_active' => true,
        ]);

        if ($openingBal > 0) {
            $account->transactions()->create([
                'type' => 'credit',
                'amount' => $openingBal,
                'balance_after' => $openingBal,
                'source_type' => 'opening_balance',
                'note' => 'Initial opening balance',
                'transaction_date' => Carbon::today(),
                'created_by' => auth()->id(),
            ]);
        }

        return redirect()->back()->with('success', 'Account created successfully!');
    }

    public function deposit(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
        ]);

        DB::transaction(function () use ($request) {
            $account = Account::lockForUpdate()->find($request->account_id);
            $newBalance = $account->current_balance + $request->amount;
            $account->current_balance = $newBalance;
            $account->save();

            $account->transactions()->create([
                'type' => 'credit',
                'amount' => $request->amount,
                'balance_after' => $newBalance,
                'source_type' => 'manual_deposit',
                'note' => $request->note ?? 'Manual fund deposit',
                'reference_no' => $request->reference_no,
                'transaction_date' => $request->transaction_date,
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->back()->with('success', 'Deposit recorded successfully!');
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'from_account_id' => 'required|exists:accounts,id|different:to_account_id',
            'to_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
        ]);

        DB::transaction(function () use ($request) {
            $from = Account::lockForUpdate()->find($request->from_account_id);
            $to = Account::lockForUpdate()->find($request->to_account_id);

            if ($from->current_balance < $request->amount) {
                throw new \Exception('Insufficient funds in source account.');
            }

            // Deduct
            $fromBal = $from->current_balance - $request->amount;
            $from->current_balance = $fromBal;
            $from->save();

            $from->transactions()->create([
                'type' => 'debit',
                'amount' => $request->amount,
                'balance_after' => $fromBal,
                'source_type' => 'fund_transfer',
                'note' => "Transfer to {$to->name}",
                'transaction_date' => $request->transaction_date,
                'created_by' => auth()->id(),
            ]);

            // Add
            $toBal = $to->current_balance + $request->amount;
            $to->current_balance = $toBal;
            $to->save();

            $to->transactions()->create([
                'type' => 'credit',
                'amount' => $request->amount,
                'balance_after' => $toBal,
                'source_type' => 'fund_transfer',
                'note' => "Transfer from {$from->name}",
                'transaction_date' => $request->transaction_date,
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->back()->with('success', 'Fund transfer completed successfully!');
    }
}

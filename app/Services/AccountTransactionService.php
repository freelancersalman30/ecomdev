<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Order;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccountTransactionService
{
    /**
     * Resolve the most appropriate account for an order based on preferred ID,
     * existing association, or payment method.
     */
    public function resolveAccountForOrder(Order $order, ?int $preferredAccountId = null): Account
    {
        if ($preferredAccountId) {
            $account = Account::where('is_active', true)->find($preferredAccountId);
            if ($account) {
                return $account;
            }
        }

        if ($order->account_id) {
            $account = Account::where('is_active', true)->find($order->account_id);
            if ($account) {
                return $account;
            }
        }

        $method = strtolower((string) ($order->payment_method ?? ''));

        // Match based on payment method keywords
        if (str_contains($method, 'bkash')) {
            $bkashAccount = Account::where('is_active', true)
                ->where(function ($q) {
                    $q->where('name', 'like', '%bkash%')
                        ->orWhere('bank_name', 'like', '%bkash%');
                })
                ->first();
            if ($bkashAccount) {
                return $bkashAccount;
            }
        }

        if (str_contains($method, 'nagad')) {
            $nagadAccount = Account::where('is_active', true)
                ->where(function ($q) {
                    $q->where('name', 'like', '%nagad%')
                        ->orWhere('bank_name', 'like', '%nagad%');
                })
                ->first();
            if ($nagadAccount) {
                return $nagadAccount;
            }
        }

        if (str_contains($method, 'card') || str_contains($method, 'bank') || str_contains($method, 'sslcommerz')) {
            $bankAccount = Account::where('is_active', true)
                ->where('account_type', 'bank')
                ->first();
            if ($bankAccount) {
                return $bankAccount;
            }
        }

        // Default or Cash in counter
        $defaultAccount = Account::where('is_active', true)
            ->where('is_default', true)
            ->first();

        if ($defaultAccount) {
            return $defaultAccount;
        }

        $cashAccount = Account::where('is_active', true)
            ->where('account_type', 'cash')
            ->first();

        if ($cashAccount) {
            return $cashAccount;
        }

        return Account::where('is_active', true)->firstOrFail();
    }

    /**
     * Credit a sale / order payment to an account and record the transaction ledger.
     */
    public function creditOrderPayment(
        Order $order,
        float $amount,
        ?int $preferredAccountId = null,
        ?string $note = null,
        ?string $referenceNo = null
    ): ?Transaction {
        if ($amount <= 0.0001) {
            return null;
        }

        return DB::transaction(function () use ($order, $amount, $preferredAccountId, $note, $referenceNo) {
            $resolvedAccount = $this->resolveAccountForOrder($order, $preferredAccountId);
            $account = Account::lockForUpdate()->find($resolvedAccount->id);

            if (! $account) {
                throw new \RuntimeException("Unable to locate active account for Order #{$order->order_no}");
            }

            $newBalance = round((float) $account->current_balance + $amount, 2);
            $account->current_balance = $newBalance;
            $account->save();

            // Ensure order points to this account if not already assigned
            if (! $order->account_id) {
                $order->account_id = $account->id;
                $order->saveQuietly();
            }

            $txDate = $order->delivered_at ? Carbon::parse($order->delivered_at)->toDateString() : Carbon::today()->toDateString();
            $creatorId = auth()->id() ?? $order->processed_by ?? $order->user_id;

            return $account->transactions()->create([
                'type' => 'credit',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'source_type' => 'order',
                'source_id' => $order->id,
                'reference_no' => $referenceNo ?? $order->order_no,
                'note' => $note ?? "Sale payment collected for Order #{$order->order_no}",
                'transaction_date' => $txDate,
                'created_by' => $creatorId,
            ]);
        });
    }

    /**
     * Reverse / refund all credited payments for an order (e.g. on cancellation/return).
     */
    public function reverseOrderPayment(Order $order, ?string $note = null): ?Transaction
    {
        return DB::transaction(function () use ($order, $note) {
            $totalCredited = (float) Transaction::where('source_type', 'order')
                ->where('source_id', $order->id)
                ->where('type', 'credit')
                ->sum('amount');

            $totalDebited = (float) Transaction::where('source_type', 'order')
                ->where('source_id', $order->id)
                ->where('type', 'debit')
                ->sum('amount');

            $netCredited = round($totalCredited - $totalDebited, 2);

            if ($netCredited <= 0.0001) {
                return null;
            }

            $resolvedAccount = $this->resolveAccountForOrder($order, $order->account_id);
            $account = Account::lockForUpdate()->find($resolvedAccount->id);

            if (! $account) {
                throw new \RuntimeException("Account not found to reverse payment for Order #{$order->order_no}");
            }

            $newBalance = max(0.00, round((float) $account->current_balance - $netCredited, 2));
            $account->current_balance = $newBalance;
            $account->save();

            return $account->transactions()->create([
                'type' => 'debit',
                'amount' => $netCredited,
                'balance_after' => $newBalance,
                'source_type' => 'order',
                'source_id' => $order->id,
                'reference_no' => $order->order_no,
                'note' => $note ?? "Sale payment reversed for Order #{$order->order_no}",
                'transaction_date' => Carbon::today()->toDateString(),
                'created_by' => auth()->id() ?? $order->processed_by,
            ]);
        });
    }

    /**
     * Synchronize all existing completed or paid orders that have no transaction ledger entry yet.
     * Returns an array of synced order details.
     */
    public function syncExistingCompletedOrders(): array
    {
        $orders = Order::where(function ($q) {
            $q->where('status', 'completed')
                ->orWhere('payment_status', 'paid')
                ->orWhere('paid_amount', '>', 0);
        })
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'returned')
            ->orderBy('id', 'asc')
            ->get();

        $synced = [];

        foreach ($orders as $order) {
            $hasCreditTx = Transaction::where('source_type', 'order')
                ->where('source_id', $order->id)
                ->where('type', 'credit')
                ->exists();

            if ($hasCreditTx) {
                continue;
            }

            $amountToCredit = 0.00;
            if ($order->status === 'completed') {
                $amountToCredit = (float) $order->grand_total;
                if ((float) $order->paid_amount < $amountToCredit) {
                    $order->paid_amount = $amountToCredit;
                    $order->due_amount = 0.00;
                    $order->payment_status = 'paid';
                    $order->saveQuietly();
                }
            } else {
                $amountToCredit = (float) $order->paid_amount;
            }

            if ($amountToCredit > 0.0001) {
                $tx = $this->creditOrderPayment(
                    $order,
                    $amountToCredit,
                    $order->account_id,
                    "Sync existing sale: Order #{$order->order_no} ({$order->status})"
                );

                $synced[] = [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'amount' => $amountToCredit,
                    'account' => $tx?->account->name,
                ];
            }
        }

        return $synced;
    }
}

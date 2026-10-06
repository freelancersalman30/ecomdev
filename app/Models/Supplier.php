<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company',
        'phone',
        'email',
        'address',
        'opening_balance',
        'total_purchased',
        'total_paid',
        'current_due',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'total_purchased' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'current_due' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function recalculateDue(): void
    {
        $purchased = (float) $this->purchases()->sum('grand_total');
        $paid = (float) $this->payments()->sum('amount');
        $this->total_purchased = $purchased;
        $this->total_paid = $paid;
        $this->current_due = max(0, ((float) ($this->opening_balance ?? 0) + $purchased) - $paid);
        $this->saveQuietly();

        $this->syncPurchasesPaymentStatus();
    }

    public function syncPurchasesPaymentStatus(): void
    {
        $purchases = $this->purchases()->orderBy('purchase_date', 'asc')->orderBy('id', 'asc')->get();
        $allPayments = $this->payments()->orderBy('payment_date', 'asc')->orderBy('id', 'asc')->get();

        $directPaymentsByPurchase = [];
        $unlinkedPaymentTotal = 0.0;

        foreach ($allPayments as $payment) {
            $amt = (float) $payment->amount;
            if ($payment->purchase_id) {
                $directPaymentsByPurchase[$payment->purchase_id] = ($directPaymentsByPurchase[$payment->purchase_id] ?? 0) + $amt;
            } else {
                $unlinkedPaymentTotal += $amt;
            }
        }

        $openingBalance = (float) ($this->opening_balance ?? 0);
        if ($openingBalance > 0) {
            $absorbed = min($openingBalance, $unlinkedPaymentTotal);
            $unlinkedPaymentTotal -= $absorbed;
        }

        foreach ($purchases as $purchase) {
            $grandTotal = (float) $purchase->grand_total;
            $directPaid = (float) ($directPaymentsByPurchase[$purchase->id] ?? 0);

            // If direct payment exceeds grand_total, surplus flows to unlinked pool
            if ($directPaid > $grandTotal) {
                $unlinkedPaymentTotal += ($directPaid - $grandTotal);
                $directPaid = $grandTotal;
            }

            $allocatedPaid = $directPaid;
            $needed = max(0, $grandTotal - $allocatedPaid);

            if ($needed > 0 && $unlinkedPaymentTotal > 0) {
                $fromUnlinked = min($needed, $unlinkedPaymentTotal);
                $allocatedPaid += $fromUnlinked;
                $unlinkedPaymentTotal -= $fromUnlinked;
            }

            $due = max(0, $grandTotal - $allocatedPaid);

            if ($due <= 0.0001) {
                $status = 'paid';
                $due = 0.00;
            } elseif ($allocatedPaid > 0) {
                $status = 'partial';
            } else {
                $status = 'due';
            }

            $purchase->updateQuietly([
                'paid_amount' => $allocatedPaid,
                'due_amount' => $due,
                'payment_status' => $status,
            ]);
        }
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Warranty extends Model
{
    use HasFactory;

    protected $table = 'product_warranties';

    protected $fillable = [
        'warranty_code',
        'serial_number',
        'order_id',
        'order_item_id',
        'customer_id',
        'product_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'warranty_period',
        'warranty_days',
        'start_date',
        'end_date',
        'status',
        'claim_notes',
        'admin_notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'warranty_days' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate exact remaining days for this warranty
     */
    public function getRemainingDaysAttribute(): int
    {
        if ($this->status === 'voided') {
            return 0;
        }

        $end = Carbon::parse($this->end_date)->startOfDay();
        $today = Carbon::now()->startOfDay();

        if ($today->greaterThan($end)) {
            return 0;
        }

        return max(0, (int) $today->diffInDays($end, false));
    }

    /**
     * Check if warranty is currently valid & active
     */
    public function getIsValidAttribute(): bool
    {
        return $this->status === 'active' && $this->remaining_days > 0;
    }

    /**
     * Check if warranty is expired
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->status === 'expired' || ($this->status === 'active' && $this->remaining_days <= 0);
    }

    /**
     * Percentage of warranty duration that has elapsed
     */
    public function getElapsedPercentageAttribute(): int
    {
        $start = Carbon::parse($this->start_date)->startOfDay();
        $totalDays = max(1, $this->warranty_days);
        $elapsedDays = max(0, Carbon::now()->diffInDays($start));

        return (int) min(100, max(0, round(($elapsedDays / $totalDays) * 100)));
    }

    /**
     * Remaining percentage
     */
    public function getRemainingPercentageAttribute(): int
    {
        return max(0, 100 - $this->elapsed_percentage);
    }

    /**
     * Status label and badge styling
     */
    public function getStatusBadgeAttribute(): array
    {
        if ($this->status === 'voided') {
            return [
                'label' => 'Voided',
                'badge_class' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400 border border-slate-300',
                'message' => 'Warranty has been voided',
            ];
        }

        if ($this->status === 'claimed') {
            return [
                'label' => 'Claimed / Replaced',
                'badge_class' => 'bg-purple-100 text-purple-800 dark:bg-purple-950/50 dark:text-purple-300 border border-purple-300',
                'message' => 'Warranty claim serviced',
            ];
        }

        $remaining = $this->remaining_days;

        if ($remaining <= 0) {
            return [
                'label' => 'Expired',
                'badge_class' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-300',
                'message' => 'Warranty expired (0 days remaining)',
            ];
        }

        if ($remaining <= 30) {
            return [
                'label' => "{$remaining} Days Left (Expiring Soon)",
                'badge_class' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-300',
                'message' => "Expiring soon in {$remaining} days",
            ];
        }

        return [
            'label' => "{$remaining} Days Remaining",
            'badge_class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-300',
            'message' => "{$remaining} days remaining",
        ];
    }

    /**
     * Parse free-form warranty text into exact number of days
     */
    public static function parseDurationDays(?string $warrantyText): int
    {
        if (empty($warrantyText)) {
            return 365;
        }

        $text = strtolower(trim($warrantyText));

        if (preg_match('/(\d+)\s*(?:year|yr)/i', $text, $matches)) {
            return (int) $matches[1] * 365;
        }

        if (preg_match('/(\d+)\s*(?:month|mo)/i', $text, $matches)) {
            return (int) $matches[1] * 30;
        }

        if (preg_match('/(\d+)\s*(?:day|d)/i', $text, $matches)) {
            return (int) $matches[1];
        }

        if (str_contains($text, 'year') || str_contains($text, 'official')) {
            return 365;
        }

        if (str_contains($text, '6 month')) {
            return 180;
        }

        if (str_contains($text, '3 month')) {
            return 90;
        }

        return 365;
    }

    /**
     * Generate unique warranty code
     */
    public static function generateCode(): string
    {
        do {
            $code = 'WAR-'.date('Ym').'-'.strtoupper(Str::random(5));
        } while (self::where('warranty_code', $code)->exists());

        return $code;
    }
}

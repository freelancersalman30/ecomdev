<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Deduct stock when an order is placed or processed.
     */
    public function deductStock(int $productId, ?int $variantId, int $quantity): bool
    {
        return DB::transaction(function () use ($productId, $variantId, $quantity) {
            $product = Product::lockForUpdate()->find($productId);
            if (! $product) {
                return false;
            }

            if ($variantId) {
                $variant = ProductVariant::lockForUpdate()->find($variantId);
                if (! $variant || $variant->stock_quantity < $quantity) {
                    return false;
                }
                $variant->decrement('stock_quantity', $quantity);
            } else {
                if ($product->stock_quantity < $quantity) {
                    return false;
                }
                $product->decrement('stock_quantity', $quantity);
            }

            $product->increment('sales_count', $quantity);

            return true;
        });
    }

    /**
     * Restore stock when an order is cancelled or returned.
     */
    public function restoreStock(int $productId, ?int $variantId, int $quantity): void
    {
        DB::transaction(function () use ($productId, $variantId, $quantity) {
            $product = Product::lockForUpdate()->find($productId);
            if (! $product) {
                return;
            }

            if ($variantId) {
                $variant = ProductVariant::lockForUpdate()->find($variantId);
                if ($variant) {
                    $variant->increment('stock_quantity', $quantity);
                }
            } else {
                $product->increment('stock_quantity', $quantity);
            }

            $product->decrement('sales_count', min($product->sales_count, $quantity));
        });
    }

    /**
     * Increase stock from a Purchase Order intake.
     */
    public function addStockFromPurchase(int $productId, ?int $variantId, int $quantity, float $costPrice): void
    {
        DB::transaction(function () use ($productId, $variantId, $quantity, $costPrice) {
            $product = Product::lockForUpdate()->find($productId);
            if (! $product) {
                return;
            }

            if ($variantId) {
                $variant = ProductVariant::lockForUpdate()->find($variantId);
                if ($variant) {
                    $variant->increment('stock_quantity', $quantity);
                    $variant->purchase_price = $costPrice;
                    $variant->save();
                }
            } else {
                $product->increment('stock_quantity', $quantity);
                $product->purchase_price = $costPrice;
                $product->save();
            }
        });
    }

    /**
     * Get live stock valuation (Cost value & Retail value)
     */
    public function getStockValuation(): array
    {
        $simpleProducts = Product::where('has_variants', false)->get();
        $variants = ProductVariant::where('is_active', true)->get();

        $totalCostValue = 0;
        $totalRetailValue = 0;
        $totalItemsCount = 0;

        foreach ($simpleProducts as $prod) {
            $totalCostValue += ($prod->purchase_price * $prod->stock_quantity);
            $totalRetailValue += ($prod->selling_price * $prod->stock_quantity);
            $totalItemsCount += $prod->stock_quantity;
        }

        foreach ($variants as $var) {
            $totalCostValue += ($var->purchase_price * $var->stock_quantity);
            $totalRetailValue += ($var->selling_price * $var->stock_quantity);
            $totalItemsCount += $var->stock_quantity;
        }

        return [
            'total_cost_value' => round($totalCostValue, 2),
            'total_retail_value' => round($totalRetailValue, 2),
            'potential_profit' => round($totalRetailValue - $totalCostValue, 2),
            'total_items_in_stock' => $totalItemsCount,
        ];
    }
}

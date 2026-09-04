<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiSetting;
use App\Models\DeliveryMethod;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DeliveryController extends Controller
{
    /**
     * Display Delivery & Delivery Charge Management Dashboard.
     */
    public function index()
    {
        $deliveryMethods = collect();
        if (Schema::hasTable('delivery_methods')) {
            try {
                $deliveryMethods = DeliveryMethod::orderBy('sort_order')->orderBy('id')->get();
            } catch (\Throwable $e) {
                $deliveryMethods = collect();
            }
        }

        // Summary Statistics
        $totalZones = $deliveryMethods->count();
        $activeZones = $deliveryMethods->where('is_active', true)->count();
        $minCharge = $deliveryMethods->where('is_active', true)->min('charge') ?? 0;
        $maxCharge = $deliveryMethods->where('is_active', true)->max('charge') ?? 0;
        $freeDeliveryRuleCount = $deliveryMethods->whereNotNull('min_order_for_free_delivery')->count();

        // Global Shipping & Logistics Settings
        $globalSettings = Setting::whereIn('key', [
            'free_shipping_threshold',
            'default_courier_partner',
            'delivery_information_notice',
            'inside_dhaka_charge',
            'outside_dhaka_charge',
            'inside_dhaka_estimate',
            'outside_dhaka_estimate',
            'footer_courier_partners',
        ])->pluck('value', 'key')->toArray();

        // Third-Party Couriers
        $courierIntegrations = ApiSetting::where('type', 'courier')->get();

        return view('admin.delivery.index', compact(
            'deliveryMethods',
            'totalZones',
            'activeZones',
            'minCharge',
            'maxCharge',
            'freeDeliveryRuleCount',
            'globalSettings',
            'courierIntegrations'
        ));
    }

    /**
     * Store a newly created delivery method / zone in storage.
     */
    public function store(Request $request)
    {
        if (! Schema::hasTable('delivery_methods')) {
            return redirect()->back()->with('error', "Database table 'delivery_methods' not found. Please run 'php artisan migrate'.");
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:delivery_methods,code',
            'charge' => 'required|numeric|min:0',
            'estimated_days' => 'required|string|max:100',
            'min_order_for_free_delivery' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ]);

        $code = ! empty($request->code)
            ? Str::slug($request->code, '_')
            : Str::slug($request->name, '_');

        // Ensure unique code
        $originalCode = $code;
        $counter = 1;
        while (DeliveryMethod::where('code', $code)->exists()) {
            $code = "{$originalCode}_{$counter}";
            $counter++;
        }

        $isDefault = $request->boolean('is_default');
        if ($isDefault) {
            DeliveryMethod::where('is_default', true)->update(['is_default' => false]);
        }

        DeliveryMethod::create([
            'name' => $request->name,
            'code' => $code,
            'charge' => $request->charge,
            'estimated_days' => $request->estimated_days,
            'min_order_for_free_delivery' => $request->filled('min_order_for_free_delivery') ? $request->min_order_for_free_delivery : null,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $isDefault,
        ]);

        $this->syncLegacySettings();

        return redirect()->back()->with('success', "Delivery Zone '{$request->name}' created successfully!");
    }

    /**
     * Update the specified delivery method / zone in storage.
     */
    public function update(Request $request, DeliveryMethod $deliveryMethod)
    {
        if (! Schema::hasTable('delivery_methods')) {
            return redirect()->back()->with('error', "Database table 'delivery_methods' not found. Please run 'php artisan migrate'.");
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:delivery_methods,code,'.$deliveryMethod->id,
            'charge' => 'required|numeric|min:0',
            'estimated_days' => 'required|string|max:100',
            'min_order_for_free_delivery' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ]);

        $isDefault = $request->boolean('is_default');
        if ($isDefault) {
            DeliveryMethod::where('id', '!=', $deliveryMethod->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $deliveryMethod->update([
            'name' => $request->name,
            'code' => Str::slug($request->code, '_'),
            'charge' => $request->charge,
            'estimated_days' => $request->estimated_days,
            'min_order_for_free_delivery' => $request->filled('min_order_for_free_delivery') ? $request->min_order_for_free_delivery : null,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $isDefault,
        ]);

        $this->syncLegacySettings();

        return redirect()->back()->with('success', "Delivery Zone '{$deliveryMethod->name}' updated successfully!");
    }

    /**
     * Toggle active/inactive status of a delivery method.
     */
    public function toggleStatus(DeliveryMethod $deliveryMethod)
    {
        $deliveryMethod->is_active = ! $deliveryMethod->is_active;
        $deliveryMethod->save();

        $this->syncLegacySettings();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $deliveryMethod->is_active,
                'message' => "Delivery zone '{$deliveryMethod->name}' status updated.",
            ]);
        }

        return redirect()->back()->with('success', "Delivery Zone '{$deliveryMethod->name}' is now ".($deliveryMethod->is_active ? 'Active' : 'Inactive').'.');
    }

    /**
     * Mark a delivery method as default.
     */
    public function setDefault(DeliveryMethod $deliveryMethod)
    {
        DeliveryMethod::where('is_default', true)->update(['is_default' => false]);

        $deliveryMethod->is_default = true;
        $deliveryMethod->is_active = true;
        $deliveryMethod->save();

        $this->syncLegacySettings();

        return redirect()->back()->with('success', "'{$deliveryMethod->name}' is now set as the default delivery method.");
    }

    /**
     * Remove the specified delivery method from storage.
     */
    public function destroy(DeliveryMethod $deliveryMethod)
    {
        if (DeliveryMethod::count() <= 1) {
            return redirect()->back()->with('error', 'Cannot delete the only remaining delivery method. You must keep at least one active delivery zone.');
        }

        $name = $deliveryMethod->name;
        $wasDefault = $deliveryMethod->is_default;

        $deliveryMethod->delete();

        // If default was deleted, assign first available as default
        if ($wasDefault) {
            $newDefault = DeliveryMethod::where('is_active', true)->first() ?? DeliveryMethod::first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        $this->syncLegacySettings();

        return redirect()->back()->with('success', "Delivery Zone '{$name}' deleted successfully!");
    }

    /**
     * Update global delivery rules and settings.
     */
    public function updateGlobalRules(Request $request)
    {
        $request->validate([
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'default_courier_partner' => 'nullable|string|max:100',
            'delivery_information_notice' => 'nullable|string|max:1000',
            'footer_courier_partners' => 'nullable|string|max:255',
        ]);

        Setting::set('free_shipping_threshold', $request->free_shipping_threshold);
        Setting::set('default_courier_partner', $request->default_courier_partner ?? 'steadfast');
        Setting::set('delivery_information_notice', $request->delivery_information_notice);
        Setting::set('footer_courier_partners', $request->footer_courier_partners ?? 'Steadfast • Pathao Courier • RedX');

        return redirect()->back()->with('success', 'Global delivery rules & courier configurations saved!');
    }

    /**
     * Helper to keep legacy setting keys in sync with active delivery methods for backward compatibility.
     */
    private function syncLegacySettings(): void
    {
        $insideDhaka = DeliveryMethod::where('code', 'inside_dhaka')->first();
        if ($insideDhaka) {
            Setting::set('inside_dhaka_charge', (string) $insideDhaka->charge);
            Setting::set('inside_dhaka_estimate', $insideDhaka->estimated_days);
        }

        $outsideDhaka = DeliveryMethod::where('code', 'outside_dhaka')->first();
        if ($outsideDhaka) {
            Setting::set('outside_dhaka_charge', (string) $outsideDhaka->charge);
            Setting::set('outside_dhaka_estimate', $outsideDhaka->estimated_days);
        }
    }
}

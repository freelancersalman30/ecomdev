<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductLayoutService;
use Illuminate\Http\Request;

class ProductLayoutController extends Controller
{
    /**
     * Show the Product Layout & Display Customizer interface.
     */
    public function index()
    {
        $config = ProductLayoutService::getConfig();

        // Sample product for live preview
        $sampleProduct = Product::where('is_active', true)->whereNotNull('discount_price')->first()
            ?? Product::where('is_active', true)->first();

        return view('admin.products.layout', compact('config', 'sampleProduct'));
    }

    /**
     * Save updated product layout options.
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_card_style' => 'required|in:modern_daraz,compact_tech,minimalist_bordered',
            'home_flash_sale_layout' => 'required|in:carousel,grid',
            'home_category_layout' => 'required|in:carousel,grid',
            'product_related_layout' => 'required|in:carousel,grid',
            'shop_grid_columns' => 'required|in:3_cols,4_cols,5_cols,6_cols',
            'carousel_interval' => 'required|numeric|min:1500|max:10000',
        ]);

        ProductLayoutService::saveConfig($request->all());

        return redirect()->route('admin.products.layout')->with('success', 'Product display and layout settings updated successfully!');
    }

    /**
     * Restore all layout settings to factory defaults.
     */
    public function reset()
    {
        ProductLayoutService::resetDefaults();

        return redirect()->route('admin.products.layout')->with('success', 'Product layout settings restored to defaults!');
    }
}

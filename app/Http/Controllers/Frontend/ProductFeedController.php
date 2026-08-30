<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Response;

class ProductFeedController extends Controller
{
    /**
     * Generate Facebook / Meta Commerce Catalog XML Feed (RSS 2.0 format).
     */
    public function facebookCatalog(): Response
    {
        $siteName = Setting::get('site_name', 'DREAMERS PCB');
        $siteTagline = Setting::get('site_tagline', 'Enterprise Electronics & PCB Marketplace');
        $currency = Setting::get('currency_code', 'BDT');

        $products = Product::where('is_active', true)->with(['category', 'brand'])->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">'."\n";
        $xml .= '<channel>'."\n";
        $xml .= '  <title>'.htmlspecialchars($siteName).' - Meta Product Catalog</title>'."\n";
        $xml .= '  <link>'.url('/').'</link>'."\n";
        $xml .= '  <description>'.htmlspecialchars($siteTagline).'</description>'."\n";

        foreach ($products as $product) {
            $price = $product->discount_price ?? $product->selling_price;
            $imageUrl = $product->thumbnail_image ? url($product->thumbnail_image) : url('/favicon.ico');
            $productUrl = route('product.show', $product->slug);
            $availability = $product->stock_quantity > 0 ? 'in stock' : 'out of stock';
            $brandName = $product->brand ? $product->brand->name : $siteName;
            $categoryName = $product->category ? $product->category->name : 'Electronics';

            $xml .= '  <item>'."\n";
            $xml .= '    <g:id>'.$product->id.'</g:id>'."\n";
            $xml .= '    <g:title><![CDATA['.$product->name.']]></g:title>'."\n";
            $xml .= '    <g:description><![CDATA['.strip_tags($product->short_description ?: $product->description ?: $product->name).']]></g:description>'."\n";
            $xml .= '    <g:link>'.htmlspecialchars($productUrl).'</g:link>'."\n";
            $xml .= '    <g:image_link>'.htmlspecialchars($imageUrl).'</g:image_link>'."\n";
            $xml .= '    <g:brand><![CDATA['.$brandName.']]></g:brand>'."\n";
            $xml .= '    <g:condition>new</g:condition>'."\n";
            $xml .= '    <g:availability>'.$availability.'</g:availability>'."\n";
            $xml .= '    <g:price>'.number_format($price, 2, '.', '').' '.$currency.'</g:price>'."\n";
            $xml .= '    <g:product_type><![CDATA['.$categoryName.']]></g:product_type>'."\n";
            $xml .= '  </item>'."\n";
        }

        $xml .= '</channel>'."\n";
        $xml .= '</rss>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Generate Google Merchant Center Product XML Feed.
     */
    public function googleMerchant(): Response
    {
        return $this->facebookCatalog(); // Meta and Google Merchant standard feeds share the identical Google Base schema
    }
}

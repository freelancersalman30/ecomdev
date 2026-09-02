<?php

namespace App\Services;

use App\Models\Setting;

class ProductLayoutService
{
    /**
     * Default product layout configurations.
     */
    public static function getDefaults(): array
    {
        return [
            'product_card_style' => 'modern_daraz', // modern_daraz, compact_tech, minimalist_bordered
            'home_flash_sale_layout' => 'carousel', // carousel, grid
            'home_category_layout' => 'carousel', // carousel, grid
            'product_related_layout' => 'carousel', // carousel, grid
            'shop_grid_columns' => '4_cols', // 3_cols, 4_cols, 5_cols, 6_cols
            'carousel_autoplay' => '1', // 1, 0
            'carousel_interval' => '3200', // ms
            'carousel_pause_hover' => '1', // 1, 0
            'show_discount_badge' => '1', // 1, 0
            'show_old_price' => '1', // 1, 0
            'show_quick_add' => '1', // 1, 0
            'show_tech_specs' => '1', // 1, 0
            'show_ratings' => '1', // 1, 0
        ];
    }

    /**
     * Retrieve all product layout configurations merged with defaults.
     */
    public static function getConfig(): array
    {
        $defaults = self::getDefaults();
        $config = [];

        foreach ($defaults as $key => $defaultValue) {
            $config[$key] = Setting::get($key, $defaultValue);
        }

        return $config;
    }

    /**
     * Save product layout settings.
     */
    public static function saveConfig(array $data): void
    {
        $defaults = self::getDefaults();

        foreach ($defaults as $key => $defaultValue) {
            if (array_key_exists($key, $data)) {
                Setting::set($key, (string) $data[$key], 'product_layout');
            } elseif (in_array($key, ['carousel_autoplay', 'carousel_pause_hover', 'show_discount_badge', 'show_old_price', 'show_quick_add', 'show_tech_specs', 'show_ratings'])) {
                // Checkboxes that were unchecked in HTML form
                Setting::set($key, '0', 'product_layout');
            }
        }
    }

    /**
     * Reset all product layout settings to factory defaults.
     */
    public static function resetDefaults(): void
    {
        $defaults = self::getDefaults();
        foreach ($defaults as $key => $value) {
            Setting::set($key, $value, 'product_layout');
        }
    }
}

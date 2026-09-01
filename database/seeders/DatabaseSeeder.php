<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\ApiSetting;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ChildCategory;
use App\Models\Color;
use App\Models\ExpenseCategory;
use App\Models\SeoSetting;
use App\Models\Setting;
use App\Models\Size;
use App\Models\SubCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions (Spatie)
        $permissions = [
            'dashboard.view', 'pos.access', 'orders.manage', 'orders.edit', 'orders.delete',
            'products.manage', 'products.create', 'products.edit', 'products.delete',
            'purchases.manage', 'suppliers.manage', 'coupons.manage', 'landing_pages.manage',
            'fraud.manage', 'sms.marketing', 'accounts.manage', 'expenses.manage',
            'users.manage', 'settings.general', 'settings.apis', 'reports.view', 'system.tools',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->syncPermissions(Permission::all());

        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $managerRole->syncPermissions(['dashboard.view', 'pos.access', 'orders.manage', 'products.manage', 'purchases.manage', 'reports.view']);

        $posRole = Role::firstOrCreate(['name' => 'POS Operator']);
        $posRole->syncPermissions(['pos.access', 'orders.manage']);

        $inventoryRole = Role::firstOrCreate(['name' => 'Inventory Manager']);
        $inventoryRole->syncPermissions(['products.manage', 'purchases.manage', 'suppliers.manage']);

        // 2. Super Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            [
                'name' => 'Dreamers Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('Super Admin');

        // 3. Accounts (Initial 0.00 Balances)
        Account::firstOrCreate(['name' => 'Cash In Counter / Drawer'], [
            'account_type' => 'cash',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'is_default' => true,
            'is_active' => true,
        ]);

        Account::firstOrCreate(['name' => 'Islami Bank PLC'], [
            'account_type' => 'bank',
            'account_number' => '20502130100987',
            'bank_name' => 'Islami Bank Bangladesh',
            'branch_name' => 'Elephant Road, Dhaka',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'is_active' => true,
        ]);

        Account::firstOrCreate(['name' => 'bKash Merchant'], [
            'account_type' => 'mobile_banking',
            'account_number' => '01700112233',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'is_active' => true,
        ]);

        Account::firstOrCreate(['name' => 'Nagad Business'], [
            'account_type' => 'mobile_banking',
            'account_number' => '01800112233',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'is_active' => true,
        ]);

        // 4. Expense Categories
        ExpenseCategory::firstOrCreate(['code' => 'RENT'], ['name' => 'Office & Showroom Rent']);
        ExpenseCategory::firstOrCreate(['code' => 'PKG'], ['name' => 'Courier Packaging & Flyers']);
        ExpenseCategory::firstOrCreate(['code' => 'UTIL'], ['name' => 'Electricity & Internet']);
        ExpenseCategory::firstOrCreate(['code' => 'MKT'], ['name' => 'Meta & Google Ads Campaign']);
        ExpenseCategory::firstOrCreate(['code' => 'TEA'], ['name' => 'Tea, Snacks & Entertainment']);

        // 5. Brands, Colors, Sizes
        Brand::firstOrCreate(['slug' => 'espressif'], ['name' => 'Espressif Systems', 'website' => 'https://www.espressif.com']);
        Brand::firstOrCreate(['slug' => 'arduino'], ['name' => 'Arduino Official', 'website' => 'https://www.arduino.cc']);
        Brand::firstOrCreate(['slug' => 'stmicroelectronics'], ['name' => 'STMicroelectronics', 'website' => 'https://www.st.com']);
        Brand::firstOrCreate(['slug' => 'raspberry-pi'], ['name' => 'Raspberry Pi', 'website' => 'https://www.raspberrypi.com']);
        Brand::firstOrCreate(['slug' => 'quick-soldering'], ['name' => 'Quick Soldering', 'website' => 'http://www.quick-global.com']);

        Color::firstOrCreate(['name' => 'Classic PCB Green'], ['code' => '#15803d']);
        Color::firstOrCreate(['name' => 'Matte Black PCB'], ['code' => '#1e293b']);
        Color::firstOrCreate(['name' => 'Cobalt Blue PCB'], ['code' => '#1d4ed8']);

        Size::firstOrCreate(['code' => 'STD'], ['name' => 'Standard Module']);
        Size::firstOrCreate(['code' => 'DIP28'], ['name' => 'DIP-28 IC']);
        Size::firstOrCreate(['code' => 'SMD'], ['name' => 'SMD / 0805']);

        // 6. Categories (3-Tier Structure)
        $catMcu = Category::firstOrCreate(['slug' => 'microcontrollers-dev-boards'], [
            'name' => 'Microcontrollers & Dev Boards',
            'icon' => 'cpu',
            'description' => 'Arduino, ESP32, STM32, ARM Cortex development boards and kits.',
            'is_featured' => true,
            'display_order' => 1,
        ]);

        $subEsp = SubCategory::firstOrCreate(['category_id' => $catMcu->id, 'slug' => 'esp32-iot-boards'], [
            'name' => 'ESP32 & IoT Boards',
            'display_order' => 1,
        ]);

        $subArd = SubCategory::firstOrCreate(['category_id' => $catMcu->id, 'slug' => 'arduino-series'], [
            'name' => 'Arduino Series',
            'display_order' => 2,
        ]);

        ChildCategory::firstOrCreate(['sub_category_id' => $subEsp->id, 'slug' => 'esp32-cam-ai-vision'], [
            'name' => 'ESP32-CAM AI Vision',
        ]);

        Category::firstOrCreate(['slug' => 'robotics-sensors'], [
            'name' => 'Robotics & Sensors',
            'icon' => 'activity',
            'description' => 'Ultrasonic, LiDAR, Temperature, Gas, IMU, and Vision sensors.',
            'is_featured' => true,
            'display_order' => 2,
        ]);

        Category::firstOrCreate(['slug' => 'soldering-lab-gear'], [
            'name' => 'Soldering & Lab Gear',
            'icon' => 'tool',
            'description' => 'Digital Hot Air Rework Stations, Multimeters, Oscilloscopes, Solder flux.',
            'is_featured' => true,
            'display_order' => 3,
        ]);

        // 7. General Settings
        $settings = [
            'site_name' => 'DREAMERS PCB',
            'site_tagline' => 'Enterprise Gadgets & Electronic Components Platform',
            'site_email' => 'support@dreamerspcb.com',
            'site_phone' => '+880 1700-112233',
            'site_address' => 'Level 5, Multiplan Center, Elephant Road, Dhaka, Bangladesh',
            'currency_symbol' => '৳',
            'currency_code' => 'BDT',
            'inside_dhaka_shipping' => '70',
            'outside_dhaka_shipping' => '130',
            'free_shipping_min_amount' => '3000',
            'facebook_url' => 'https://facebook.com/dreamerspcb',
            'youtube_url' => 'https://youtube.com/@dreamerspcb',
            'maintenance_mode' => '0',
        ];

        foreach ($settings as $k => $v) {
            Setting::set($k, $v, 'general');
        }

        // 8. SEO Settings
        SeoSetting::firstOrCreate([], [
            'meta_title' => 'DREAMERS PCB - Enterprise Gadgets, Arduino, ESP32 & PCB Components in Bangladesh',
            'meta_description' => 'Leading supplier for Microcontrollers, Sensors, Robotics kits, Soldering gear, and Electronic DIY components with fast nationwide courier delivery.',
            'meta_keywords' => 'Arduino Bangladesh, ESP32, Robotics, Soldering Stations, Dhaka PCB, Electronic Components BD',
            'robots_txt' => "User-agent: *\nAllow: /\nDisallow: /admin/\nSitemap: ".url('/sitemap.xml'),
            'sitemap_auto_ping' => true,
            'last_sitemap_generated_at' => Carbon::now(),
        ]);

        // 9. Clean Hero Banners
        Banner::firstOrCreate(['title' => 'Next-Gen IoT Microcontrollers & Dev Boards'], [
            'subtitle' => 'Genuine Espressif ESP32-S3, STM32 Nucleo & Arduino Series In Stock',
            'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=1200&auto=format&fit=crop&q=80',
            'link' => '/shop',
            'placement' => 'hero_slider',
            'display_order' => 1,
            'is_active' => true,
        ]);

        Banner::firstOrCreate(['title' => 'Industrial Lead-Free Soldering & Rework Stations'], [
            'subtitle' => 'Quick, Hakko & T12 Precision Tools for Pro Engineers',
            'image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=1200&auto=format&fit=crop&q=80',
            'link' => '/shop',
            'placement' => 'hero_slider',
            'display_order' => 2,
            'is_active' => true,
        ]);

        // 10. API Settings
        ApiSetting::firstOrCreate(['provider' => 'steadfast'], [
            'type' => 'courier',
            'title' => 'Steadfast Courier API',
            'credentials' => [
                'api_key' => '',
                'secret_key' => '',
            ],
            'is_sandbox' => false,
            'is_active' => false,
        ]);

        ApiSetting::firstOrCreate(['provider' => 'bkash'], [
            'type' => 'payment',
            'title' => 'bKash Merchant Direct Checkout',
            'credentials' => [
                'app_key' => '',
                'app_secret' => '',
                'username' => '',
                'password' => '',
            ],
            'is_sandbox' => true,
            'is_active' => false,
        ]);

        ApiSetting::firstOrCreate(['provider' => 'fb_capi'], [
            'type' => 'tracking',
            'title' => 'Facebook Conversion API (Multi-Pixel)',
            'credentials' => [
                'pixel_id' => '',
                'access_token' => '',
                'test_event_code' => '',
            ],
            'is_sandbox' => false,
            'is_active' => false,
        ]);
    }
}

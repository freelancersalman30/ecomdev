<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\ApiSetting;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ChildCategory;
use App\Models\Color;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FraudCheck;
use App\Models\LandingPage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\SeoSetting;
use App\Models\Setting;
use App\Models\Size;
use App\Models\SmsLog;
use App\Models\SubCategory;
use App\Models\Supplier;
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
            'users.manage', 'settings.general', 'settings.apis', 'reports.view', 'system.tools'
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

        // 2. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            [
                'name' => 'Dreamers Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('Super Admin');

        // 3. Accounts
        $cashAcc = Account::create([
            'name' => 'Cash In Counter / Drawer',
            'account_type' => 'cash',
            'opening_balance' => 25000.00,
            'current_balance' => 84200.00,
            'is_default' => true,
            'is_active' => true,
        ]);

        $bankAcc = Account::create([
            'name' => 'Islami Bank PLC (Account: 20502130100)',
            'account_type' => 'bank',
            'account_number' => '20502130100987',
            'bank_name' => 'Islami Bank Bangladesh',
            'branch_name' => 'Elephant Road, Dhaka',
            'opening_balance' => 150000.00,
            'current_balance' => 312500.00,
            'is_active' => true,
        ]);

        $bkashAcc = Account::create([
            'name' => 'bKash Merchant (01700112233)',
            'account_type' => 'mobile_banking',
            'account_number' => '01700112233',
            'opening_balance' => 10000.00,
            'current_balance' => 45600.00,
            'is_active' => true,
        ]);

        $nagadAcc = Account::create([
            'name' => 'Nagad Business (01800112233)',
            'account_type' => 'mobile_banking',
            'account_number' => '01800112233',
            'opening_balance' => 5000.00,
            'current_balance' => 18900.00,
            'is_active' => true,
        ]);

        // 4. Expense Categories & Expenses
        $rentCat = ExpenseCategory::create(['name' => 'Office & Showroom Rent', 'code' => 'RENT']);
        $courierCat = ExpenseCategory::create(['name' => 'Courier Packaging & Flyers', 'code' => 'PKG']);
        $utilityCat = ExpenseCategory::create(['name' => 'Electricity & Internet', 'code' => 'UTIL']);
        $marketingCat = ExpenseCategory::create(['name' => 'Meta & Google Ads Campaign', 'code' => 'MKT']);
        $teaCat = ExpenseCategory::create(['name' => 'Tea, Snacks & Entertainment', 'code' => 'TEA']);

        Expense::create([
            'account_id' => $cashAcc->id,
            'expense_category_id' => $courierCat->id,
            'title' => 'Steadfast 500pcs Poly Mailers & Bubble Wrap',
            'amount' => 3200.00,
            'expense_date' => Carbon::today(),
            'reference_no' => 'EXP-2026-001',
            'note' => 'Packaging materials for daily online shipments',
            'created_by' => $admin->id,
        ]);

        Expense::create([
            'account_id' => $bankAcc->id,
            'expense_category_id' => $marketingCat->id,
            'title' => 'Facebook Conversion Ads - Flash PCB Sale',
            'amount' => 15000.00,
            'expense_date' => Carbon::now()->subDays(3),
            'reference_no' => 'FB-INV-9812',
            'note' => 'August Mid campaign targeted to engineering universities',
            'created_by' => $admin->id,
        ]);

        Expense::create([
            'account_id' => $cashAcc->id,
            'expense_category_id' => $utilityCat->id,
            'title' => 'Fiber Internet Bill (50 Mbps Dedicated)',
            'amount' => 2000.00,
            'expense_date' => Carbon::now()->startOfMonth(),
            'created_by' => $admin->id,
        ]);

        // 5. Brands, Colors, Sizes
        $brandEsp = Brand::create(['name' => 'Espressif Systems', 'slug' => 'espressif', 'website' => 'https://www.espressif.com']);
        $brandArd = Brand::create(['name' => 'Arduino Official', 'slug' => 'arduino', 'website' => 'https://www.arduino.cc']);
        $brandStm = Brand::create(['name' => 'STMicroelectronics', 'slug' => 'stmicroelectronics', 'website' => 'https://www.st.com']);
        $brandRpi = Brand::create(['name' => 'Raspberry Pi', 'slug' => 'raspberry-pi', 'website' => 'https://www.raspberrypi.com']);
        $brandQuick = Brand::create(['name' => 'Quick Soldering', 'slug' => 'quick-soldering', 'website' => 'http://www.quick-global.com']);

        $colGreen = Color::create(['name' => 'Classic PCB Green', 'code' => '#15803d']);
        $colBlack = Color::create(['name' => 'Matte Black PCB', 'code' => '#1e293b']);
        $colBlue = Color::create(['name' => 'Cobalt Blue PCB', 'code' => '#1d4ed8']);

        $sizeStd = Size::create(['name' => 'Standard Module', 'code' => 'STD']);
        $sizeDIP = Size::create(['name' => 'DIP-28 IC', 'code' => 'DIP28']);
        $sizeSMD = Size::create(['name' => 'SMD / 0805', 'code' => 'SMD']);

        // 6. Categories (3-Tier)
        $catMcu = Category::create([
            'name' => 'Microcontrollers & Dev Boards',
            'slug' => 'microcontrollers-dev-boards',
            'icon' => 'cpu',
            'description' => 'Arduino, ESP32, STM32, ARM Cortex development boards and kits.',
            'is_featured' => true,
            'display_order' => 1,
        ]);

        $subEsp = SubCategory::create([
            'category_id' => $catMcu->id,
            'name' => 'ESP32 & IoT Boards',
            'slug' => 'esp32-iot-boards',
            'display_order' => 1,
        ]);

        $subArd = SubCategory::create([
            'category_id' => $catMcu->id,
            'name' => 'Arduino Series',
            'slug' => 'arduino-series',
            'display_order' => 2,
        ]);

        $childEsp32Cam = ChildCategory::create([
            'sub_category_id' => $subEsp->id,
            'name' => 'ESP32-CAM AI Vision',
            'slug' => 'esp32-cam-ai-vision',
        ]);

        $catSensors = Category::create([
            'name' => 'Robotics & Sensors',
            'slug' => 'robotics-sensors',
            'icon' => 'activity',
            'description' => 'Ultrasonic, LiDAR, Temperature, Gas, IMU, and Vision sensors.',
            'is_featured' => true,
            'display_order' => 2,
        ]);

        $catTools = Category::create([
            'name' => 'Soldering & Lab Gear',
            'slug' => 'soldering-lab-gear',
            'icon' => 'tool',
            'description' => 'Digital Hot Air Rework Stations, Multimeters, Oscilloscopes, Solder flux.',
            'is_featured' => true,
            'display_order' => 3,
        ]);

        // 7. Suppliers
        $supShenzhen = Supplier::create([
            'name' => 'RoboTech Shenzhen Electronics Ltd.',
            'company' => 'Shenzhen RoboTech Industrial Co.',
            'phone' => '+86 755 8321 9901',
            'email' => 'sales@robotech-sz.com',
            'address' => 'Huaqiangbei Commercial Area, Futian District, Shenzhen, China',
            'opening_balance' => 0.00,
            'total_purchased' => 450000.00,
            'total_paid' => 400000.00,
            'current_due' => 50000.00,
            'notes' => 'Primary direct importer for microcontrollers & SMD components',
        ]);

        $supDhaka = Supplier::create([
            'name' => 'Dhaka Electronics & Hardware Hub',
            'company' => 'Dhaka Component Agency',
            'phone' => '01711223344',
            'email' => 'dhaka.components@gmail.com',
            'address' => 'Stadium Market, Nawabpur, Dhaka',
            'opening_balance' => 5000.00,
            'total_purchased' => 120000.00,
            'total_paid' => 110000.00,
            'current_due' => 15000.00,
        ]);

        // 8. Products & Variants (Electronics & PCB items)
        $p1 = Product::create([
            'name' => 'ESP32-WROOM-32D Dual-Core WiFi + Bluetooth Dev Board',
            'slug' => 'esp32-wroom-32d-dual-core-wifi-bluetooth-dev-board',
            'sku' => 'PCB-ESP32-D32',
            'barcode' => '894001001001',
            'category_id' => $catMcu->id,
            'sub_category_id' => $subEsp->id,
            'brand_id' => $brandEsp->id,
            'pcb_model' => 'ESP32 NodeMCU-32S V1.2 (30-Pin)',
            'voltage' => '3.3V DC (Micro USB 5V In)',
            'warranty' => '6 Months Replacement',
            'dimensions' => '51.5 x 28.5 mm',
            'weight' => '12g',
            'chipset' => 'Tensilica Xtensa Dual-Core 32-bit LX6 @ 240MHz',
            'purchase_price' => 320.00,
            'selling_price' => 480.00,
            'discount_price' => 440.00,
            'stock_quantity' => 150,
            'alert_threshold' => 15,
            'has_variants' => false,
            'short_description' => 'Flagship IoT Microcontroller with built-in Wi-Fi (802.11 b/g/n) and Bluetooth 4.2 BR/EDR & BLE.',
            'description' => 'The ESP32 is a low-cost, low-power system on a chip (SoC) microcontroller with integrated Wi-Fi and dual-mode Bluetooth. Perfect for smart home projects, industrial automation, and custom IoT sensor nodes.',
            'thumbnail' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&auto=format&fit=crop&q=80',
            'is_featured' => true,
            'is_flash_sale' => true,
            'is_active' => true,
            'views_count' => 1420,
            'sales_count' => 88,
        ]);

        $p2 = Product::create([
            'name' => 'Arduino Uno R3 SMD Edition with ATmega328P + CH340G',
            'slug' => 'arduino-uno-r3-smd-edition-atmega328p',
            'sku' => 'PCB-ARD-UNO-R3',
            'barcode' => '894001001002',
            'category_id' => $catMcu->id,
            'sub_category_id' => $subArd->id,
            'brand_id' => $brandArd->id,
            'pcb_model' => 'UNO-R3-CH340 Rev3.1',
            'voltage' => '5V DC (DC Barrel Jack 7-12V)',
            'warranty' => '1 Year Official Warranty',
            'dimensions' => '68.6 x 53.4 mm',
            'weight' => '25g',
            'chipset' => 'Microchip ATmega328P 16MHz',
            'purchase_price' => 280.00,
            'selling_price' => 420.00,
            'discount_price' => 380.00,
            'stock_quantity' => 210,
            'alert_threshold' => 20,
            'has_variants' => true,
            'short_description' => 'Standard microcontroller board based on the ATmega328P. Includes 14 digital input/output pins, 6 analog inputs.',
            'thumbnail' => 'https://images.unsplash.com/photo-1553406830-ef2513450d76?w=600&auto=format&fit=crop&q=80',
            'is_featured' => true,
            'is_active' => true,
            'sales_count' => 145,
        ]);

        // Variants for Arduino Uno
        ProductVariant::create([
            'product_id' => $p2->id,
            'color_id' => $colBlue->id,
            'size_id' => $sizeStd->id,
            'variant_name' => 'Blue PCB - Standard Headers',
            'sku' => 'PCB-ARD-UNO-BLU',
            'barcode' => '894001002001',
            'purchase_price' => 280.00,
            'selling_price' => 420.00,
            'discount_price' => 380.00,
            'stock_quantity' => 120,
            'alert_threshold' => 10,
        ]);

        ProductVariant::create([
            'product_id' => $p2->id,
            'color_id' => $colBlack->id,
            'size_id' => $sizeStd->id,
            'variant_name' => 'Matte Black PCB - Gold Plated Headers',
            'sku' => 'PCB-ARD-UNO-BLK',
            'barcode' => '894001002002',
            'purchase_price' => 310.00,
            'selling_price' => 460.00,
            'discount_price' => 420.00,
            'stock_quantity' => 90,
            'alert_threshold' => 10,
        ]);

        $p3 = Product::create([
            'name' => 'Quick 861DW 1000W Lead-Free Digital Hot Air Rework Station',
            'slug' => 'quick-861dw-1000w-digital-hot-air-rework-station',
            'sku' => 'TOOL-QUICK-861DW',
            'barcode' => '894001001003',
            'category_id' => $catTools->id,
            'brand_id' => $brandQuick->id,
            'pcb_model' => '861DW Industrial Series',
            'voltage' => '220V AC 50Hz (1000W Max Power)',
            'warranty' => '2 Years Service Warranty',
            'dimensions' => '188 x 245 x 135 mm',
            'weight' => '4.65 kg',
            'chipset' => 'PID Temperature Control Microcontroller (100°C - 500°C)',
            'purchase_price' => 16500.00,
            'selling_price' => 21500.00,
            'discount_price' => 19800.00,
            'stock_quantity' => 4, // Low stock on purpose!
            'alert_threshold' => 5,
            'has_variants' => false,
            'short_description' => 'Professional repair & rework station for SMD PCB rework with 3 user programmable channels (CH1, CH2, CH3).',
            'thumbnail' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=600&auto=format&fit=crop&q=80',
            'is_featured' => true,
            'is_active' => true,
            'sales_count' => 12,
        ]);

        $p4 = Product::create([
            'name' => 'ESP32-CAM AI-Thinker OV2640 2MP Camera Module with IPEX Antenna',
            'slug' => 'esp32-cam-ai-thinker-ov2640-camera-module',
            'sku' => 'PCB-ESP32-CAM-OV',
            'barcode' => '894001001004',
            'category_id' => $catMcu->id,
            'sub_category_id' => $subEsp->id,
            'child_category_id' => $childEsp32Cam->id,
            'brand_id' => $brandEsp->id,
            'pcb_model' => 'ESP32-CAM V2.0',
            'voltage' => '5V DC In via Pin 5V',
            'warranty' => '6 Months Replacement',
            'chipset' => 'ESP32-S + OV2640 2 Megapixel Sensor + TF Card Slot',
            'purchase_price' => 480.00,
            'selling_price' => 720.00,
            'discount_price' => 650.00,
            'stock_quantity' => 75,
            'alert_threshold' => 10,
            'has_variants' => false,
            'short_description' => 'Compact camera module capable of standalone operation as IoT video streaming server and face recognition.',
            'thumbnail' => 'https://images.unsplash.com/photo-1517420704952-d9f39e95b43e?w=600&auto=format&fit=crop&q=80',
            'is_featured' => true,
            'is_flash_sale' => true,
            'is_active' => true,
            'sales_count' => 64,
        ]);

        // 9. Customers CRM & Fraud records
        $c1 = Customer::create([
            'name' => 'Engr. Tanvir Ahmed',
            'phone' => '01712345678',
            'email' => 'tanvir.robotics@buet.ac.bd',
            'address' => 'Dr. Qudrat-E-Khuda Road, Dhanmondi 32',
            'city' => 'Dhaka',
            'loyalty_points' => 350,
            'total_spent' => 42500.00,
            'total_orders_count' => 8,
            'delivered_orders_count' => 8,
            'delivery_success_rate' => 100.00,
            'is_flagged_fraud' => false,
            'is_active' => true,
        ]);

        $c2 = Customer::create([
            'name' => 'Mehedi Hasan Niloy',
            'phone' => '01898765432',
            'email' => 'niloy.iot@gmail.com',
            'address' => 'Agrabad Commercial Area, GEC Circle',
            'city' => 'Chittagong',
            'loyalty_points' => 120,
            'total_spent' => 8400.00,
            'total_orders_count' => 3,
            'delivered_orders_count' => 3,
            'delivery_success_rate' => 100.00,
            'is_flagged_fraud' => false,
            'is_active' => true,
        ]);

        $c3 = Customer::create([
            'name' => 'Suspicious User (Fake COD Orders)',
            'phone' => '01900998877',
            'email' => 'fakeuser88@tempmail.com',
            'address' => 'Unknown Street, Road 9',
            'city' => 'Gazipur',
            'loyalty_points' => 0,
            'total_spent' => 0.00,
            'total_orders_count' => 4,
            'delivered_orders_count' => 0,
            'cancelled_orders_count' => 3,
            'returned_orders_count' => 1,
            'delivery_success_rate' => 0.00,
            'is_flagged_fraud' => true,
            'fraud_reason' => '100% parcel return/cancellation history in courier network',
            'is_active' => false,
        ]);

        FraudCheck::create([
            'phone' => '01900998877',
            'ip_address' => '103.145.74.12',
            'risk_level' => 'critical',
            'courier_success_rate' => 0.00,
            'total_parcels' => 6,
            'delivered_parcels' => 0,
            'cancelled_parcels' => 6,
            'notes' => 'Refused COD parcel delivery multiple times across Steadfast and RedX',
            'is_blacklisted' => true,
        ]);

        // 10. Sample Orders across 8-stage pipeline
        $o1 = Order::create([
            'order_no' => 'DPCB-20260827-0001',
            'customer_id' => $c1->id,
            'order_type' => 'online',
            'status' => 'completed',
            'subtotal' => 20680.00,
            'discount' => 500.00,
            'coupon_code' => 'DREAMER10',
            'shipping_charge' => 70.00,
            'grand_total' => 20250.00,
            'paid_amount' => 20250.00,
            'due_amount' => 0.00,
            'payment_method' => 'bkash',
            'payment_status' => 'paid',
            'payment_transaction_id' => 'BK89X7712A',
            'account_id' => $bkashAcc->id,
            'shipping_name' => 'Engr. Tanvir Ahmed',
            'shipping_phone' => '01712345678',
            'shipping_email' => 'tanvir.robotics@buet.ac.bd',
            'shipping_address' => 'Dr. Qudrat-E-Khuda Road, Dhanmondi 32',
            'shipping_city' => 'Dhaka',
            'courier_name' => 'Steadfast',
            'courier_tracking_id' => 'S-TK89012390',
            'courier_status' => 'Delivered',
            'delivered_at' => Carbon::now()->subDays(1),
        ]);

        $o1->items()->create([
            'product_id' => $p3->id,
            'product_name' => $p3->name,
            'sku' => $p3->sku,
            'unit_cost' => $p3->purchase_price,
            'unit_price' => 19800.00,
            'quantity' => 1,
            'subtotal' => 19800.00,
        ]);

        $o1->items()->create([
            'product_id' => $p1->id,
            'product_name' => $p1->name,
            'sku' => $p1->sku,
            'unit_cost' => $p1->purchase_price,
            'unit_price' => 440.00,
            'quantity' => 2,
            'subtotal' => 880.00,
        ]);

        $o2 = Order::create([
            'order_no' => 'DPCB-20260827-0002',
            'customer_id' => $c2->id,
            'order_type' => 'online',
            'status' => 'processing',
            'subtotal' => 1950.00,
            'discount' => 0.00,
            'shipping_charge' => 130.00,
            'grand_total' => 2080.00,
            'paid_amount' => 0.00,
            'due_amount' => 2080.00,
            'payment_method' => 'cash_on_delivery',
            'payment_status' => 'pending',
            'shipping_name' => 'Mehedi Hasan Niloy',
            'shipping_phone' => '01898765432',
            'shipping_email' => 'niloy.iot@gmail.com',
            'shipping_address' => 'Agrabad Commercial Area, GEC Circle',
            'shipping_city' => 'Chittagong',
            'courier_name' => 'Pathao',
            'courier_tracking_id' => 'P-984210938',
            'courier_status' => 'Assigned to Rider',
        ]);

        $o2->items()->create([
            'product_id' => $p4->id,
            'product_name' => $p4->name,
            'sku' => $p4->sku,
            'unit_cost' => $p4->purchase_price,
            'unit_price' => 650.00,
            'quantity' => 3,
            'subtotal' => 1950.00,
        ]);

        $o3 = Order::create([
            'order_no' => 'DPCB-20260827-0003',
            'customer_id' => $c3->id,
            'order_type' => 'online',
            'status' => 'pending',
            'subtotal' => 39600.00,
            'discount' => 0.00,
            'shipping_charge' => 130.00,
            'grand_total' => 39730.00,
            'paid_amount' => 0.00,
            'due_amount' => 39730.00,
            'payment_method' => 'cash_on_delivery',
            'payment_status' => 'pending',
            'shipping_name' => 'Suspicious User',
            'shipping_phone' => '01900998877',
            'shipping_address' => 'Unknown Street, Road 9',
            'shipping_city' => 'Gazipur',
            'is_fraud_suspect' => true,
            'fraud_score' => 95,
            'fraud_reason' => 'Customer phone blacklisted. 100% courier return history',
        ]);

        $o3->items()->create([
            'product_id' => $p3->id,
            'product_name' => $p3->name,
            'sku' => $p3->sku,
            'unit_cost' => $p3->purchase_price,
            'unit_price' => 19800.00,
            'quantity' => 2,
            'subtotal' => 39600.00,
        ]);

        // 11. Purchases
        $po1 = Purchase::create([
            'supplier_id' => $supShenzhen->id,
            'purchase_no' => 'PO-20260810-001',
            'supplier_invoice_no' => 'SZ-EXP-8901',
            'purchase_date' => Carbon::now()->subDays(17),
            'subtotal' => 150000.00,
            'discount' => 5000.00,
            'tax' => 0.00,
            'shipping_cost' => 4500.00,
            'grand_total' => 149500.00,
            'paid_amount' => 149500.00,
            'due_amount' => 0.00,
            'payment_status' => 'paid',
            'status' => 'received',
            'notes' => 'Direct sea freight batch import',
            'created_by' => $admin->id,
        ]);

        $po1->items()->create([
            'product_id' => $p1->id,
            'unit_cost' => 320.00,
            'quantity' => 300,
            'subtotal' => 96000.00,
            'batch_no' => 'BAT-ESP32-2026A',
        ]);

        $po1->items()->create([
            'product_id' => $p4->id,
            'unit_cost' => 480.00,
            'quantity' => 100,
            'subtotal' => 48000.00,
            'batch_no' => 'BAT-CAM-2026A',
        ]);

        // 12. Coupons
        Coupon::create([
            'code' => 'DREAMER10',
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'min_order_amount' => 1000.00,
            'max_discount_amount' => 1000.00,
            'usage_limit' => 500,
            'usage_per_user' => 1,
            'times_used' => 42,
            'starts_at' => Carbon::now()->subMonths(1),
            'expires_at' => Carbon::now()->addMonths(6),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'FLASH500',
            'discount_type' => 'fixed',
            'discount_value' => 500.00,
            'min_order_amount' => 5000.00,
            'is_active' => true,
        ]);

        // 13. Landing Pages
        LandingPage::create([
            'title' => 'ESP32-CAM AI Smart Surveillance Node - Flash Sale',
            'slug' => 'esp32-cam-flash-sale',
            'product_id' => $p4->id,
            'headline' => 'Build Your Own AI Face Recognition & WiFi Security Camera in Minutes!',
            'sub_headline' => 'High Definition OV2640 2MP Camera with built-in ESP32 Dual Core Wi-Fi module and TF Card recording support.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'features_list' => [
                'Dual-core 32-bit CPU with clock frequency up to 240MHz',
                'Supports OV2640 and OV7670 cameras with built-in Flash LED',
                'Built-in 520 KB SRAM + external 4M PSRAM',
                'Supports Wi-Fi image upload & TF card local storage',
                'Plug and play with Arduino IDE, ESP-IDF, and Home Assistant',
            ],
            'testimonials' => [
                ['name' => 'Sabbir Hossain', 'comment' => 'Excellent board! I made an AI security bell in 2 hours using this kit.'],
                ['name' => 'Fahim Rahman', 'comment' => 'Original AI-Thinker module. High speed delivery by DREAMERS PCB. Highly recommended!']
            ],
            'theme_color' => '#0ea5e9',
            'fb_pixel_id' => '987654321012345',
            'views_count' => 3840,
            'conversions_count' => 128,
            'is_active' => true,
        ]);

        // 14. SMS Logs
        SmsLog::create([
            'gateway' => 'BulkSMS BD',
            'phone' => '01712345678',
            'message' => 'Dear Engr. Tanvir Ahmed, your order #DPCB-20260827-0001 of TK 20,250 is confirmed! DREAMERS PCB.',
            'character_count' => 98,
            'sms_parts' => 1,
            'status' => 'sent',
            'sent_at' => Carbon::now()->subDays(1),
        ]);

        // 15. General Settings
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

        // 16. SEO Settings
        SeoSetting::create([
            'meta_title' => 'DREAMERS PCB - Enterprise Gadgets, Arduino, ESP32 & PCB Components in Bangladesh',
            'meta_description' => 'Leading supplier for Microcontrollers, Sensors, Robotics kits, Soldering gear, and Electronic DIY components with fast nationwide courier delivery.',
            'meta_keywords' => 'Arduino Bangladesh, ESP32, Robotics, Soldering Stations, Dhaka PCB, Electronic Components BD',
            'robots_txt' => "User-agent: *\nAllow: /\nDisallow: /admin/\nSitemap: " . url('/sitemap.xml'),
            'sitemap_auto_ping' => true,
            'last_sitemap_generated_at' => Carbon::now(),
        ]);

        // 17. Banners
        Banner::create([
            'title' => 'Next-Gen IoT Microcontrollers & Dev Boards',
            'subtitle' => 'Genuine Espressif ESP32-S3, STM32 Nucleo & Arduino Series In Stock',
            'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=1200&auto=format&fit=crop&q=80',
            'link' => '/category/microcontrollers-dev-boards',
            'placement' => 'hero_slider',
            'display_order' => 1,
            'is_active' => true,
        ]);

        Banner::create([
            'title' => 'Industrial Lead-Free Soldering & Rework Stations',
            'subtitle' => 'Quick, Hakko & T12 Precision Tools for Pro Engineers',
            'image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=1200&auto=format&fit=crop&q=80',
            'link' => '/category/soldering-lab-gear',
            'placement' => 'hero_slider',
            'display_order' => 2,
            'is_active' => true,
        ]);

        // 18. API Settings Hub
        ApiSetting::create([
            'provider' => 'steadfast',
            'type' => 'courier',
            'title' => 'Steadfast Courier API',
            'credentials' => [
                'api_key' => 'st_live_98a76b54c321',
                'secret_key' => 'st_sec_45901238910',
            ],
            'is_sandbox' => false,
            'is_active' => true,
        ]);

        ApiSetting::create([
            'provider' => 'bkash',
            'type' => 'payment',
            'title' => 'bKash Merchant Direct Checkout',
            'credentials' => [
                'app_key' => 'bkash_app_key_demo',
                'app_secret' => 'bkash_secret_key_demo',
                'username' => '01700112233',
                'password' => 'pass1234',
            ],
            'is_sandbox' => true,
            'is_active' => true,
        ]);

        ApiSetting::create([
            'provider' => 'fb_capi',
            'type' => 'tracking',
            'title' => 'Facebook Conversion API (Multi-Pixel)',
            'credentials' => [
                'pixel_id' => '987654321012345',
                'access_token' => 'EAAGNOv...mock_token',
                'test_event_code' => 'TEST12345',
            ],
            'is_sandbox' => false,
            'is_active' => true,
        ]);
    }
}

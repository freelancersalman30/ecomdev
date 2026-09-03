<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\ApiHubController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailSettingController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\FooterSettingController;
use App\Http\Controllers\Admin\FraudCheckController;
use App\Http\Controllers\Admin\GeminiSettingController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\LandingPageController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductLayoutController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\SitemapController;
use App\Http\Controllers\Admin\SmsMarketingController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\SystemToolController;
use App\Http\Controllers\Admin\SystemUpdateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WarrantyController as AdminWarrantyController;
use App\Http\Controllers\Customer\Auth\CustomerAuthController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Customer\CustomerWarrantyController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Frontend\ProductController as FrontProductController;
use App\Http\Controllers\Frontend\ProductFeedController;
use App\Http\Controllers\Frontend\ShopController;
use Illuminate\Support\Facades\Route;

// ==========================================
// 🛍️ FRONT-END STOREFRONT ROUTES (Daraz-Style)
// ==========================================
Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/category/{slug}', [ShopController::class, 'category'])->name('category.show');
Route::get('/brand/{slug}', [ShopController::class, 'brand'])->name('brand.show');
Route::get('/product/{slug}', [FrontProductController::class, 'show'])->name('product.show');

// Shopping Cart & Live Drawer APIs
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/json', [CartController::class, 'getJson'])->name('cart.json');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::post('/cart/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

// Fast One-Page Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/order-success/{order_no}', [CheckoutController::class, 'success'])->name('checkout.success');

// Order Live Tracker
Route::get('/track-order', [FrontendController::class, 'trackOrder'])->name('order.track');

// Public Product Warranty Verification
Route::get('/warranty/verify', [CustomerWarrantyController::class, 'publicVerify'])->name('warranty.verify');

// Automated Ad & Shopping Product Feeds (Facebook Catalog & Google Merchant)
Route::get('/feeds/facebook-catalog.xml', [ProductFeedController::class, 'facebookCatalog'])->name('feed.facebook');
Route::get('/feeds/google-merchant.xml', [ProductFeedController::class, 'googleMerchant'])->name('feed.google');

// ==========================================
// 👤 CUSTOMER AUTHENTICATION & PORTAL ROUTES
// ==========================================
Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('register');

Route::prefix('customer')->name('customer.')->group(function () {
    // Guest customer routes
    Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('register.submit');
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

    // Protected customer portal routes
    Route::middleware('auth:customer')->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [CustomerDashboardController::class, 'orders'])->name('orders');
        Route::get('/orders/{order_no}', [CustomerDashboardController::class, 'orderDetail'])->name('orders.show');
        Route::get('/warranties', [CustomerWarrantyController::class, 'index'])->name('warranties');
        Route::get('/profile', [CustomerDashboardController::class, 'profile'])->name('profile');
        Route::post('/profile/update', [CustomerDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/password', [CustomerDashboardController::class, 'updatePassword'])->name('password.update');
        Route::get('/wishlist', [CustomerDashboardController::class, 'wishlist'])->name('wishlist');
    });
});

// Dynamic XML Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'generateXml'])->name('sitemap.xml');

// Landing page public flash sale preview
Route::get('/offer/{landingPage:slug}', [LandingPageController::class, 'preview'])->name('landing.preview');

// ==========================================
// 🛡️ ADMIN AUTHENTICATION (Guest & Logout)
// ==========================================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// ==========================================
// 🛡️ PROTECTED ADMIN PANEL (All 22 Modules - auth:web)
// ==========================================
Route::prefix('admin')->name('admin.')->middleware('auth:web')->group(function () {

    // 1. Dashboard Overview
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. POS System
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/search', [PosController::class, 'search'])->name('pos.search');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::get('/pos/receipt/{order}', [PosController::class, 'receipt'])->name('pos.receipt');

    // 3. Orders Management
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status.update');
    Route::post('/orders/bulk-status', [OrderController::class, 'bulkUpdateStatus'])->name('orders.bulk.status');
    Route::post('/orders/{order}/book-courier', [OrderController::class, 'bookCourier'])->name('orders.courier.book');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('/orders/{order}/packing-slip', [OrderController::class, 'packingSlip'])->name('orders.packing_slip');

    // 3.1 Admin Notifications Center & Live Polling
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/latest', [NotificationController::class, 'latest'])->name('notifications.latest');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark_all_read');
    Route::post('/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clear_all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // 3.5 Product Warranty Verification & Management
    Route::get('/warranties', [AdminWarrantyController::class, 'index'])->name('warranties.index');
    Route::post('/warranties', [AdminWarrantyController::class, 'store'])->name('warranties.store');
    Route::put('/warranties/{warranty}', [AdminWarrantyController::class, 'update'])->name('warranties.update');
    Route::get('/warranties/verify', [AdminWarrantyController::class, 'verify'])->name('warranties.verify');
    Route::delete('/warranties/{warranty}', [AdminWarrantyController::class, 'destroy'])->name('warranties.destroy');

    // 4. Product & Catalog Management
    Route::get('products/layout', [ProductLayoutController::class, 'index'])->name('products.layout');
    Route::post('products/layout', [ProductLayoutController::class, 'update'])->name('products.layout.update');
    Route::post('products/layout/reset', [ProductLayoutController::class, 'reset'])->name('products.layout.reset');
    Route::get('settings/product-layout', fn () => redirect()->route('admin.products.layout'))->name('settings.product_layout');
    Route::post('products/ai-generate-description', [ProductController::class, 'generateAiDescription'])->name('products.ai.generate');
    Route::resource('products', ProductController::class);

    // Categories (3-Tier)
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'storeCategory'])->name('categories.store');
    Route::post('/sub-categories', [CategoryController::class, 'storeSubCategory'])->name('subcategories.store');
    Route::post('/child-categories', [CategoryController::class, 'storeChildCategory'])->name('childcategories.store');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Brands
    Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');

    // Attributes (Colors, Sizes/Pinouts)
    Route::get('/attributes', [AttributeController::class, 'index'])->name('attributes.index');
    Route::post('/attributes/color', [AttributeController::class, 'storeColor'])->name('attributes.color.store');
    Route::post('/attributes/size', [AttributeController::class, 'storeSize'])->name('attributes.size.store');

    // 5. Purchases & Supplier Due
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');

    // 6. Supplier Management
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::post('/suppliers/{supplier}/pay', [SupplierController::class, 'storePayment'])->name('suppliers.pay');

    // 7. Coupon & Discount Engine
    Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons', [CouponController::class, 'store'])->name('coupons.store');
    Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy'])->name('coupons.destroy');

    // 8. Landing Page & Campaign Builder
    Route::resource('landing-pages', LandingPageController::class);

    // 9. Fraud & Risk Check
    Route::get('/fraud-checks', [FraudCheckController::class, 'index'])->name('fraud.index');
    Route::post('/fraud-checks/lookup', [FraudCheckController::class, 'checkNumber'])->name('fraud.check');
    Route::post('/fraud-checks/blacklist', [FraudCheckController::class, 'blacklistNumber'])->name('fraud.blacklist');

    // 10. Custom SMS Marketing
    Route::get('/sms-marketing', [SmsMarketingController::class, 'index'])->name('sms.index');
    Route::post('/sms-marketing/send', [SmsMarketingController::class, 'sendBulk'])->name('sms.send');

    // 11. Accounts & Funds
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
    Route::post('/accounts/deposit', [AccountController::class, 'deposit'])->name('accounts.deposit');
    Route::post('/accounts/transfer', [AccountController::class, 'transfer'])->name('accounts.transfer');

    // 12. Expenses & Budgeting
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::post('/expenses/category', [ExpenseController::class, 'storeCategory'])->name('expenses.category.store');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // 13. Users, Roles & Permissions + Customers CRM
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.role.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');

    // 14. General Settings & Integrations
    Route::get('/settings', fn () => redirect()->route('admin.settings.general'));
    Route::get('/settings/general', [GeneralSettingController::class, 'index'])->name('settings.general');
    Route::match(['post', 'put'], '/settings/general', [GeneralSettingController::class, 'update'])->name('settings.general.update');

    // 15. Email Configuration (SMTP)
    Route::get('/settings/email', [EmailSettingController::class, 'index'])->name('settings.email');
    Route::match(['post', 'put'], '/settings/email', [EmailSettingController::class, 'update'])->name('settings.email.update');
    Route::post('/settings/email/test', [EmailSettingController::class, 'testMail'])->name('settings.email.test');

    // 16. Fraud API Manager (Integrated into Fraud Module / ApiHub)
    Route::get('/settings/fraud-api', [FraudCheckController::class, 'index'])->name('settings.fraud');

    // 17. Third-Party API Hub
    Route::get('/settings/api-hub', [ApiHubController::class, 'index'])->name('settings.api_hub');
    Route::get('/settings/api_hub', [ApiHubController::class, 'index'])->name('settings.api-hub');
    Route::get('/settings/api-hub/save', fn () => redirect()->route('admin.settings.api_hub'));
    Route::match(['post', 'put'], '/settings/api-hub', [ApiHubController::class, 'update'])->name('settings.api_hub.update');
    Route::match(['post', 'put'], '/settings/api-hub/save', [ApiHubController::class, 'update'])->name('settings.api-hub.update');
    Route::post('/settings/api-hub/test', [ApiHubController::class, 'testConnection'])->name('settings.api_hub.test');

    // 17.5 Google Gemini AI Settings
    Route::get('/settings/gemini', [GeminiSettingController::class, 'index'])->name('settings.gemini');
    Route::match(['post', 'put'], '/settings/gemini', [GeminiSettingController::class, 'update'])->name('settings.gemini.update');
    Route::post('/settings/gemini/test', [GeminiSettingController::class, 'testConnection'])->name('settings.gemini.test');

    // 17.6 System & Git Version Update
    Route::get('/settings/system-update', [SystemUpdateController::class, 'index'])->name('settings.system_update');
    Route::post('/settings/system-update/pull', [SystemUpdateController::class, 'pull'])->name('settings.system_update.pull');

    // 18. Banners & Advertising
    Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
    Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');

    // 19. Analytics & Reports Hub
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/orders', [ReportController::class, 'orders'])->name('reports.orders');
    Route::get('/reports/purchases', [ReportController::class, 'purchases'])->name('reports.purchases');
    Route::get('/reports/expenses', [ReportController::class, 'expenses'])->name('reports.expenses');
    Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
    Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit_loss');

    // 20. SEO Settings
    Route::get('/settings/seo', [SeoController::class, 'index'])->name('settings.seo');
    Route::match(['post', 'put'], '/settings/seo', [SeoController::class, 'update'])->name('settings.seo.update');

    // 21. Sitemap Settings
    Route::get('/settings/sitemap', [SitemapController::class, 'index'])->name('settings.sitemap');
    Route::match(['post', 'put'], '/settings/sitemap/ping', [SitemapController::class, 'pingSearchEngines'])->name('settings.sitemap.ping');
    Route::match(['post', 'put'], '/settings/sitemap/regenerate', [SitemapController::class, 'pingSearchEngines'])->name('settings.sitemap.regenerate');

    // 22. System Tools & Cache
    Route::get('/system/tools', [SystemToolController::class, 'index'])->name('system.tools');
    Route::post('/system/cache/clear', [SystemToolController::class, 'clearCache'])->name('system.cache.clear');

    // 23. Footer Information CRUD & CMS
    Route::get('/settings/footer', [FooterSettingController::class, 'index'])->name('settings.footer');
    Route::match(['post', 'put'], '/settings/footer', [FooterSettingController::class, 'update'])->name('settings.footer.update');
});

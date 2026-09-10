# 📱 Flutter Mobile App for Ecomdev Laravel Backend

This is the complete, cross-platform **Flutter Mobile Application** designed specifically for your Laravel E-Commerce system (`Ecomdev`). It supports both **Android** and **iOS** from a single codebase with seamless Laravel Sanctum REST API integration.

---

## 🚀 Key Features Built-In

1. **🔐 Authentication & Customer Profiles (`lib/presentation/screens/auth/`)**
   - Customer Register & Login with Phone / Email.
   - Laravel Sanctum token persistence via `flutter_secure_storage`.
   - Customer loyalty points, total spent, and order history statistics.

2. **🏠 Home Storefront & Dynamic Feed (`lib/presentation/screens/home/`)**
   - Hero Banners carousel slider with autoplay and smooth page indicators.
   - Quick category circular chips with horizontal scrolling.
   - Flash Deals section with discount badges.
   - Featured Products, Best Sellers, and New Arrivals product grids.

3. **🛍️ Catalog Explorer & Search (`lib/presentation/screens/catalog/`)**
   - Multi-level Category tree & subcategory expansion.
   - Debounced keyword search and price / newest / popularity sorting.

4. **🔍 Product Details (PDP) (`lib/presentation/screens/product/`)**
   - Multi-image zoomable carousel with thumbnail previews.
   - Interactive **Color** & **Size** variant selection chips with dynamic price & stock updates.
   - Warranty specification tags.
   - Sticky "Add to Cart" & Quantity stepper.

5. **🛒 Cart & Fast Checkout (`lib/presentation/screens/cart/` & `checkout/`)**
   - Local cart persistence via `shared_preferences`.
   - Coupon code applicator with instant discount validation feedback.
   - Dynamic shipping zone calculations (Inside Dhaka, Outside Dhaka, etc.).
   - Cash on Delivery (COD) & Online Gateway payment options.
   - Order confirmation screen with instant "Track My Order" link.

6. **📦 Live Order Tracker (`lib/presentation/screens/orders/`)**
   - Customer Order History with status tags.
   - Step-by-step visual order tracking timeline (`Pending` ➔ `Processing` ➔ `In Courier Transit` ➔ `Delivered`).
   - Public tracking by Order ID & Phone number.

7. **🛡️ Warranty Manager & Barcode Scanner (`lib/presentation/screens/warranty/`)**
   - Camera QR / Barcode scanner using `mobile_scanner` to scan packaging serials.
   - Instant warranty validity check & claim submission form.

---

## 🛠️ How to Configure & Run the App

### 1. Configure Backend API URL
Open [lib/core/constants/api_endpoints.dart](file:///d:/Ecomdev/flutter_app/lib/core/constants/api_endpoints.dart):

```dart
static String get baseUrl {
  // For Android Emulator (maps to localhost on host machine):
  if (Platform.isAndroid) {
    return 'http://10.0.2.2:8000/api/v1';
  }
  // For iOS Simulator / Web:
  return 'http://127.0.0.1:8000/api/v1';
  
  // For Physical Device over Wi-Fi / Production Live Server:
  // return 'https://your-domain.com/api/v1';
}
```

### 2. Start the Laravel Backend Server
Make sure your Laravel backend is running:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### 3. Run Flutter App
Navigate to the `flutter_app` directory in your terminal or open it in VS Code / Android Studio:

```bash
cd flutter_app

# 1. Install dependencies
flutter pub get

# 2. Run on connected device or emulator
flutter run
```

---

## 📁 Architecture & File Layout

```text
flutter_app/
├── lib/
│   ├── main.dart                                # App Entry point & Riverpod Scope
│   ├── core/
│   │   ├── api/
│   │   │   ├── api_client.dart                  # Dio HTTP client with Sanctum interceptor
│   │   │   └── api_exception.dart               # Error handling & validation messages
│   │   ├── constants/
│   │   │   ├── api_endpoints.dart               # API route catalog matching Laravel routes
│   │   │   ├── app_colors.dart                  # Curated teal/slate/amber theme palette
│   │   │   └── app_constants.dart               # Storage keys, app configuration
│   │   ├── theme/
│   │   │   └── app_theme.dart                   # Modern Material 3 theme configuration
│   │   └── utils/
│   │       └── currency_formatter.dart          # Currency & number formatting (৳ / $)
│   ├── data/
│   │   ├── models/                              # DTOs mapping Laravel JSON payloads
│   │   └── repositories/                        # Network & local persistence repositories
│   ├── providers/                               # Riverpod State Notifiers (Auth, Cart, Catalog)
│   └── presentation/
│       ├── widgets/                             # Reusable components (ProductCard, Shimmer, Buttons)
│       └── screens/                             # Full feature screens (Home, PDP, Cart, Checkout, etc.)
```

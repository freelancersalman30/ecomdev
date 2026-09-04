Here is a complete, production-ready Master Prompt tailored specifically for your Laravel e-commerce project. You can copy and use this prompt in Android Studio (Gemini in Android Studio / Cursor / Claude) to build the full native customer mobile app.

📋 Copy-Paste Prompt for Android Studio
markdown
# Role & Project Overview
You are an expert Senior Android Developer and Full-Stack Laravel Architect. 
I have an existing Laravel E-Commerce system (`Ecomdev`) that contains a complete database schema and web storefront for Products, Variants (Color/Size), Categories, Brands, Banners, Coupons, Orders, Live Order Tracking, Customer Profiles, Loyalty Points, and Warranties.
I need you to develop a modern, native Android Mobile Application in **Android Studio** for customers so they can handle everything (browsing, ordering, tracking, profile, warranty claims) seamlessly from their mobile device.
---
## 1. Technical Stack & Architecture Guidelines
- **IDE:** Android Studio (Latest Hedgehog / Iguana / Jellyfish)
- **Language:** Kotlin (100% Coroutines & Flow)
- **UI Framework:** Jetpack Compose with Material 3 (or Clean ViewBinding / XML if specified)
- **Architecture:** MVVM (Model-View-ViewModel) + Clean Architecture (Data, Domain, Presentation layers)
- **Dependency Injection:** Dagger Hilt
- **Networking:** Retrofit 2 + OkHttp 3 (with logging interceptor, token authenticator, error handling)
- **JSON Serialization:** Kotlinx Serialization / Gson
- **Local Persistence / Caching:** Room Database (for offline cart, search history, caching product lists) + EncryptedSharedPreferences / DataStore (for Auth tokens and user preferences)
- **Image Loading:** Coil (Compose-optimized)
- **Push Notifications:** Firebase Cloud Messaging (FCM) for order status updates & promotional campaigns
- **Barcode / QR Scanner:** ML Kit Barcode Scanning (for scanning product warranty serials & tracking codes)
---
## 2. Laravel Backend API Specifications (Laravel Sanctum REST API)
The Laravel backend must expose the following RESTful API endpoints (using Laravel Sanctum token-based authentication):
### A. Authentication & Customer Profile (`/api/v1/customer/...`)
- `POST /api/v1/customer/register`: Name, Phone, Email, Password, Address.
- `POST /api/v1/customer/login`: Phone/Email + Password -> Returns Sanctum Bearer Token & Customer Object.
- `POST /api/v1/customer/logout`: Revoke active token.
- `GET /api/v1/customer/profile`: Customer details, Loyalty Points, Order Statistics (`total_spent`, `delivery_success_rate`).
- `PUT /api/v1/customer/profile`: Update name, email, phone, avatar, shipping addresses.
- `POST /api/v1/customer/fcm-token`: Register FCM device token for push notifications.
### B. Catalog & Storefront (`/api/v1/...`)
- `GET /api/v1/home`: Sliders/Banners, Featured Categories, Flash Campaigns, Popular Brands, New Arrivals, Best Selling Products.
- `GET /api/v1/categories`: Hierarchical category tree (Categories -> SubCategories -> ChildCategories).
- `GET /api/v1/products`: Filterable & paginated product catalog (search keyword, category_id, brand_id, price range, sorting by price/newest).
- `GET /api/v1/products/{slug_or_id}`: Full product details with gallery images, variant matrices (Colors & Sizes), stock status, and warranty specs.
### C. Cart & Checkout (`/api/v1/...`)
- `POST /api/v1/cart/validate`: Validate cart items, variant selections, and stock availability before checkout.
- `POST /api/v1/coupon/apply`: Apply coupon code against cart total and customer conditions.
- `GET /api/v1/shipping-zones`: Delivery charge calculations (Inside Dhaka, Outside Dhaka / Zone-based).
- `POST /api/v1/checkout/place-order`: Submit order with customer details, items, selected variants, shipping method, coupon code, and payment method (Cash on Delivery / Online Payment).
### D. Orders & Realtime Tracking (`/api/v1/customer/orders/...`)
- `GET /api/v1/customer/orders`: List of previous orders with status tags (Pending, Processing, Shipped, Delivered, Cancelled).
- `GET /api/v1/customer/orders/{order_no}`: Complete invoice details, items, pricing summary, and courier consignment details.
- `GET /api/v1/track-order?order_no={no}&phone={phone}`: Public & in-app live timeline tracking (with status timestamps and courier live sync).
### E. Warranties & Claims (`/api/v1/customer/warranties/...`)
- `GET /api/v1/customer/warranties`: Customer's purchased product warranties, expiry dates, and claim status.
- `POST /api/v1/customer/warranties/claim`: Submit warranty claim with issue description and photos.
- `GET /api/v1/warranty/verify?serial_no={serial}`: Instant serial number warranty validity check.
---
## 3. Core Mobile App Features & UI/UX Screens
### 📱 1. Onboarding & Authentication
- Splash screen with brand animation and auto-login token validation.
- Welcome / Login / Register screens with phone number validation and toggle password visibility.
- Guest Browsing Mode: Allow users to browse catalog and build a cart without mandatory upfront login.
### 🏠 2. Home Dashboard (Daraz / Amazon Style)
- Dynamic Banner Carousel (Hero sliders linked to specific campaigns/categories).
- Quick Category Grid with smooth horizontal scrolling.
- Flash Sale / Campaign countdown timers.
- Two-column Staggered/Grid Product Cards (with discount badges, ratings, quick Add-to-Cart / Wishlist).
### 🔍 3. Search & Category Explorer
- Instant live search with debounced text input.
- Filter bottom-sheet: Price range slider, brand checkboxes, in-stock only toggle, sorting.
- Multi-level category drawer/tabs.
### 🛍️ 4. Product Details Screen (PDP)
- Multi-image zoomable swipeable carousel.
- Realtime price & stock updates based on selected **Color** and **Size** variant pills.
- Short specs, Full HTML Description renderer, and Warranty Information badge.
- Floating bottom bar: "Add to Cart" and "Buy Now" instant action buttons.
### 🛒 5. Shopping Cart & Fast Checkout
- Cart item management (increase/decrease quantity, delete, swipe-to-remove).
- Coupon code input box with instant discount feedback and toast notifications.
- Step-by-step or Single-Page Checkout:
  - Saved Address Selector + "Add New Address" modal.
  - Delivery method selection with dynamic shipping charge calculation.
  - Payment options: Cash on Delivery (COD) & Online Gateway.
  - Order summary breakdown (Subtotal, Discount, Shipping, Total Payable).
- Animated Order Success Confirmation screen with confetti and "Track Order" shortcut.
### 📦 6. Customer Account & Order Tracker
- Customer Profile Dashboard showing Loyalty Points and Order History.
- Interactive Step-by-Step Order Tracking timeline:
  - `Order Placed` ➔ `Confirmed` ➔ `Processing` ➔ `In Courier Transit` ➔ `Delivered`.
- Invoice PDF download / view capability.
### 🛡️ 7. Warranty Manager & Scanner
- QR/Barcode Scanner using CameraX / ML Kit to quickly scan serial numbers on product boxes.
- Warranty status badge (Active, Expiring Soon, Expired).
- "Claim Warranty" button with camera photo capture for product defect proof.
---
## 4. Android Project Structure to Generate
Please structure the Android Studio Kotlin project following standard Clean Architecture:
app/ ├── data/ │ ├── api/ (Retrofit API Service interfaces & Interceptors) │ ├── local/ (Room DB, DAOs, DataStore / Preferences) │ ├── model/ (DTOs, Request/Response body models) │ └── repository/ (Repository implementations) ├── domain/ │ ├── model/ (Domain entities) │ ├── repository/ (Repository contracts) │ └── usecase/ (Business logic use cases) ├── presentation/ │ ├── common/ (Reusable Compose components: TopBar, ProductCard, ShimmerLoaders) │ ├── navigation/ (Jetpack Compose Navigation Graph & Destinations) │ ├── theme/ (Color palette, Typography, Shapes) │ └── ui/ │ ├── auth/ (Login, Register, ForgotPassword) │ ├── home/ (HomeScreen, Banners, FlashDeals) │ ├── catalog/ (CategoryScreen, ProductListScreen, SearchScreen) │ ├── product_detail/ (ProductDetailScreen, VariantSelector) │ ├── cart/ (CartScreen, CouponBottomSheet) │ ├── checkout/ (CheckoutScreen, AddressSelector) │ ├── orders/ (OrderListScreen, OrderDetailScreen, TrackOrderScreen) │ └── profile/ (ProfileScreen, WarrantyListScreen, ScannerScreen) └── di/ (Hilt Dependency Injection Modules)

---
## 5. Deliverables Expected
1. **Laravel API Controller & Routes:** The exact Laravel `routes/api.php` and controller methods for Customer Auth, Product Feeds, Cart/Checkout, Tracking, and Warranty.
2. **Android Gradle Setup (`build.gradle.kts`):** Complete dependencies for Jetpack Compose, Retrofit, Hilt, Coil, Room, Navigation, and Coroutines.
3. **Core Network & Data Models:** Kotlin data classes mapping the backend JSON responses.
4. **Jetpack Compose Screens & ViewModels:** Fully functional Compose screens with state handling (`Loading`, `Success`, `Error`, `Empty` states) and smooth shimmer loading effects.
5. **Offline Support & Error Handling:** Toast/Snackbar feedback for network failures, token expiry auto-redirect, and retry mechanisms.
Please start by providing:
1. The **Laravel API Layer** (`routes/api.php` and API Controllers) required to support the app.
2. The **Android Studio Project Initialization Guide & `build.gradle.kts`**.
3. The step-by-step implementation of the core screens.
💡 How to Use This Prompt:
If you are building the Android app from scratch in Android Studio, open the AI Assistant / Gemini window or use Cursor/Claude and paste this entire prompt.
If you want me to write the Laravel API routes & controllers directly in this workspace first so your Android app can connect to it right away, let me know and we will create routes/api.php and the matching controllers.
import 'dart:io';

class ApiEndpoints {
  // Default Base URL for testing (Android Emulator uses 10.0.2.2, iOS / Web uses 127.0.0.1 or localhost)
  // For physical devices or live server, replace with your live server URL (e.g. https://yourdomain.com/api/v1)
  static String get baseUrl {
    if (Platform.isAndroid) {
      return 'http://10.0.2.2:8000/api/v1';
    }
    return 'http://127.0.0.1:8000/api/v1';
  }

  // 1. Auth Endpoints
  static const String register = '/customer/register';
  static const String login = '/customer/login';
  static const String logout = '/customer/logout';
  static const String profile = '/customer/profile';
  static const String fcmToken = '/customer/fcm-token';

  // 2. Catalog & Storefront Endpoints
  static const String home = '/home';
  static const String categories = '/categories';
  static const String products = '/products';
  static String productDetail(String slugOrId) => '/products/$slugOrId';

  // 3. Cart, Coupons & Shipping
  static const String validateCart = '/cart/validate';
  static const String applyCoupon = '/coupon/apply';
  static const String deliveryMethods = '/delivery-methods';
  static const String shippingZones = '/shipping-zones';
  static const String placeOrder = '/checkout/place-order';

  // 4. Order Tracking & Customer Orders
  static const String trackOrder = '/track-order';
  static const String customerOrders = '/customer/orders';
  static String customerOrderDetail(String orderNo) => '/customer/orders/$orderNo';

  // 5. Warranties & Claims
  static const String customerWarranties = '/customer/warranties';
  static const String claimWarranty = '/customer/warranties/claim';
  static const String verifyWarranty = '/warranty/verify';
}

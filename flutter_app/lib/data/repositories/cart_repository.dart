import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../../core/api/api_client.dart';
import '../../core/constants/api_endpoints.dart';
import '../../core/constants/app_constants.dart';
import '../models/cart_item_model.dart';
import '../models/coupon_model.dart';
import '../models/shipping_method_model.dart';

class CartRepository {
  final ApiClient _client = ApiClient();

  Future<void> saveCartToLocal(List<CartItemModel> items) async {
    final prefs = await SharedPreferences.getInstance();
    final jsonList = items.map((i) => i.toJson()).toList();
    await prefs.setString(AppConstants.cartCacheKey, jsonEncode(jsonList));
  }

  Future<List<CartItemModel>> loadCartFromLocal() async {
    final prefs = await SharedPreferences.getInstance();
    final raw = prefs.getString(AppConstants.cartCacheKey);
    if (raw != null && raw.isNotEmpty) {
      final List decoded = jsonDecode(raw);
      return decoded.map((i) => CartItemModel.fromJson(i)).toList();
    }
    return [];
  }

  Future<List<ShippingMethodModel>> getDeliveryMethods() async {
    final response = await _client.get(ApiEndpoints.deliveryMethods);
    final list = response['data'] as List? ?? [];
    return list.map((item) => ShippingMethodModel.fromJson(item)).toList();
  }

  Future<CouponResultModel> applyCoupon({
    required String code,
    required num subtotal,
    required List<CartItemModel> items,
  }) async {
    final response = await _client.post(
      ApiEndpoints.applyCoupon,
      data: {
        'code': code,
        'subtotal': subtotal,
        'items': items.map((i) => i.toApiPayload()).toList(),
      },
    );

    return CouponResultModel.fromJson(response['data'] ?? response);
  }

  Future<Map<String, dynamic>> validateCart(List<CartItemModel> items) async {
    final response = await _client.post(
      ApiEndpoints.validateCart,
      data: {
        'items': items.map((i) => i.toApiPayload()).toList(),
      },
    );
    return response;
  }

  Future<Map<String, dynamic>> placeOrder({
    required String name,
    required String phone,
    required String address,
    String? city,
    required int deliveryMethodId,
    required String paymentMethod, // cod, online
    String? couponCode,
    String? orderNotes,
    required List<CartItemModel> items,
  }) async {
    final response = await _client.post(
      ApiEndpoints.placeOrder,
      data: {
        'shipping_name': name,
        'shipping_phone': phone,
        'shipping_address': address,
        'shipping_city': city,
        'delivery_method_id': deliveryMethodId,
        'payment_method': paymentMethod,
        'coupon_code': couponCode,
        'order_notes': orderNotes,
        'items': items.map((i) => i.toApiPayload()).toList(),
      },
    );
    return response;
  }
}

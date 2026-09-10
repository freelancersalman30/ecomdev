import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/models/cart_item_model.dart';
import '../data/models/coupon_model.dart';
import '../data/models/product_model.dart';
import '../data/models/shipping_method_model.dart';
import '../data/repositories/cart_repository.dart';

final cartRepositoryProvider = Provider<CartRepository>((ref) => CartRepository());

class CartState {
  final List<CartItemModel> items;
  final CouponResultModel? coupon;
  final ShippingMethodModel? selectedShipping;
  final bool isLoading;
  final String? error;

  CartState({
    this.items = const [],
    this.coupon,
    this.selectedShipping,
    this.isLoading = false,
    this.error,
  });

  int get totalItemCount => items.fold(0, (sum, i) => sum + i.quantity);

  num get subtotal => items.fold(0, (sum, i) => sum + i.itemTotal);

  num get discountAmount {
    if (coupon != null && coupon!.valid) {
      return coupon!.calculatedDiscount;
    }
    return 0;
  }

  num get shippingFee => selectedShipping?.cost ?? 0;

  num get grandTotal {
    final calculated = (subtotal - discountAmount) + shippingFee;
    return calculated < 0 ? 0 : calculated;
  }

  CartState copyWith({
    List<CartItemModel>? items,
    CouponResultModel? coupon,
    ShippingMethodModel? selectedShipping,
    bool? isLoading,
    String? error,
    bool clearCoupon = false,
  }) {
    return CartState(
      items: items ?? this.items,
      coupon: clearCoupon ? null : (coupon ?? this.coupon),
      selectedShipping: selectedShipping ?? this.selectedShipping,
      isLoading: isLoading ?? this.isLoading,
      error: error,
    );
  }
}

class CartNotifier extends StateNotifier<CartState> {
  final CartRepository _repo;

  CartNotifier(this._repo) : super(CartState()) {
    _init();
  }

  Future<void> _init() async {
    final cached = await _repo.loadCartFromLocal();
    state = state.copyWith(items: cached);
  }

  Future<void> addToCart(
    ProductDetailModel product, {
    ProductVariantModel? variant,
    int quantity = 1,
  }) async {
    final existingIndex = state.items.indexWhere(
      (i) => i.productId == product.id && i.variantId == variant?.id,
    );

    List<CartItemModel> updated = List.from(state.items);

    if (existingIndex >= 0) {
      final current = updated[existingIndex];
      final newQty = current.quantity + quantity;
      current.quantity = newQty > current.maxStock ? current.maxStock : newQty;
    } else {
      updated.add(
        CartItemModel.fromProduct(product, variant: variant, quantity: quantity),
      );
    }

    state = state.copyWith(items: updated);
    await _repo.saveCartToLocal(updated);
  }

  Future<void> updateQuantity(String key, int newQuantity) async {
    if (newQuantity <= 0) {
      await removeItem(key);
      return;
    }

    List<CartItemModel> updated = state.items.map((item) {
      if (item.uniqueCartKey == key) {
        item.quantity = newQuantity > item.maxStock ? item.maxStock : newQuantity;
      }
      return item;
    }).toList();

    state = state.copyWith(items: updated);
    await _repo.saveCartToLocal(updated);
  }

  Future<void> removeItem(String key) async {
    List<CartItemModel> updated = state.items.where((i) => i.uniqueCartKey != key).toList();
    state = state.copyWith(items: updated);
    await _repo.saveCartToLocal(updated);
  }

  Future<void> clearCart() async {
    state = CartState();
    await _repo.saveCartToLocal([]);
  }

  void setShippingMethod(ShippingMethodModel method) {
    state = state.copyWith(selectedShipping: method);
  }

  Future<String?> applyCoupon(String code) async {
    if (state.items.isEmpty) return 'Your cart is empty';

    state = state.copyWith(isLoading: true, error: null);
    try {
      final result = await _repo.applyCoupon(
        code: code,
        subtotal: state.subtotal,
        items: state.items,
      );

      if (result.valid) {
        state = state.copyWith(coupon: result, isLoading: false);
        return null;
      } else {
        state = state.copyWith(isLoading: false, error: result.message);
        return result.message;
      }
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
      return e.toString();
    }
  }

  void removeCoupon() {
    state = state.copyWith(clearCoupon: true);
  }
}

final cartProvider = StateNotifierProvider<CartNotifier, CartState>((ref) {
  final repo = ref.watch(cartRepositoryProvider);
  return CartNotifier(repo);
});

final shippingMethodsProvider = FutureProvider.autoDispose<List<ShippingMethodModel>>((ref) async {
  final repo = ref.watch(cartRepositoryProvider);
  return await repo.getDeliveryMethods();
});

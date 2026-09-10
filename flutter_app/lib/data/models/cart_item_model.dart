import 'product_model.dart';

class CartItemModel {
  final int productId;
  final String title;
  final String slug;
  final String? thumbnail;
  final num price;
  final int? variantId;
  final String? variantColor;
  final String? variantSize;
  int quantity;
  final int maxStock;

  CartItemModel({
    required this.productId,
    required this.title,
    required this.slug,
    this.thumbnail,
    required this.price,
    this.variantId,
    this.variantColor,
    this.variantSize,
    required this.quantity,
    required this.maxStock,
  });

  String get uniqueCartKey => '${productId}_${variantId ?? 0}';

  num get itemTotal => price * quantity;

  factory CartItemModel.fromProduct(
    ProductDetailModel product, {
    ProductVariantModel? variant,
    int quantity = 1,
  }) {
    final effectivePrice = variant != null ? variant.price : product.price;
    final effectiveStock = variant != null ? variant.stock : product.stockQuantity;

    return CartItemModel(
      productId: product.id,
      title: product.title,
      slug: product.slug,
      thumbnail: variant?.image ?? product.thumbnail,
      price: effectivePrice,
      variantId: variant?.id,
      variantColor: variant?.colorName,
      variantSize: variant?.sizeName,
      quantity: quantity,
      maxStock: effectiveStock,
    );
  }

  factory CartItemModel.fromJson(Map<String, dynamic> json) {
    return CartItemModel(
      productId: json['product_id'] ?? 0,
      title: json['title'] ?? '',
      slug: json['slug'] ?? '',
      thumbnail: json['thumbnail'],
      price: json['price'] ?? 0,
      variantId: json['variant_id'],
      variantColor: json['variant_color'],
      variantSize: json['variant_size'],
      quantity: json['quantity'] ?? 1,
      maxStock: json['max_stock'] ?? 99,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'product_id': productId,
      'title': title,
      'slug': slug,
      'thumbnail': thumbnail,
      'price': price,
      'variant_id': variantId,
      'variant_color': variantColor,
      'variant_size': variantSize,
      'quantity': quantity,
      'max_stock': maxStock,
    };
  }

  Map<String, dynamic> toApiPayload() {
    return {
      'product_id': productId,
      'variant_id': variantId,
      'quantity': quantity,
    };
  }
}

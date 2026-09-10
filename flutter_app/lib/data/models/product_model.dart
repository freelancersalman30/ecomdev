import 'category_model.dart';

class ProductCardModel {
  final int id;
  final String title;
  final String slug;
  final String? sku;
  final num price;
  final num? oldPrice;
  final num discountPercent;
  final String? thumbnail;
  final bool isFeatured;
  final bool isFlashDeal;
  final String stockStatus;
  final bool inStock;
  final String? categoryName;
  final String? brandName;
  final num ratingAvg;
  final int reviewsCount;

  ProductCardModel({
    required this.id,
    required this.title,
    required this.slug,
    this.sku,
    required this.price,
    this.oldPrice,
    required this.discountPercent,
    this.thumbnail,
    required this.isFeatured,
    required this.isFlashDeal,
    required this.stockStatus,
    required this.inStock,
    this.categoryName,
    this.brandName,
    required this.ratingAvg,
    required this.reviewsCount,
  });

  factory ProductCardModel.fromJson(Map<String, dynamic> json) {
    return ProductCardModel(
      id: json['id'] ?? 0,
      title: json['title'] ?? '',
      slug: json['slug'] ?? '',
      sku: json['sku'],
      price: json['price'] ?? 0,
      oldPrice: json['old_price'],
      discountPercent: json['discount_percent'] ?? 0,
      thumbnail: json['thumbnail'],
      isFeatured: json['is_featured'] ?? false,
      isFlashDeal: json['is_flash_deal'] ?? false,
      stockStatus: json['stock_status'] ?? 'in_stock',
      inStock: json['in_stock'] ?? true,
      categoryName: json['category_name'],
      brandName: json['brand_name'],
      ratingAvg: json['rating_avg'] ?? 0,
      reviewsCount: json['reviews_count'] ?? 0,
    );
  }
}

class ProductVariantModel {
  final int id;
  final int? colorId;
  final String? colorName;
  final String? colorCode;
  final int? sizeId;
  final String? sizeName;
  final String? sku;
  final num price;
  final int stock;
  final String? image;

  ProductVariantModel({
    required this.id,
    this.colorId,
    this.colorName,
    this.colorCode,
    this.sizeId,
    this.sizeName,
    this.sku,
    required this.price,
    required this.stock,
    this.image,
  });

  factory ProductVariantModel.fromJson(Map<String, dynamic> json) {
    return ProductVariantModel(
      id: json['id'] ?? 0,
      colorId: json['color_id'],
      colorName: json['color_name'],
      colorCode: json['color_code'],
      sizeId: json['size_id'],
      sizeName: json['size_name'],
      sku: json['sku'],
      price: json['price'] ?? 0,
      stock: json['stock'] ?? 0,
      image: json['image'],
    );
  }
}

class ProductColorOption {
  final int id;
  final String name;
  final String? code;

  ProductColorOption({
    required this.id,
    required this.name,
    this.code,
  });

  factory ProductColorOption.fromJson(Map<String, dynamic> json) {
    return ProductColorOption(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      code: json['code'],
    );
  }
}

class ProductSizeOption {
  final int id;
  final String name;

  ProductSizeOption({
    required this.id,
    required this.name,
  });

  factory ProductSizeOption.fromJson(Map<String, dynamic> json) {
    return ProductSizeOption(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
    );
  }
}

class ProductDetailModel {
  final int id;
  final String title;
  final String slug;
  final String? sku;
  final String? barcode;
  final String? description;
  final String? shortDescription;
  final num price;
  final num? oldPrice;
  final num discountPercent;
  final String? thumbnail;
  final List<String> gallery;
  final int stockQuantity;
  final String stockStatus;
  final bool inStock;
  final bool hasVariants;
  final List<ProductVariantModel> variants;
  final List<ProductColorOption> colors;
  final List<ProductSizeOption> sizes;
  final String? warrantyType;
  final String? warrantyPeriod;
  final BrandModel? brand;
  final CategoryModel? category;
  final List<ProductCardModel> relatedProducts;

  ProductDetailModel({
    required this.id,
    required this.title,
    required this.slug,
    this.sku,
    this.barcode,
    this.description,
    this.shortDescription,
    required this.price,
    this.oldPrice,
    required this.discountPercent,
    this.thumbnail,
    required this.gallery,
    required this.stockQuantity,
    required this.stockStatus,
    required this.inStock,
    required this.hasVariants,
    required this.variants,
    required this.colors,
    required this.sizes,
    this.warrantyType,
    this.warrantyPeriod,
    this.brand,
    this.category,
    this.relatedProducts = const [],
  });

  factory ProductDetailModel.fromJson(Map<String, dynamic> json) {
    var galleryList = <String>[];
    if (json['gallery'] != null && json['gallery'] is List) {
      galleryList = List<String>.from(json['gallery']);
    }

    var variantsList = <ProductVariantModel>[];
    if (json['variants'] != null && json['variants'] is List) {
      variantsList = (json['variants'] as List)
          .map((item) => ProductVariantModel.fromJson(item))
          .toList();
    }

    var colorsList = <ProductColorOption>[];
    if (json['colors'] != null && json['colors'] is List) {
      colorsList = (json['colors'] as List)
          .map((item) => ProductColorOption.fromJson(item))
          .toList();
    }

    var sizesList = <ProductSizeOption>[];
    if (json['sizes'] != null && json['sizes'] is List) {
      sizesList = (json['sizes'] as List)
          .map((item) => ProductSizeOption.fromJson(item))
          .toList();
    }

    var relatedList = <ProductCardModel>[];
    if (json['related_products'] != null && json['related_products'] is List) {
      relatedList = (json['related_products'] as List)
          .map((item) => ProductCardModel.fromJson(item))
          .toList();
    }

    return ProductDetailModel(
      id: json['id'] ?? 0,
      title: json['title'] ?? '',
      slug: json['slug'] ?? '',
      sku: json['sku'],
      barcode: json['barcode'],
      description: json['description'],
      shortDescription: json['short_description'],
      price: json['price'] ?? 0,
      oldPrice: json['old_price'],
      discountPercent: json['discount_percent'] ?? 0,
      thumbnail: json['thumbnail'],
      gallery: galleryList,
      stockQuantity: json['stock_quantity'] ?? 0,
      stockStatus: json['stock_status'] ?? 'in_stock',
      inStock: json['in_stock'] ?? true,
      hasVariants: json['has_variants'] ?? false,
      variants: variantsList,
      colors: colorsList,
      sizes: sizesList,
      warrantyType: json['warranty_type'],
      warrantyPeriod: json['warranty_period'],
      brand: json['brand'] != null ? BrandModel.fromJson(json['brand']) : null,
      category: json['category'] != null ? CategoryModel.fromJson(json['category']) : null,
      relatedProducts: relatedList,
    );
  }
}

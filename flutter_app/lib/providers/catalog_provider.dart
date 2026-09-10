import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/models/category_model.dart';
import '../data/models/product_model.dart';
import 'home_provider.dart';

final categoriesProvider = FutureProvider.autoDispose<List<CategoryModel>>((ref) async {
  final repo = ref.watch(catalogRepositoryProvider);
  return await repo.getCategories();
});

final productDetailProvider = FutureProvider.autoDispose.family<ProductDetailModel, String>((ref, slugOrId) async {
  final repo = ref.watch(catalogRepositoryProvider);
  return await repo.getProductDetail(slugOrId);
});

class ProductFilterParams {
  final String? search;
  final int? categoryId;
  final int? brandId;
  final num? minPrice;
  final num? maxPrice;
  final String? sort;
  final bool? isFlashDeal;
  final bool? isFeatured;
  final int page;

  ProductFilterParams({
    this.search,
    this.categoryId,
    this.brandId,
    this.minPrice,
    this.maxPrice,
    this.sort,
    this.isFlashDeal,
    this.isFeatured,
    this.page = 1,
  });

  ProductFilterParams copyWith({
    String? search,
    int? categoryId,
    int? brandId,
    num? minPrice,
    num? maxPrice,
    String? sort,
    bool? isFlashDeal,
    bool? isFeatured,
    int? page,
  }) {
    return ProductFilterParams(
      search: search ?? this.search,
      categoryId: categoryId ?? this.categoryId,
      brandId: brandId ?? this.brandId,
      minPrice: minPrice ?? this.minPrice,
      maxPrice: maxPrice ?? this.maxPrice,
      sort: sort ?? this.sort,
      isFlashDeal: isFlashDeal ?? this.isFlashDeal,
      isFeatured: isFeatured ?? this.isFeatured,
      page: page ?? this.page,
    );
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    return other is ProductFilterParams &&
        other.search == search &&
        other.categoryId == categoryId &&
        other.brandId == brandId &&
        other.minPrice == minPrice &&
        other.maxPrice == maxPrice &&
        other.sort == sort &&
        other.isFlashDeal == isFlashDeal &&
        other.isFeatured == isFeatured &&
        other.page == page;
  }

  @override
  int get hashCode => Object.hash(
        search,
        categoryId,
        brandId,
        minPrice,
        maxPrice,
        sort,
        isFlashDeal,
        isFeatured,
        page,
      );
}

final filteredProductsProvider = FutureProvider.autoDispose.family<Map<String, dynamic>, ProductFilterParams>((ref, params) async {
  final repo = ref.watch(catalogRepositoryProvider);
  return await repo.getProducts(
    search: params.search,
    categoryId: params.categoryId,
    brandId: params.brandId,
    minPrice: params.minPrice,
    maxPrice: params.maxPrice,
    sort: params.sort,
    isFlashDeal: params.isFlashDeal,
    isFeatured: params.isFeatured,
    page: params.page,
  );
});

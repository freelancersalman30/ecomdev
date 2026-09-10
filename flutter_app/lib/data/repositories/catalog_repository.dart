import '../../core/api/api_client.dart';
import '../../core/constants/api_endpoints.dart';
import '../models/category_model.dart';
import '../models/home_feed_model.dart';
import '../models/product_model.dart';

class CatalogRepository {
  final ApiClient _client = ApiClient();

  Future<HomeFeedModel> getHomeFeed() async {
    final response = await _client.get(ApiEndpoints.home);
    return HomeFeedModel.fromJson(response['data'] ?? response);
  }

  Future<List<CategoryModel>> getCategories() async {
    final response = await _client.get(ApiEndpoints.categories);
    final list = response['data'] as List? ?? [];
    return list.map((item) => CategoryModel.fromJson(item)).toList();
  }

  Future<Map<String, dynamic>> getProducts({
    String? search,
    int? categoryId,
    int? brandId,
    num? minPrice,
    num? maxPrice,
    String? sort,
    bool? isFlashDeal,
    bool? isFeatured,
    int page = 1,
  }) async {
    final queryParams = <String, dynamic>{
      'page': page,
    };

    if (search != null && search.isNotEmpty) queryParams['search'] = search;
    if (categoryId != null) queryParams['category_id'] = categoryId;
    if (brandId != null) queryParams['brand_id'] = brandId;
    if (minPrice != null) queryParams['min_price'] = minPrice;
    if (maxPrice != null) queryParams['max_price'] = maxPrice;
    if (sort != null) queryParams['sort'] = sort;
    if (isFlashDeal == true) queryParams['flash_deal'] = 1;
    if (isFeatured == true) queryParams['featured'] = 1;

    final response = await _client.get(
      ApiEndpoints.products,
      queryParameters: queryParams,
    );

    final data = response['data'] as List? ?? [];
    final products = data.map((item) => ProductCardModel.fromJson(item)).toList();

    return {
      'products': products,
      'meta': response['meta'] ?? {},
    };
  }

  Future<ProductDetailModel> getProductDetail(String slugOrId) async {
    final response = await _client.get(ApiEndpoints.productDetail(slugOrId));
    return ProductDetailModel.fromJson(response['data'] ?? response);
  }
}

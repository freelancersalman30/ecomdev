import 'category_model.dart';
import 'product_model.dart';

class HomeFeedModel {
  final List<BannerModel> banners;
  final List<CategoryModel> categories;
  final List<CampaignModel> flashCampaigns;
  final List<BrandModel> popularBrands;
  final List<ProductCardModel> flashDeals;
  final List<ProductCardModel> featuredProducts;
  final List<ProductCardModel> bestSellers;
  final List<ProductCardModel> newArrivals;

  HomeFeedModel({
    required this.banners,
    required this.categories,
    required this.flashCampaigns,
    required this.popularBrands,
    required this.flashDeals,
    required this.featuredProducts,
    required this.bestSellers,
    required this.newArrivals,
  });

  factory HomeFeedModel.fromJson(Map<String, dynamic> json) {
    return HomeFeedModel(
      banners: (json['banners'] as List? ?? [])
          .map((item) => BannerModel.fromJson(item))
          .toList(),
      categories: (json['featured_categories'] as List? ?? [])
          .map((item) => CategoryModel.fromJson(item))
          .toList(),
      flashCampaigns: (json['flash_campaigns'] as List? ?? [])
          .map((item) => CampaignModel.fromJson(item))
          .toList(),
      popularBrands: (json['popular_brands'] as List? ?? [])
          .map((item) => BrandModel.fromJson(item))
          .toList(),
      flashDeals: (json['flash_deals'] as List? ?? [])
          .map((item) => ProductCardModel.fromJson(item))
          .toList(),
      featuredProducts: (json['featured_products'] as List? ?? [])
          .map((item) => ProductCardModel.fromJson(item))
          .toList(),
      bestSellers: (json['best_sellers'] as List? ?? [])
          .map((item) => ProductCardModel.fromJson(item))
          .toList(),
      newArrivals: (json['new_arrivals'] as List? ?? [])
          .map((item) => ProductCardModel.fromJson(item))
          .toList(),
    );
  }
}

class CategoryModel {
  final int id;
  final String name;
  final String slug;
  final String? image;
  final String? icon;
  final int productsCount;
  final List<CategoryModel> children;

  CategoryModel({
    required this.id,
    required this.name,
    required this.slug,
    this.image,
    this.icon,
    this.productsCount = 0,
    this.children = const [],
  });

  factory CategoryModel.fromJson(Map<String, dynamic> json) {
    var childrenList = <CategoryModel>[];
    if (json['children'] != null && json['children'] is List) {
      childrenList = (json['children'] as List)
          .map((item) => CategoryModel.fromJson(item))
          .toList();
    }

    return CategoryModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      slug: json['slug'] ?? '',
      image: json['image'],
      icon: json['icon'],
      productsCount: json['products_count'] ?? 0,
      children: childrenList,
    );
  }
}

class BrandModel {
  final int id;
  final String name;
  final String slug;
  final String? logo;

  BrandModel({
    required this.id,
    required this.name,
    required this.slug,
    this.logo,
  });

  factory BrandModel.fromJson(Map<String, dynamic> json) {
    return BrandModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      slug: json['slug'] ?? '',
      logo: json['logo'],
    );
  }
}

class BannerModel {
  final int id;
  final String title;
  final String? subtitle;
  final String? image;
  final String? linkUrl;
  final String actionType;

  BannerModel({
    required this.id,
    required this.title,
    this.subtitle,
    this.image,
    this.linkUrl,
    required this.actionType,
  });

  factory BannerModel.fromJson(Map<String, dynamic> json) {
    return BannerModel(
      id: json['id'] ?? 0,
      title: json['title'] ?? '',
      subtitle: json['subtitle'],
      image: json['image'],
      linkUrl: json['link_url'],
      actionType: json['action_type'] ?? 'url',
    );
  }
}

class CampaignModel {
  final int id;
  final String name;
  final String? banner;
  final num? discountPercentage;
  final String? startDate;
  final String? endDate;

  CampaignModel({
    required this.id,
    required this.name,
    this.banner,
    this.discountPercentage,
    this.startDate,
    this.endDate,
  });

  factory CampaignModel.fromJson(Map<String, dynamic> json) {
    return CampaignModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      banner: json['banner'],
      discountPercentage: json['discount_percentage'],
      startDate: json['start_date'],
      endDate: json['end_date'],
    );
  }
}

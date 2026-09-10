import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/constants/app_colors.dart';
import '../../../data/models/category_model.dart';
import '../../../data/models/product_model.dart';
import '../../../providers/home_provider.dart';
import '../../widgets/banner_carousel.dart';
import '../../widgets/product_card.dart';
import '../../widgets/shimmer_loader.dart';
import '../catalog/category_screen.dart';
import '../catalog/product_list_screen.dart';
import '../product/product_detail_screen.dart';

class HomeScreen extends ConsumerWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final homeAsync = ref.watch(homeFeedProvider);

    return Scaffold(
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: const [
            Text(
              'EcomStore',
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.w800,
                color: AppColors.primaryDark,
              ),
            ),
            Text(
              'Find your favorite products',
              style: TextStyle(fontSize: 11, color: AppColors.textSecondary, fontWeight: FontWeight.normal),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.search_rounded),
            onPressed: () {
              Navigator.of(context).push(
                MaterialPageRoute(
                  builder: (_) => const ProductListScreen(title: 'Search Products'),
                ),
              );
            },
          ),
        ],
      ),
      body: homeAsync.when(
        data: (feed) {
          return RefreshIndicator(
            onRefresh: () async {
              ref.invalidate(homeFeedProvider);
            },
            child: SingleChildScrollView(
              padding: const EdgeInsets.only(bottom: 24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const SizedBox(height: 12),

                  // 1. Hero Banners Carousel
                  if (feed.banners.isNotEmpty) ...[
                    BannerCarousel(
                      banners: feed.banners,
                      onBannerTap: (banner) {
                        Navigator.of(context).push(
                          MaterialPageRoute(
                            builder: (_) => ProductListScreen(title: banner.title),
                          ),
                        );
                      },
                    ),
                    const SizedBox(height: 20),
                  ],

                  // 2. Featured Categories Row
                  if (feed.categories.isNotEmpty) ...[
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 16),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text(
                            'Categories',
                            style: TextStyle(fontSize: 17, fontWeight: FontWeight.w700),
                          ),
                          TextButton(
                            onPressed: () {
                              Navigator.of(context).push(
                                MaterialPageRoute(builder: (_) => const CategoryScreen()),
                              );
                            },
                            child: const Text('See All', style: TextStyle(color: AppColors.primary, fontWeight: FontWeight.w600)),
                          ),
                        ],
                      ),
                    ),
                    SizedBox(
                      height: 96,
                      child: ListView.separated(
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        scrollDirection: Axis.horizontal,
                        itemCount: feed.categories.length,
                        separatorBuilder: (_, __) => const SizedBox(width: 14),
                        itemBuilder: (context, index) {
                          final cat = feed.categories[index];
                          return _buildCategoryChip(context, cat);
                        },
                      ),
                    ),
                    const SizedBox(height: 20),
                  ],

                  // 3. Flash Sale / Flash Deals
                  if (feed.flashDeals.isNotEmpty) ...[
                    _buildSectionHeader(
                      title: '⚡ Flash Deals',
                      subtitle: 'Limited time offers with big discounts',
                      onViewAll: () {
                        Navigator.of(context).push(
                          MaterialPageRoute(
                            builder: (_) => const ProductListScreen(
                              title: 'Flash Deals',
                              isFlashDeal: true,
                            ),
                          ),
                        );
                      },
                    ),
                    const SizedBox(height: 12),
                    _buildHorizontalProductList(context, feed.flashDeals),
                    const SizedBox(height: 24),
                  ],

                  // 4. Featured Products Grid
                  if (feed.featuredProducts.isNotEmpty) ...[
                    _buildSectionHeader(
                      title: '🌟 Featured Products',
                      subtitle: 'Curated top recommendations',
                      onViewAll: () {
                        Navigator.of(context).push(
                          MaterialPageRoute(
                            builder: (_) => const ProductListScreen(
                              title: 'Featured Products',
                              isFeatured: true,
                            ),
                          ),
                        );
                      },
                    ),
                    const SizedBox(height: 12),
                    _buildProductGrid(context, feed.featuredProducts),
                    const SizedBox(height: 24),
                  ],

                  // 5. Best Sellers
                  if (feed.bestSellers.isNotEmpty) ...[
                    _buildSectionHeader(
                      title: '🔥 Best Selling',
                      subtitle: 'Most popular among shoppers',
                      onViewAll: () {
                        Navigator.of(context).push(
                          MaterialPageRoute(
                            builder: (_) => const ProductListScreen(title: 'Best Selling'),
                          ),
                        );
                      },
                    ),
                    const SizedBox(height: 12),
                    _buildHorizontalProductList(context, feed.bestSellers),
                    const SizedBox(height: 24),
                  ],

                  // 6. New Arrivals
                  if (feed.newArrivals.isNotEmpty) ...[
                    _buildSectionHeader(
                      title: '✨ New Arrivals',
                      subtitle: 'Fresh products added this week',
                      onViewAll: () {
                        Navigator.of(context).push(
                          MaterialPageRoute(
                            builder: (_) => const ProductListScreen(title: 'New Arrivals'),
                          ),
                        );
                      },
                    ),
                    const SizedBox(height: 12),
                    _buildProductGrid(context, feed.newArrivals),
                  ],
                ],
              ),
            ),
          );
        },
        loading: () => const SingleChildScrollView(
          padding: EdgeInsets.all(16),
          child: Column(
            children: [
              ShimmerLoader(width: double.infinity, height: 160, borderRadius: 16),
              SizedBox(height: 20),
              ProductGridShimmer(),
            ],
          ),
        ),
        error: (err, _) => Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.cloud_off_rounded, size: 48, color: AppColors.error),
              const SizedBox(height: 12),
              Text('Failed to load home feed: ${err.toString()}'),
              const SizedBox(height: 12),
              ElevatedButton(
                onPressed: () => ref.invalidate(homeFeedProvider),
                child: const Text('Try Again'),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildCategoryChip(BuildContext context, CategoryModel cat) {
    return GestureDetector(
      onTap: () {
        Navigator.of(context).push(
          MaterialPageRoute(
            builder: (_) => ProductListScreen(
              title: cat.name,
              categoryId: cat.id,
            ),
          ),
        );
      },
      child: Column(
        children: [
          Container(
            width: 58,
            height: 58,
            padding: const EdgeInsets.all(3),
            decoration: BoxDecoration(
              color: AppColors.primaryLight,
              shape: BoxShape.circle,
              border: Border.all(color: AppColors.primary.withOpacity(0.3), width: 1.5),
            ),
            child: ClipOval(
              child: cat.image != null && cat.image!.isNotEmpty
                  ? CachedNetworkImage(
                      imageUrl: cat.image!,
                      fit: BoxFit.cover,
                      errorWidget: (_, __, ___) => const Icon(Icons.category, color: AppColors.primary),
                    )
                  : const Icon(Icons.category, color: AppColors.primary),
            ),
          ),
          const SizedBox(height: 6),
          SizedBox(
            width: 70,
            child: Text(
              cat.name,
              textAlign: TextAlign.center,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: AppColors.textPrimary),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSectionHeader({
    required String title,
    required String subtitle,
    required VoidCallback onViewAll,
  }) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w800, color: AppColors.textPrimary),
              ),
              Text(
                subtitle,
                style: const TextStyle(fontSize: 11, color: AppColors.textSecondary),
              ),
            ],
          ),
          TextButton(
            onPressed: onViewAll,
            child: const Text('View All', style: TextStyle(color: AppColors.primary, fontWeight: FontWeight.w600)),
          ),
        ],
      ),
    );
  }

  Widget _buildHorizontalProductList(BuildContext context, List<ProductCardModel> products) {
    return SizedBox(
      height: 255,
      child: ListView.separated(
        padding: const EdgeInsets.symmetric(horizontal: 16),
        scrollDirection: Axis.horizontal,
        itemCount: products.length,
        separatorBuilder: (_, __) => const SizedBox(width: 12),
        itemBuilder: (context, index) {
          final p = products[index];
          return SizedBox(
            width: 160,
            child: ProductCard(
              product: p,
              onTap: () {
                Navigator.of(context).push(
                  MaterialPageRoute(builder: (_) => ProductDetailScreen(slugOrId: p.slug)),
                );
              },
            ),
          );
        },
      ),
    );
  }

  Widget _buildProductGrid(BuildContext context, List<ProductCardModel> products) {
    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      padding: const EdgeInsets.symmetric(horizontal: 16),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        childAspectRatio: 0.68,
        crossAxisSpacing: 12,
        mainAxisSpacing: 12,
      ),
      itemCount: products.length > 6 ? 6 : products.length,
      itemBuilder: (context, index) {
        final p = products[index];
        return ProductCard(
          product: p,
          onTap: () {
            Navigator.of(context).push(
              MaterialPageRoute(builder: (_) => ProductDetailScreen(slugOrId: p.slug)),
            );
          },
        );
      },
    );
  }
}

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/constants/app_colors.dart';
import '../../../data/models/product_model.dart';
import '../../../providers/catalog_provider.dart';
import '../../widgets/product_card.dart';
import '../../widgets/shimmer_loader.dart';
import '../product/product_detail_screen.dart';

class ProductListScreen extends ConsumerStatefulWidget {
  final String title;
  final int? categoryId;
  final int? brandId;
  final bool? isFlashDeal;
  final bool? isFeatured;
  final String? initialSearch;

  const ProductListScreen({
    super.key,
    required this.title,
    this.categoryId,
    this.brandId,
    this.isFlashDeal,
    this.isFeatured,
    this.initialSearch,
  });

  @override
  ConsumerState<ProductListScreen> createState() => _ProductListScreenState();
}

class _ProductListScreenState extends ConsumerState<ProductListScreen> {
  late final TextEditingController _searchController;
  late ProductFilterParams _filterParams;
  String _selectedSort = 'newest';

  @override
  void initState() {
    super.initState();
    _searchController = TextEditingController(text: widget.initialSearch);
    _filterParams = ProductFilterParams(
      categoryId: widget.categoryId,
      brandId: widget.brandId,
      isFlashDeal: widget.isFlashDeal,
      isFeatured: widget.isFeatured,
      search: widget.initialSearch,
      sort: _selectedSort,
    );
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  void _onSearchSubmitted(String query) {
    setState(() {
      _filterParams = _filterParams.copyWith(search: query.trim().isNotEmpty ? query.trim() : null);
    });
  }

  void _onSortChanged(String? newSort) {
    if (newSort != null) {
      setState(() {
        _selectedSort = newSort;
        _filterParams = _filterParams.copyWith(sort: newSort);
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final productsAsync = ref.watch(filteredProductsProvider(_filterParams));

    return Scaffold(
      appBar: AppBar(
        title: Text(widget.title),
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(60),
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            child: Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _searchController,
                    onSubmitted: _onSearchSubmitted,
                    textInputAction: TextInputAction.search,
                    decoration: InputDecoration(
                      hintText: 'Search in ${widget.title}...',
                      prefixIcon: const Icon(Icons.search, size: 20, color: AppColors.textMuted),
                      suffixIcon: _searchController.text.isNotEmpty
                          ? IconButton(
                              icon: const Icon(Icons.clear, size: 18),
                              onPressed: () {
                                _searchController.clear();
                                _onSearchSubmitted('');
                              },
                            )
                          : null,
                      contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                      fillColor: AppColors.background,
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: AppColors.border),
                  ),
                  child: DropdownButtonHideUnderline(
                    child: DropdownButton<String>(
                      value: _selectedSort,
                      icon: const Icon(Icons.sort_rounded, size: 20, color: AppColors.primary),
                      items: const [
                        DropdownMenuItem(value: 'newest', child: Text('Newest', style: TextStyle(fontSize: 13))),
                        DropdownMenuItem(value: 'price_low_high', child: Text('Price: Low to High', style: TextStyle(fontSize: 13))),
                        DropdownMenuItem(value: 'price_high_low', child: Text('Price: High to Low', style: TextStyle(fontSize: 13))),
                        DropdownMenuItem(value: 'popular', child: Text('Popular', style: TextStyle(fontSize: 13))),
                      ],
                      onChanged: _onSortChanged,
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
      body: productsAsync.when(
        data: (result) {
          final products = result['products'] as List<ProductCardModel>? ?? [];

          if (products.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.search_off_rounded, size: 64, color: AppColors.textMuted),
                  const SizedBox(height: 12),
                  const Text(
                    'No products found',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600, color: AppColors.textPrimary),
                  ),
                  const SizedBox(height: 6),
                  const Text(
                    'Try adjusting your search query or filter',
                    style: TextStyle(fontSize: 13, color: AppColors.textSecondary),
                  ),
                ],
              ),
            );
          }

          return RefreshIndicator(
            onRefresh: () async {
              ref.invalidate(filteredProductsProvider(_filterParams));
            },
            child: GridView.builder(
              padding: const EdgeInsets.all(16),
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 2,
                childAspectRatio: 0.68,
                crossAxisSpacing: 12,
                mainAxisSpacing: 12,
              ),
              itemCount: products.length,
              itemBuilder: (context, index) {
                final product = products[index];
                return ProductCard(
                  product: product,
                  onTap: () {
                    Navigator.of(context).push(
                      MaterialPageRoute(
                        builder: (_) => ProductDetailScreen(slugOrId: product.slug),
                      ),
                    );
                  },
                );
              },
            ),
          );
        },
        loading: () => const ProductGridShimmer(),
        error: (err, stack) => Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.error_outline_rounded, size: 48, color: AppColors.error),
              const SizedBox(height: 12),
              Text(
                'Failed to load products: ${err.toString()}',
                textAlign: TextAlign.center,
                style: const TextStyle(color: AppColors.textSecondary),
              ),
              const SizedBox(height: 16),
              ElevatedButton(
                onPressed: () => ref.invalidate(filteredProductsProvider(_filterParams)),
                child: const Text('Try Again'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

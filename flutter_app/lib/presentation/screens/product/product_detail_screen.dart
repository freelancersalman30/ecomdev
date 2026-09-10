import 'package:cached_network_image/cached_network_image.dart';
import 'package:carousel_slider/carousel_slider.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:smooth_page_indicator/smooth_page_indicator.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/utils/currency_formatter.dart';
import '../../../data/models/product_model.dart';
import '../../../providers/cart_provider.dart';
import '../../../providers/catalog_provider.dart';
import '../../widgets/custom_button.dart';
import '../../widgets/shimmer_loader.dart';
import '../cart/cart_screen.dart';

class ProductDetailScreen extends ConsumerStatefulWidget {
  final String slugOrId;

  const ProductDetailScreen({super.key, required this.slugOrId});

  @override
  ConsumerState<ProductDetailScreen> createState() => _ProductDetailScreenState();
}

class _ProductDetailScreenState extends ConsumerState<ProductDetailScreen> {
  int _currentImageIndex = 0;
  int? _selectedColorId;
  int? _selectedSizeId;
  int _quantity = 1;
  ProductVariantModel? _selectedVariant;

  void _onColorSelected(ProductDetailModel product, int? colorId) {
    setState(() {
      _selectedColorId = colorId;
      _updateMatchedVariant(product);
    });
  }

  void _onSizeSelected(ProductDetailModel product, int? sizeId) {
    setState(() {
      _selectedSizeId = sizeId;
      _updateMatchedVariant(product);
    });
  }

  void _updateMatchedVariant(ProductDetailModel product) {
    if (!product.hasVariants || product.variants.isEmpty) {
      _selectedVariant = null;
      return;
    }

    try {
      _selectedVariant = product.variants.firstWhere(
        (v) =>
            (_selectedColorId == null || v.colorId == _selectedColorId) &&
            (_selectedSizeId == null || v.sizeId == _selectedSizeId),
      );
    } catch (_) {
      _selectedVariant = null;
    }
  }

  @override
  Widget build(BuildContext context) {
    final detailAsync = ref.watch(productDetailProvider(widget.slugOrId));

    return detailAsync.when(
      data: (product) => _buildContent(product),
      loading: () => Scaffold(
        appBar: AppBar(),
        body: const Center(child: CircularProgressIndicator(color: AppColors.primary)),
      ),
      error: (err, _) => Scaffold(
        appBar: AppBar(),
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.error_outline, size: 48, color: AppColors.error),
              const SizedBox(height: 12),
              Text('Error: ${err.toString()}'),
              const SizedBox(height: 16),
              ElevatedButton(
                onPressed: () => ref.invalidate(productDetailProvider(widget.slugOrId)),
                child: const Text('Retry'),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildContent(ProductDetailModel product) {
    final effectivePrice = _selectedVariant != null ? _selectedVariant!.price : product.price;
    final effectiveStock = _selectedVariant != null ? _selectedVariant!.stock : product.stockQuantity;
    final isInStock = effectiveStock > 0;

    final List<String> images = [];
    if (product.thumbnail != null && product.thumbnail!.isNotEmpty) {
      images.add(product.thumbnail!);
    }
    for (final img in product.gallery) {
      if (!images.contains(img)) images.add(img);
    }
    if (images.isEmpty) images.add('');

    return Scaffold(
      appBar: AppBar(
        title: Text(
          product.brand?.name ?? product.category?.name ?? 'Product Details',
          style: const TextStyle(fontSize: 16),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.shopping_cart_outlined),
            onPressed: () {
              Navigator.of(context).push(
                MaterialPageRoute(builder: (_) => const CartScreen()),
              );
            },
          ),
        ],
      ),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.06),
              blurRadius: 10,
              offset: const Offset(0, -4),
            ),
          ],
        ),
        child: SafeArea(
          child: Row(
            children: [
              // Quantity Stepper
              Container(
                decoration: BoxDecoration(
                  border: Border.all(color: AppColors.border),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Row(
                  children: [
                    IconButton(
                      icon: const Icon(Icons.remove, size: 16),
                      onPressed: _quantity > 1 ? () => setState(() => _quantity--) : null,
                    ),
                    Text(
                      '$_quantity',
                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
                    ),
                    IconButton(
                      icon: const Icon(Icons.add, size: 16),
                      onPressed: _quantity < effectiveStock ? () => setState(() => _quantity++) : null,
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 12),
              // Add to Cart Button
              Expanded(
                child: CustomButton(
                  text: isInStock ? 'Add to Cart' : 'Out of Stock',
                  icon: Icons.add_shopping_cart_rounded,
                  backgroundColor: isInStock ? AppColors.primary : AppColors.textMuted,
                  onPressed: isInStock
                      ? () async {
                          await ref.read(cartProvider.notifier).addToCart(
                                product,
                                variant: _selectedVariant,
                                quantity: _quantity,
                              );
                          if (mounted) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(
                                content: Text('Added ${_quantity}x "${product.title}" to cart!'),
                                backgroundColor: AppColors.primaryDark,
                                action: SnackBarAction(
                                  label: 'View Cart',
                                  textColor: AppColors.accent,
                                  onPressed: () {
                                    Navigator.of(context).push(
                                      MaterialPageRoute(builder: (_) => const CartScreen()),
                                    );
                                  },
                                ),
                              ),
                            );
                          }
                        }
                      : null,
                ),
              ),
            ],
          ),
        ),
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Image Carousel
            Stack(
              children: [
                CarouselSlider.builder(
                  itemCount: images.length,
                  options: CarouselOptions(
                    height: 320,
                    viewportFraction: 1.0,
                    enableInfiniteScroll: images.length > 1,
                    onPageChanged: (index, _) {
                      setState(() => _currentImageIndex = index);
                    },
                  ),
                  itemBuilder: (context, index, _) {
                    final imgUrl = images[index];
                    return Container(
                      color: Colors.white,
                      width: double.infinity,
                      child: imgUrl.isNotEmpty
                          ? CachedNetworkImage(
                              imageUrl: imgUrl,
                              fit: BoxFit.contain,
                              placeholder: (_, __) => const ShimmerLoader(
                                width: double.infinity,
                                height: 320,
                                borderRadius: 0,
                              ),
                              errorWidget: (_, __, ___) => const Center(
                                child: Icon(Icons.image_not_supported_outlined, size: 64, color: AppColors.textMuted),
                              ),
                            )
                          : const Center(
                              child: Icon(Icons.image_outlined, size: 64, color: AppColors.textMuted),
                            ),
                    );
                  },
                ),
                if (images.length > 1)
                  Positioned(
                    bottom: 12,
                    left: 0,
                    right: 0,
                    child: Center(
                      child: AnimatedSmoothIndicator(
                        activeIndex: _currentImageIndex,
                        count: images.length,
                        effect: const ExpandingDotsEffect(
                          dotHeight: 6,
                          dotWidth: 6,
                          activeDotColor: AppColors.primary,
                          dotColor: AppColors.border,
                        ),
                      ),
                    ),
                  ),
                if (product.discountPercent > 0)
                  Positioned(
                    top: 16,
                    left: 16,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: AppColors.error,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        '-${product.discountPercent.toInt()}% OFF',
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ),
              ],
            ),

            // Main Details
            Container(
              padding: const EdgeInsets.all(20),
              decoration: const BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Brand & Category Row
                  Row(
                    children: [
                      if (product.category != null)
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                          decoration: BoxDecoration(
                            color: AppColors.primaryLight,
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Text(
                            product.category!.name,
                            style: const TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.w600,
                              color: AppColors.primaryDark,
                            ),
                          ),
                        ),
                      const Spacer(),
                      Text(
                        isInStock ? 'In Stock ($effectiveStock)' : 'Out of Stock',
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                          color: isInStock ? AppColors.success : AppColors.error,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 10),
                  Text(
                    product.title,
                    style: const TextStyle(
                      fontSize: 19,
                      fontWeight: FontWeight.w800,
                      color: AppColors.textPrimary,
                      height: 1.3,
                    ),
                  ),
                  const SizedBox(height: 12),
                  // Price Row
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.baseline,
                    textBaseline: TextBaseline.alphabetic,
                    children: [
                      Text(
                        CurrencyFormatter.format(effectivePrice),
                        style: const TextStyle(
                          fontSize: 24,
                          fontWeight: FontWeight.w800,
                          color: AppColors.primaryDark,
                        ),
                      ),
                      if (product.oldPrice != null && product.oldPrice! > effectivePrice) ...[
                        const SizedBox(width: 10),
                        Text(
                          CurrencyFormatter.format(product.oldPrice),
                          style: const TextStyle(
                            fontSize: 15,
                            color: AppColors.textMuted,
                            decoration: TextDecoration.lineThrough,
                          ),
                        ),
                      ],
                    ],
                  ),

                  // Warranty Badge
                  if (product.warrantyPeriod != null && product.warrantyPeriod!.isNotEmpty) ...[
                    const SizedBox(height: 14),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                      decoration: BoxDecoration(
                        color: AppColors.background,
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: AppColors.border),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.verified_user_outlined, size: 20, color: AppColors.primary),
                          const SizedBox(width: 8),
                          Text(
                            'Warranty: ${product.warrantyPeriod} ${product.warrantyType ?? ""}',
                            style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600),
                          ),
                        ],
                      ),
                    ),
                  ],

                  // Colors Selector
                  if (product.colors.isNotEmpty) ...[
                    const SizedBox(height: 20),
                    const Text('Select Color', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700)),
                    const SizedBox(height: 8),
                    Wrap(
                      spacing: 8,
                      children: product.colors.map((c) {
                        final isSelected = _selectedColorId == c.id;
                        return ChoiceChip(
                          label: Text(c.name),
                          selected: isSelected,
                          selectedColor: AppColors.primary,
                          labelStyle: TextStyle(
                            color: isSelected ? Colors.white : AppColors.textPrimary,
                            fontWeight: FontWeight.w600,
                            fontSize: 12,
                          ),
                          onSelected: (selected) => _onColorSelected(product, selected ? c.id : null),
                        );
                      }).toList(),
                    ),
                  ],

                  // Sizes Selector
                  if (product.sizes.isNotEmpty) ...[
                    const SizedBox(height: 16),
                    const Text('Select Size', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700)),
                    const SizedBox(height: 8),
                    Wrap(
                      spacing: 8,
                      children: product.sizes.map((s) {
                        final isSelected = _selectedSizeId == s.id;
                        return ChoiceChip(
                          label: Text(s.name),
                          selected: isSelected,
                          selectedColor: AppColors.primary,
                          labelStyle: TextStyle(
                            color: isSelected ? Colors.white : AppColors.textPrimary,
                            fontWeight: FontWeight.w600,
                            fontSize: 12,
                          ),
                          onSelected: (selected) => _onSizeSelected(product, selected ? s.id : null),
                        );
                      }).toList(),
                    ),
                  ],

                  // Description
                  if (product.description != null && product.description!.isNotEmpty) ...[
                    const SizedBox(height: 24),
                    const Text('Description', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
                    const SizedBox(height: 8),
                    Text(
                      product.description!,
                      style: const TextStyle(fontSize: 13, color: AppColors.textSecondary, height: 1.6),
                    ),
                  ],

                  const SizedBox(height: 30),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

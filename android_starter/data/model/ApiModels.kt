package com.dreamerspcb.ecommerce.data.model

import com.google.gson.annotations.SerializedName

// ==========================================
// Generic API Response Wrapper
// ==========================================
data class ApiResponse<T>(
    @SerializedName("success") val success: Boolean,
    @SerializedName("message") val message: String? = null,
    @SerializedName("data") val data: T? = null,
    @SerializedName("errors") val errors: Map<String, List<String>>? = null
)

// ==========================================
// Customer & Auth DTOs
// ==========================================
data class AuthResponse(
    @SerializedName("success") val success: Boolean,
    @SerializedName("message") val message: String?,
    @SerializedName("token") val token: String?,
    @SerializedName("customer") val customer: CustomerDto?
)

data class CustomerDto(
    @SerializedName("id") val id: Long,
    @SerializedName("name") val name: String,
    @SerializedName("phone") val phone: String,
    @SerializedName("email") val email: String?,
    @SerializedName("avatar") val avatar: String?,
    @SerializedName("address") val address: String?,
    @SerializedName("city") val city: String?,
    @SerializedName("postal_code") val postalCode: String?,
    @SerializedName("loyalty_points") val loyaltyPoints: Int,
    @SerializedName("total_spent") val totalSpent: Double,
    @SerializedName("total_orders_count") val totalOrdersCount: Int,
    @SerializedName("delivery_success_rate") val deliverySuccessRate: Double
)

// ==========================================
// Home Feed DTOs
// ==========================================
data class HomeFeedDto(
    @SerializedName("banners") val banners: List<BannerDto>,
    @SerializedName("categories") val categories: List<CategoryDto>,
    @SerializedName("flash_campaigns") val flashCampaigns: List<CampaignDto>,
    @SerializedName("brands") val brands: List<BrandDto>,
    @SerializedName("flash_deals") val flashDeals: List<ProductCardDto>,
    @SerializedName("featured_products") val featuredProducts: List<ProductCardDto>,
    @SerializedName("best_sellers") val bestSellers: List<ProductCardDto>,
    @SerializedName("new_arrivals") val newArrivals: List<ProductCardDto>
)

data class BannerDto(
    @SerializedName("id") val id: Long,
    @SerializedName("title") val title: String,
    @SerializedName("subtitle") val subtitle: String?,
    @SerializedName("image") val image: String?,
    @SerializedName("link_url") val linkUrl: String?
)

data class CategoryDto(
    @SerializedName("id") val id: Long,
    @SerializedName("name") val name: String,
    @SerializedName("slug") val slug: String,
    @SerializedName("image") val image: String?,
    @SerializedName("icon") val icon: String?,
    @SerializedName("sub_categories") val subCategories: List<SubCategoryDto>? = null
)

data class SubCategoryDto(
    @SerializedName("id") val id: Long,
    @SerializedName("name") val name: String,
    @SerializedName("slug") val slug: String,
    @SerializedName("image") val image: String?,
    @SerializedName("child_categories") val childCategories: List<ChildCategoryDto>? = null
)

data class ChildCategoryDto(
    @SerializedName("id") val id: Long,
    @SerializedName("name") val name: String,
    @SerializedName("slug") val slug: String,
    @SerializedName("image") val image: String?
)

data class CampaignDto(
    @SerializedName("id") val id: Long,
    @SerializedName("name") val name: String,
    @SerializedName("banner") val banner: String?,
    @SerializedName("discount_percentage") val discountPercentage: Double?
)

data class BrandDto(
    @SerializedName("id") val id: Long,
    @SerializedName("name") val name: String,
    @SerializedName("slug") val slug: String,
    @SerializedName("logo") val logo: String?
)

data class ProductCardDto(
    @SerializedName("id") val id: Long,
    @SerializedName("name") val name: String,
    @SerializedName("slug") val slug: String,
    @SerializedName("sku") val sku: String?,
    @SerializedName("thumbnail") val thumbnail: String?,
    @SerializedName("category_name") val categoryName: String?,
    @SerializedName("brand_name") val brandName: String?,
    @SerializedName("selling_price") val sellingPrice: Double,
    @SerializedName("discount_price") val discountPrice: Double?,
    @SerializedName("effective_price") val effectivePrice: Double,
    @SerializedName("discount_percentage") val discountPercentage: Int,
    @SerializedName("stock_quantity") val stockQuantity: Int,
    @SerializedName("in_stock") val inStock: Boolean,
    @SerializedName("has_variants") val hasVariants: Boolean,
    @SerializedName("warranty") val warranty: String?,
    @SerializedName("is_flash_sale") val isFlashSale: Boolean
)

// ==========================================
// Product Details DTO
// ==========================================
data class ProductDetailDto(
    @SerializedName("id") val id: Long,
    @SerializedName("name") val name: String,
    @SerializedName("slug") val slug: String,
    @SerializedName("sku") val sku: String?,
    @SerializedName("category") val category: CategoryDto?,
    @SerializedName("brand") val brand: BrandDto?,
    @SerializedName("selling_price") val sellingPrice: Double,
    @SerializedName("discount_price") val discountPrice: Double?,
    @SerializedName("effective_price") val effectivePrice: Double,
    @SerializedName("discount_percentage") val discountPercentage: Int,
    @SerializedName("stock_quantity") val stockQuantity: Int,
    @SerializedName("in_stock") val inStock: Boolean,
    @SerializedName("has_variants") val hasVariants: Boolean,
    @SerializedName("warranty") val warranty: String?,
    @SerializedName("short_description") val shortDescription: String?,
    @SerializedName("description") val description: String?,
    @SerializedName("specifications") val specifications: Map<String, Any>?,
    @SerializedName("images") val images: List<ProductImageDto>,
    @SerializedName("variants") val variants: List<ProductVariantDto>,
    @SerializedName("related_products") val relatedProducts: List<ProductCardDto>
)

data class ProductImageDto(
    @SerializedName("id") val id: Long,
    @SerializedName("image") val image: String,
    @SerializedName("is_primary") val isPrimary: Boolean
)

data class ProductVariantDto(
    @SerializedName("id") val id: Long,
    @SerializedName("name") val name: String?,
    @SerializedName("sku") val sku: String?,
    @SerializedName("color") val color: ColorDto?,
    @SerializedName("size") val size: SizeDto?,
    @SerializedName("selling_price") val sellingPrice: Double,
    @SerializedName("discount_price") val discountPrice: Double?,
    @SerializedName("effective_price") val effectivePrice: Double,
    @SerializedName("stock_quantity") val stockQuantity: Int,
    @SerializedName("image") val image: String?,
    @SerializedName("in_stock") val inStock: Boolean
)

data class ColorDto(
    @SerializedName("id") val id: Long,
    @SerializedName("name") val name: String,
    @SerializedName("code") val code: String
)

data class SizeDto(
    @SerializedName("id") val id: Long,
    @SerializedName("name") val name: String
)

// ==========================================
// Cart & Checkout DTOs
// ==========================================
data class DeliveryMethodDto(
    @SerializedName("id") val id: Long,
    @SerializedName("name") val name: String,
    @SerializedName("code") val code: String,
    @SerializedName("base_charge") val baseCharge: Double,
    @SerializedName("effective_charge") val effectiveCharge: Double,
    @SerializedName("is_free_delivery") val isFreeDelivery: Boolean,
    @SerializedName("estimated_days") val estimatedDays: String,
    @SerializedName("min_order_for_free_delivery") val minOrderForFreeDelivery: Double?,
    @SerializedName("description") val description: String?,
    @SerializedName("is_default") val isDefault: Boolean
)

data class CouponApplyResponse(
    @SerializedName("code") val code: String,
    @SerializedName("discount_type") val discountType: String,
    @SerializedName("discount_value") val discountValue: Double,
    @SerializedName("discount_amount") val discountAmount: Double,
    @SerializedName("new_subtotal") val newSubtotal: Double
)

data class OrderPlacementRequest(
    @SerializedName("name") val name: String,
    @SerializedName("phone") val phone: String,
    @SerializedName("email") val email: String?,
    @SerializedName("address") val address: String,
    @SerializedName("city") val city: String?,
    @SerializedName("delivery_method_code") val deliveryMethodCode: String?,
    @SerializedName("payment_method") val paymentMethod: String,
    @SerializedName("customer_note") val customerNote: String?,
    @SerializedName("coupon_code") val couponCode: String?,
    @SerializedName("items") val items: List<CartOrderItemDto>
)

data class CartOrderItemDto(
    @SerializedName("product_id") val productId: Long,
    @SerializedName("variant_id") val variantId: Long?,
    @SerializedName("quantity") val quantity: Int
)

data class OrderPlacedDto(
    @SerializedName("order_id") val orderId: Long,
    @SerializedName("order_no") val orderNo: String,
    @SerializedName("status") val status: String,
    @SerializedName("grand_total") val grandTotal: Double,
    @SerializedName("payment_method") val paymentMethod: String,
    @SerializedName("payment_status") val paymentStatus: String
)

// ==========================================
// Tracking & Warranties DTOs
// ==========================================
data class OrderTrackingDto(
    @SerializedName("order_no") val orderNo: String,
    @SerializedName("status") val status: String,
    @SerializedName("is_cancelled") val isCancelled: Boolean,
    @SerializedName("current_step") val currentStep: Int,
    @SerializedName("total_steps") val totalSteps: Int,
    @SerializedName("grand_total") val grandTotal: Double,
    @SerializedName("shipping_name") val shippingName: String?,
    @SerializedName("shipping_address") val shippingAddress: String?,
    @SerializedName("courier_name") val courierName: String?,
    @SerializedName("courier_tracking_id") val courierTrackingId: String?,
    @SerializedName("timeline") val timeline: List<TimelineMilestoneDto>
)

data class TimelineMilestoneDto(
    @SerializedName("step") val step: Int,
    @SerializedName("key") val key: String,
    @SerializedName("title") val title: String,
    @SerializedName("description") val description: String,
    @SerializedName("completed") val completed: Boolean,
    @SerializedName("is_current") val isCurrent: Boolean,
    @SerializedName("timestamp") val timestamp: String?
)

data class WarrantyDto(
    @SerializedName("id") val id: Long,
    @SerializedName("warranty_code") val warrantyCode: String,
    @SerializedName("serial_number") val serialNumber: String,
    @SerializedName("product_name") val productName: String,
    @SerializedName("product_thumbnail") val productThumbnail: String?,
    @SerializedName("warranty_period") val warrantyPeriod: String,
    @SerializedName("start_date") val startDate: String?,
    @SerializedName("end_date") val endDate: String?,
    @SerializedName("status") val status: String,
    @SerializedName("is_valid") val isValid: Boolean,
    @SerializedName("days_remaining") val daysRemaining: Int?
)

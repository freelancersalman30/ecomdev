package com.dreamerspcb.ecommerce.data.api

import com.dreamerspcb.ecommerce.data.model.*
import retrofit2.Response
import retrofit2.http.*

interface ApiService {

    // ==========================================
    // 1. Customer Authentication & Profile
    // ==========================================
    @POST("customer/register")
    suspend fun register(
        @Body request: Map<String, String>
    ): Response<AuthResponse>

    @POST("customer/login")
    suspend fun login(
        @Body request: Map<String, String>
    ): Response<AuthResponse>

    @POST("customer/logout")
    suspend fun logout(): Response<ApiResponse<Unit>>

    @GET("customer/profile")
    suspend fun getProfile(): Response<ApiResponse<CustomerDto>>

    @PUT("customer/profile")
    suspend fun updateProfile(
        @Body request: Map<String, String>
    ): Response<ApiResponse<CustomerDto>>

    @POST("customer/fcm-token")
    suspend fun updateFcmToken(
        @Body request: Map<String, String>
    ): Response<ApiResponse<Unit>>

    // ==========================================
    // 2. Catalog & Products
    // ==========================================
    @GET("home")
    suspend fun getHomeFeed(): Response<ApiResponse<HomeFeedDto>>

    @GET("categories")
    suspend fun getCategories(): Response<ApiResponse<List<CategoryDto>>>

    @GET("products")
    suspend fun getProducts(
        @Query("q") query: String? = null,
        @Query("category_id") categoryId: Long? = null,
        @Query("brand_id") brandId: Long? = null,
        @Query("min_price") minPrice: Double? = null,
        @Query("max_price") maxPrice: Double? = null,
        @Query("in_stock_only") inStockOnly: Boolean? = null,
        @Query("sort") sort: String? = "newest",
        @Query("page") page: Int = 1
    ): Response<ApiResponse<List<ProductCardDto>>>

    @GET("products/{slug_or_id}")
    suspend fun getProductDetail(
        @Path("slug_or_id") slugOrId: String
    ): Response<ApiResponse<ProductDetailDto>>

    // ==========================================
    // 3. Cart, Coupons & Shipping
    // ==========================================
    @POST("cart/validate")
    suspend fun validateCart(
        @Body request: Map<String, Any>
    ): Response<ApiResponse<Map<String, Any>>>

    @POST("coupon/apply")
    suspend fun applyCoupon(
        @Body request: Map<String, Any>
    ): Response<ApiResponse<CouponApplyResponse>>

    @GET("delivery-methods")
    suspend fun getDeliveryMethods(
        @Query("subtotal") subtotal: Double = 0.0
    ): Response<ApiResponse<List<DeliveryMethodDto>>>

    @POST("checkout/place-order")
    suspend fun placeOrder(
        @Body request: OrderPlacementRequest
    ): Response<ApiResponse<OrderPlacedDto>>

    // ==========================================
    // 4. Orders & Realtime Tracking
    // ==========================================
    @GET("customer/orders")
    suspend fun getCustomerOrders(): Response<ApiResponse<List<Map<String, Any>>>>

    @GET("customer/orders/{order_no}")
    suspend fun getOrderDetail(
        @Path("order_no") orderNo: String
    ): Response<ApiResponse<Map<String, Any>>>

    @GET("track-order")
    suspend fun trackOrder(
        @Query("order_no") orderNo: String,
        @Query("phone") phone: String? = null
    ): Response<ApiResponse<OrderTrackingDto>>

    // ==========================================
    // 5. Warranties & Claims
    // ==========================================
    @GET("customer/warranties")
    suspend fun getCustomerWarranties(): Response<ApiResponse<List<WarrantyDto>>>

    @GET("warranty/verify")
    suspend fun verifyWarranty(
        @Query("serial_no") serialNo: String
    ): Response<ApiResponse<WarrantyDto>>
}

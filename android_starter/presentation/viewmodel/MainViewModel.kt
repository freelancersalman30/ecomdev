package com.dreamerspcb.ecommerce.presentation.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.dreamerspcb.ecommerce.data.api.ApiService
import com.dreamerspcb.ecommerce.data.local.CartDao
import com.dreamerspcb.ecommerce.data.local.CartEntity
import com.dreamerspcb.ecommerce.data.model.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class MainViewModel @Inject constructor(
    private val apiService: ApiService,
    private val cartDao: CartDao
) : ViewModel() {

    // Home Feed State
    private val _homeFeed = MutableStateFlow<HomeFeedDto?>(null)
    val homeFeed: StateFlow<HomeFeedDto?> = _homeFeed.asStateFlow()

    private val _isLoadingHome = MutableStateFlow(false)
    val isLoadingHome: StateFlow<Boolean> = _isLoadingHome.asStateFlow()

    // Product Detail State
    private val _currentProduct = MutableStateFlow<ProductDetailDto?>(null)
    val currentProduct: StateFlow<ProductDetailDto?> = _currentProduct.asStateFlow()

    private val _isLoadingDetail = MutableStateFlow(false)
    val isLoadingDetail: StateFlow<Boolean> = _isLoadingDetail.asStateFlow()

    // Delivery Methods State
    private val _deliveryMethods = MutableStateFlow<List<DeliveryMethodDto>>(emptyList())
    val deliveryMethods: StateFlow<List<DeliveryMethodDto>> = _deliveryMethods.asStateFlow()

    // Coupon State
    private val _couponDiscount = MutableStateFlow(0.0)
    val couponDiscount: StateFlow<Double> = _couponDiscount.asStateFlow()

    private val _appliedCouponCode = MutableStateFlow<String?>(null)
    val appliedCouponCode: StateFlow<String?> = _appliedCouponCode.asStateFlow()

    // Tracking State
    private val _trackingData = MutableStateFlow<OrderTrackingDto?>(null)
    val trackingData: StateFlow<OrderTrackingDto?> = _trackingData.asStateFlow()

    private val _isLoadingTracking = MutableStateFlow(false)
    val isLoadingTracking: StateFlow<Boolean> = _isLoadingTracking.asStateFlow()

    // Scanned Warranty State
    private val _scannedWarranty = MutableStateFlow<WarrantyDto?>(null)
    val scannedWarranty: StateFlow<WarrantyDto?> = _scannedWarranty.asStateFlow()

    private val _isLoadingWarranty = MutableStateFlow(false)
    val isLoadingWarranty: StateFlow<Boolean> = _isLoadingWarranty.asStateFlow()

    // Shopping Cart Stream
    val cartItems: StateFlow<List<CartEntity>> = cartDao.getAllCartItems()
        .stateIn(viewModelScope, SharingStarted.WhileSubscribed(5000), emptyList())

    init {
        loadHomeFeed()
        loadDeliveryMethods()
    }

    fun loadHomeFeed() {
        viewModelScope.launch {
            _isLoadingHome.value = true
            try {
                val res = apiService.getHomeFeed()
                if (res.isSuccessful && res.body()?.success == true) {
                    _homeFeed.value = res.body()?.data
                }
            } catch (e: Exception) {
                e.printStackTrace()
            } finally {
                _isLoadingHome.value = false
            }
        }
    }

    fun loadProductDetail(slugOrId: String) {
        viewModelScope.launch {
            _isLoadingDetail.value = true
            try {
                val res = apiService.getProductDetail(slugOrId)
                if (res.isSuccessful && res.body()?.success == true) {
                    _currentProduct.value = res.body()?.data
                }
            } catch (e: Exception) {
                e.printStackTrace()
            } finally {
                _isLoadingDetail.value = false
            }
        }
    }

    fun loadDeliveryMethods(subtotal: Double = 0.0) {
        viewModelScope.launch {
            try {
                val res = apiService.getDeliveryMethods(subtotal)
                if (res.isSuccessful && res.body()?.success == true) {
                    _deliveryMethods.value = res.body()?.data ?: emptyList()
                }
            } catch (e: Exception) {
                e.printStackTrace()
            }
        }
    }

    fun addToCart(product: ProductDetailDto, variant: ProductVariantDto?, qty: Int) {
        viewModelScope.launch {
            val price = variant?.effectivePrice ?: product.effectivePrice
            val name = product.name
            val variantName = variant?.name
            val thumbnail = variant?.image ?: product.images.firstOrNull()?.image

            cartDao.insertItem(
                CartEntity(
                    productId = product.id,
                    variantId = variant?.id,
                    name = name,
                    variantName = variantName,
                    sku = variant?.sku ?: product.sku,
                    thumbnail = thumbnail,
                    unitPrice = price,
                    quantity = qty
                )
            )
        }
    }

    fun updateCartQuantity(item: CartEntity, newQty: Int) {
        viewModelScope.launch {
            if (newQty > 0) {
                cartDao.updateItem(item.copy(quantity = newQty))
            } else {
                cartDao.deleteItem(item)
            }
        }
    }

    fun removeCartItem(item: CartEntity) {
        viewModelScope.launch {
            cartDao.deleteItem(item)
        }
    }

    fun applyCoupon(code: String, subtotal: Double) {
        viewModelScope.launch {
            try {
                val res = apiService.applyCoupon(mapOf("code" to code, "subtotal" to subtotal))
                if (res.isSuccessful && res.body()?.success == true) {
                    _couponDiscount.value = res.body()?.data?.discountAmount ?: 0.0
                    _appliedCouponCode.value = code
                }
            } catch (e: Exception) {
                e.printStackTrace()
            }
        }
    }

    fun trackOrder(orderNo: String, phone: String?) {
        viewModelScope.launch {
            _isLoadingTracking.value = true
            try {
                val res = apiService.trackOrder(orderNo, phone)
                if (res.isSuccessful && res.body()?.success == true) {
                    _trackingData.value = res.body()?.data
                }
            } catch (e: Exception) {
                e.printStackTrace()
            } finally {
                _isLoadingTracking.value = false
            }
        }
    }

    fun verifyWarranty(serial: String) {
        viewModelScope.launch {
            _isLoadingWarranty.value = true
            try {
                val res = apiService.verifyWarranty(serial)
                if (res.isSuccessful && res.body()?.success == true) {
                    _scannedWarranty.value = res.body()?.data
                }
            } catch (e: Exception) {
                e.printStackTrace()
            } finally {
                _isLoadingWarranty.value = false
            }
        }
    }
}

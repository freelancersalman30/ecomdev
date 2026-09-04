package com.dreamerspcb.ecommerce

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.viewModels
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.padding
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.currentBackStackEntryAsState
import androidx.navigation.compose.rememberNavController
import com.dreamerspcb.ecommerce.presentation.navigation.Screen
import com.dreamerspcb.ecommerce.presentation.ui.*
import com.dreamerspcb.ecommerce.presentation.viewmodel.MainViewModel
import dagger.hilt.android.AndroidEntryPoint

@AndroidEntryPoint
class MainActivity : ComponentActivity() {

    private val viewModel: MainViewModel by viewModels()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContent {
            MaterialTheme {
                val navController = rememberNavController()
                val navBackStackEntry by navController.currentBackStackEntryAsState()
                val currentRoute = navBackStackEntry?.destination?.route

                val homeFeed by viewModel.homeFeed.collectAsState()
                val isLoadingHome by viewModel.isLoadingHome.collectAsState()
                val currentProduct by viewModel.currentProduct.collectAsState()
                val isLoadingProduct by viewModel.isLoadingDetail.collectAsState()
                val cartItems by viewModel.cartItems.collectAsState()
                val deliveryMethods by viewModel.deliveryMethods.collectAsState()
                val couponDiscount by viewModel.couponDiscount.collectAsState()
                val appliedCouponCode by viewModel.appliedCouponCode.collectAsState()
                val trackingData by viewModel.trackingData.collectAsState()
                val isLoadingTracking by viewModel.isLoadingTracking.collectAsState()
                val scannedWarranty by viewModel.scannedWarranty.collectAsState()
                val isLoadingWarranty by viewModel.isLoadingWarranty.collectAsState()

                Scaffold(
                    bottomBar = {
                        if (currentRoute in listOf(Screen.Home.route, Screen.Cart.route, Screen.TrackOrder.route, Screen.Profile.route)) {
                            NavigationBar {
                                NavigationBarItem(
                                    icon = { Icon(Icons.Default.Home, contentDescription = "Home") },
                                    label = { Text("Home") },
                                    selected = currentRoute == Screen.Home.route,
                                    onClick = { navController.navigate(Screen.Home.route) }
                                )
                                NavigationBarItem(
                                    icon = {
                                        BadgedBox(badge = { if (cartItems.isNotEmpty()) Badge { Text("${cartItems.size}") } }) {
                                            Icon(Icons.Default.ShoppingCart, contentDescription = "Cart")
                                        }
                                    },
                                    label = { Text("Cart") },
                                    selected = currentRoute == Screen.Cart.route,
                                    onClick = { navController.navigate(Screen.Cart.route) }
                                )
                                NavigationBarItem(
                                    icon = { Icon(Icons.Default.LocalShipping, contentDescription = "Track") },
                                    label = { Text("Track") },
                                    selected = currentRoute == Screen.TrackOrder.route,
                                    onClick = { navController.navigate(Screen.TrackOrder.route) }
                                )
                                NavigationBarItem(
                                    icon = { Icon(Icons.Default.Person, contentDescription = "Account") },
                                    label = { Text("Account") },
                                    selected = currentRoute == Screen.Profile.route,
                                    onClick = { navController.navigate(Screen.Profile.route) }
                                )
                            }
                        }
                    }
                ) { innerPadding ->
                    NavHost(
                        navController = navController,
                        startDestination = Screen.Home.route,
                        modifier = Modifier.fillMaxSize().padding(innerPadding)
                    ) {
                        composable(Screen.Home.route) {
                            HomeScreen(
                                homeFeed = homeFeed,
                                isLoading = isLoadingHome,
                                onSearchClick = { /* Live search */ },
                                onCartClick = { navController.navigate(Screen.Cart.route) },
                                onScannerClick = { navController.navigate(Screen.BarcodeScanner.route) },
                                onProductClick = { slug ->
                                    viewModel.loadProductDetail(slug)
                                    navController.navigate(Screen.ProductDetail.createRoute(slug))
                                },
                                onCategoryClick = { /* Category browse */ }
                            )
                        }

                        composable(Screen.ProductDetail.route) {
                            ProductDetailScreen(
                                product = currentProduct,
                                isLoading = isLoadingProduct,
                                onBackClick = { navController.popBackStack() },
                                onAddToCartClick = { prod, variant, qty ->
                                    viewModel.addToCart(prod, variant, qty)
                                },
                                onBuyNowClick = { prod, variant, qty ->
                                    viewModel.addToCart(prod, variant, qty)
                                    navController.navigate(Screen.Checkout.route)
                                }
                            )
                        }

                        composable(Screen.Cart.route) {
                            CartScreen(
                                cartItems = cartItems,
                                onBackClick = { navController.popBackStack() },
                                onQuantityChange = { item, qty -> viewModel.updateCartQuantity(item, qty) },
                                onRemoveItem = { item -> viewModel.removeCartItem(item) },
                                onProceedToCheckout = { navController.navigate(Screen.Checkout.route) }
                            )
                        }

                        composable(Screen.Checkout.route) {
                            CheckoutScreen(
                                cartItems = cartItems,
                                deliveryMethods = deliveryMethods,
                                isSubmitting = false,
                                onBackClick = { navController.popBackStack() },
                                onApplyCoupon = { code, subtotal -> viewModel.applyCoupon(code, subtotal) },
                                couponDiscount = couponDiscount,
                                appliedCouponCode = appliedCouponCode,
                                onPlaceOrder = { req ->
                                    // Order placed callback
                                    navController.navigate(Screen.Home.route)
                                }
                            )
                        }

                        composable(Screen.TrackOrder.route) {
                            TrackOrderScreen(
                                trackingData = trackingData,
                                isLoading = isLoadingTracking,
                                onBackClick = { navController.popBackStack() },
                                onSearchOrder = { orderNo, phone -> viewModel.trackOrder(orderNo, phone) }
                            )
                        }

                        composable(Screen.BarcodeScanner.route) {
                            WarrantyScannerScreen(
                                scannedWarranty = scannedWarranty,
                                isLoading = isLoadingWarranty,
                                onBackClick = { navController.popBackStack() },
                                onManualSearch = { serial -> viewModel.verifyWarranty(serial) },
                                onClaimWarranty = { /* Claim screen */ }
                            )
                        }

                        composable(Screen.Profile.route) {
                            ProfileScreen(
                                customer = null,
                                onLogoutClick = { navController.navigate(Screen.Home.route) },
                                onOrdersClick = { navController.navigate(Screen.TrackOrder.route) },
                                onWarrantiesClick = { navController.navigate(Screen.BarcodeScanner.route) }
                            )
                        }
                    }
                }
            }
        }
    }
}

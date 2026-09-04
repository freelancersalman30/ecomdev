package com.dreamerspcb.ecommerce.presentation.navigation

sealed class Screen(val route: String) {
    object Splash : Screen("splash")
    object Login : Screen("login")
    object Register : Screen("register")
    object Main : Screen("main")
    object Home : Screen("home")
    object Categories : Screen("categories")
    object ProductDetail : Screen("product_detail/{slug}") {
        fun createRoute(slug: String) = "product_detail/$slug"
    }
    object Cart : Screen("cart")
    object Checkout : Screen("checkout")
    object OrderSuccess : Screen("order_success/{order_no}") {
        fun createRoute(orderNo: String) = "order_success/$orderNo"
    }
    object TrackOrder : Screen("track_order")
    object Profile : Screen("profile")
    object Warranties : Screen("warranties")
    object BarcodeScanner : Screen("barcode_scanner")
}

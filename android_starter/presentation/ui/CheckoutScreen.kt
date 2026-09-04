package com.dreamerspcb.ecommerce.presentation.ui

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dreamerspcb.ecommerce.data.local.CartEntity
import com.dreamerspcb.ecommerce.data.model.DeliveryMethodDto
import com.dreamerspcb.ecommerce.data.model.OrderPlacementRequest
import com.dreamerspcb.ecommerce.data.model.CartOrderItemDto

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun CheckoutScreen(
    cartItems: List<CartEntity>,
    deliveryMethods: List<DeliveryMethodDto>,
    isSubmitting: Boolean,
    onBackClick: () -> Unit,
    onApplyCoupon: (String, Double) -> Unit,
    couponDiscount: Double,
    appliedCouponCode: String?,
    onPlaceOrder: (OrderPlacementRequest) -> Unit
) {
    var name by remember { mutableStateOf("") }
    var phone by remember { mutableStateOf("") }
    var address by remember { mutableStateOf("") }
    var city by remember { mutableStateOf("Dhaka") }
    var couponInput by remember { mutableStateOf("") }
    var selectedDeliveryCode by remember { mutableStateOf(deliveryMethods.firstOrNull { it.isDefault }?.code ?: "inside_dhaka") }
    var paymentMethod by remember { mutableStateOf("cod") }

    val subtotal = cartItems.sumOf { it.unitPrice * it.quantity }
    val selectedDelivery = deliveryMethods.firstOrNull { it.code == selectedDeliveryCode }
    val shippingCharge = selectedDelivery?.effectiveCharge ?: 60.0
    val grandTotal = maxOf(0.0, (subtotal - couponDiscount) + shippingCharge)

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text(text = "Checkout & Payment", fontSize = 16.sp, fontWeight = FontWeight.Bold) },
                navigationIcon = {
                    IconButton(onClick = onBackClick) {
                        Icon(imageVector = Icons.Default.ArrowBack, contentDescription = "Back")
                    }
                }
            )
        },
        bottomBar = {
            Surface(
                shadowElevation = 8.dp,
                color = MaterialTheme.colorScheme.surface
            ) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .navigationBarsPadding()
                        .padding(16.dp)
                ) {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween
                    ) {
                        Text(text = "Total Payable Amount", fontSize = 14.sp, fontWeight = FontWeight.SemiBold)
                        Text(text = "৳$grandTotal", fontSize = 18.sp, fontWeight = FontWeight.Black, color = MaterialTheme.colorScheme.primary)
                    }
                    Spacer(modifier = Modifier.height(10.dp))
                    Button(
                        onClick = {
                            if (name.isNotBlank() && phone.isNotBlank() && address.isNotBlank()) {
                                onPlaceOrder(
                                    OrderPlacementRequest(
                                        name = name,
                                        phone = phone,
                                        email = null,
                                        address = address,
                                        city = city,
                                        deliveryMethodCode = selectedDeliveryCode,
                                        paymentMethod = paymentMethod,
                                        customerNote = null,
                                        couponCode = appliedCouponCode,
                                        items = cartItems.map {
                                            CartOrderItemDto(
                                                productId = it.productId,
                                                variantId = it.variantId,
                                                quantity = it.quantity
                                            )
                                        }
                                    )
                                )
                            }
                        },
                        enabled = !isSubmitting && name.isNotBlank() && phone.isNotBlank() && address.isNotBlank(),
                        modifier = Modifier.fillMaxWidth().height(48.dp),
                        shape = RoundedCornerShape(12.dp)
                    ) {
                        if (isSubmitting) {
                            CircularProgressIndicator(color = Color.White, modifier = Modifier.size(20.dp))
                        } else {
                            Text(text = "Confirm Order (৳$grandTotal)", fontSize = 14.sp, fontWeight = FontWeight.Black)
                        }
                    }
                }
            }
        }
    ) { padding ->
        LazyColumn(
            modifier = Modifier.fillMaxSize().padding(padding),
            contentPadding = PaddingValues(16.dp),
            verticalArrangement = Arrangement.spacedBy(14.dp)
        ) {
            // 1. Shipping Details
            item {
                Card(shape = RoundedCornerShape(12.dp)) {
                    Column(modifier = Modifier.padding(14.dp)) {
                        Text(text = "📍 Shipping Details", fontSize = 14.sp, fontWeight = FontWeight.Bold)
                        Spacer(modifier = Modifier.height(10.dp))
                        OutlinedTextField(
                            value = name,
                            onValueChange = { name = it },
                            label = { Text("Full Name *") },
                            modifier = Modifier.fillMaxWidth(),
                            singleLine = true
                        )
                        Spacer(modifier = Modifier.height(8.dp))
                        OutlinedTextField(
                            value = phone,
                            onValueChange = { phone = it },
                            label = { Text("Phone Number (01XXXXXXXXX) *") },
                            modifier = Modifier.fillMaxWidth(),
                            singleLine = true
                        )
                        Spacer(modifier = Modifier.height(8.dp))
                        OutlinedTextField(
                            value = address,
                            onValueChange = { address = it },
                            label = { Text("Full Delivery Address (House/Road/Area) *") },
                            modifier = Modifier.fillMaxWidth(),
                            maxLines = 3
                        )
                    }
                }
            }

            // 2. Delivery Zone Selection
            item {
                Card(shape = RoundedCornerShape(12.dp)) {
                    Column(modifier = Modifier.padding(14.dp)) {
                        Text(text = "🚚 Delivery Method", fontSize = 14.sp, fontWeight = FontWeight.Bold)
                        Spacer(modifier = Modifier.height(10.dp))
                        deliveryMethods.forEach { method ->
                            val isSelected = selectedDeliveryCode == method.code
                            Row(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .clip(RoundedCornerShape(8.dp))
                                    .border(
                                        width = if (isSelected) 1.5.dp else 1.dp,
                                        color = if (isSelected) MaterialTheme.colorScheme.primary else Color.LightGray,
                                        shape = RoundedCornerShape(8.dp)
                                    )
                                    .clickable { selectedDeliveryCode = method.code }
                                    .padding(12.dp),
                                horizontalArrangement = Arrangement.SpaceBetween,
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Row(verticalAlignment = Alignment.CenterVertically) {
                                    RadioButton(selected = isSelected, onClick = { selectedDeliveryCode = method.code })
                                    Spacer(modifier = Modifier.width(6.dp))
                                    Column {
                                        Text(text = method.name, fontSize = 13.sp, fontWeight = FontWeight.SemiBold)
                                        Text(text = method.estimatedDays, fontSize = 11.sp, color = Color.Gray)
                                    }
                                }
                                Text(
                                    text = if (method.effectiveCharge == 0.0) "FREE" else "৳${method.effectiveCharge}",
                                    fontSize = 13.sp,
                                    fontWeight = FontWeight.Bold,
                                    color = if (method.effectiveCharge == 0.0) Color(0xFF15803D) else MaterialTheme.colorScheme.primary
                                )
                            }
                            Spacer(modifier = Modifier.height(6.dp))
                        }
                    }
                }
            }

            // 3. Coupon Code Application
            item {
                Card(shape = RoundedCornerShape(12.dp)) {
                    Column(modifier = Modifier.padding(14.dp)) {
                        Text(text = "🏷️ Promo Coupon Code", fontSize = 14.sp, fontWeight = FontWeight.Bold)
                        Spacer(modifier = Modifier.height(8.dp))
                        Row(modifier = Modifier.fillMaxWidth()) {
                            OutlinedTextField(
                                value = couponInput,
                                onValueChange = { couponInput = it },
                                placeholder = { Text("Enter coupon (e.g. PCB100)") },
                                modifier = Modifier.weight(1f),
                                singleLine = true
                            )
                            Spacer(modifier = Modifier.width(8.dp))
                            Button(
                                onClick = { onApplyCoupon(couponInput, subtotal) },
                                modifier = Modifier.height(56.dp),
                                shape = RoundedCornerShape(8.dp)
                            ) {
                                Text("Apply")
                            }
                        }
                        if (couponDiscount > 0) {
                            Spacer(modifier = Modifier.height(6.dp))
                            Text(
                                text = "✓ Coupon '$appliedCouponCode' applied! -৳$couponDiscount",
                                fontSize = 12.sp,
                                fontWeight = FontWeight.Bold,
                                color = Color(0xFF15803D)
                            )
                        }
                    }
                }
            }

            // 4. Payment Method Selection
            item {
                Card(shape = RoundedCornerShape(12.dp)) {
                    Column(modifier = Modifier.padding(14.dp)) {
                        Text(text = "💳 Payment Option", fontSize = 14.sp, fontWeight = FontWeight.Bold)
                        Spacer(modifier = Modifier.height(8.dp))
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            RadioButton(selected = paymentMethod == "cod", onClick = { paymentMethod = "cod" })
                            Spacer(modifier = Modifier.width(6.dp))
                            Text(text = "Cash on Delivery (COD)", fontSize = 13.sp, fontWeight = FontWeight.SemiBold)
                        }
                    }
                }
            }

            // 5. Pricing Summary Breakdown
            item {
                Card(shape = RoundedCornerShape(12.dp)) {
                    Column(modifier = Modifier.padding(14.dp)) {
                        Text(text = "Order Summary", fontSize = 14.sp, fontWeight = FontWeight.Bold)
                        Spacer(modifier = Modifier.height(8.dp))
                        SummaryRow(label = "Subtotal (${cartItems.size} items)", value = "৳$subtotal")
                        if (couponDiscount > 0) {
                            SummaryRow(label = "Coupon Discount", value = "-৳$couponDiscount", isDiscount = true)
                        }
                        SummaryRow(label = "Shipping Charge", value = if (shippingCharge == 0.0) "FREE" else "৳$shippingCharge")
                        Divider(modifier = Modifier.padding(vertical = 8.dp))
                        SummaryRow(label = "Total Payable", value = "৳$grandTotal", isBold = true)
                    }
                }
            }
        }
    }
}

@Composable
fun SummaryRow(label: String, value: String, isDiscount: Boolean = false, isBold: Boolean = false) {
    Row(
        modifier = Modifier.fillMaxWidth().padding(vertical = 3.dp),
        horizontalArrangement = Arrangement.SpaceBetween
    ) {
        Text(text = label, fontSize = 13.sp, fontWeight = if (isBold) FontWeight.Bold else FontWeight.Normal, color = if (isBold) Color.Black else Color.Gray)
        Text(
            text = value,
            fontSize = if (isBold) 15.sp else 13.sp,
            fontWeight = if (isBold) FontWeight.Black else FontWeight.SemiBold,
            color = if (isDiscount) Color(0xFF15803D) else if (isBold) MaterialTheme.colorScheme.primary else Color.Black
        )
    }
}

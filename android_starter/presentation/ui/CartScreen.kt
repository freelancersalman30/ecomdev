package com.dreamerspcb.ecommerce.presentation.ui

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil.compose.AsyncImage
import com.dreamerspcb.ecommerce.data.local.CartEntity

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun CartScreen(
    cartItems: List<CartEntity>,
    onBackClick: () -> Unit,
    onQuantityChange: (CartEntity, Int) -> Unit,
    onRemoveItem: (CartEntity) -> Unit,
    onProceedToCheckout: () -> Unit
) {
    val subtotal = cartItems.sumOf { it.unitPrice * it.quantity }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text(text = "My Shopping Cart (${cartItems.size})", fontSize = 16.sp, fontWeight = FontWeight.Bold) },
                navigationIcon = {
                    IconButton(onClick = onBackClick) {
                        Icon(imageVector = Icons.Default.ArrowBack, contentDescription = "Back")
                    }
                }
            )
        },
        bottomBar = {
            if (cartItems.isNotEmpty()) {
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
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text(text = "Estimated Subtotal", fontSize = 14.sp, color = Color.Gray)
                            Text(text = "৳$subtotal", fontSize = 18.sp, fontWeight = FontWeight.Black, color = MaterialTheme.colorScheme.primary)
                        }
                        Spacer(modifier = Modifier.height(12.dp))
                        Button(
                            onClick = onProceedToCheckout,
                            modifier = Modifier.fillMaxWidth().height(48.dp),
                            shape = RoundedCornerShape(12.dp)
                        ) {
                            Text(text = "Proceed to Checkout", fontSize = 14.sp, fontWeight = FontWeight.Bold)
                            Spacer(modifier = Modifier.width(8.dp))
                            Icon(imageVector = Icons.Default.ArrowForward, contentDescription = null, modifier = Modifier.size(18.dp))
                        }
                    }
                }
            }
        }
    ) { padding ->
        if (cartItems.isEmpty()) {
            Box(
                modifier = Modifier.fillMaxSize().padding(padding),
                contentAlignment = Alignment.Center
            ) {
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    Icon(
                        imageVector = Icons.Default.RemoveShoppingCart,
                        contentDescription = null,
                        modifier = Modifier.size(72.dp),
                        tint = Color.LightGray
                    )
                    Spacer(modifier = Modifier.height(12.dp))
                    Text(text = "Your Cart is Empty", fontSize = 16.sp, fontWeight = FontWeight.Bold)
                    Text(text = "Explore products and add items to your cart", fontSize = 13.sp, color = Color.Gray)
                }
            }
        } else {
            LazyColumn(
                modifier = Modifier.fillMaxSize().padding(padding),
                contentPadding = PaddingValues(16.dp),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                items(cartItems, key = { it.id }) { item ->
                    Card(
                        shape = RoundedCornerShape(12.dp),
                        elevation = CardDefaults.cardElevation(defaultElevation = 1.dp)
                    ) {
                        Row(
                            modifier = Modifier.fillMaxWidth().padding(12.dp),
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            AsyncImage(
                                model = item.thumbnail,
                                contentDescription = item.name,
                                modifier = Modifier
                                    .size(70.dp)
                                    .clip(RoundedCornerShape(8.dp))
                                    .background(Color(0xFFF1F5F9)),
                                contentScale = ContentScale.Crop
                            )
                            Spacer(modifier = Modifier.width(12.dp))
                            Column(modifier = Modifier.weight(1f)) {
                                Text(text = item.name, fontSize = 13.sp, fontWeight = FontWeight.SemiBold, maxLines = 1)
                                if (!item.variantName.isNullOrEmpty()) {
                                    Text(text = "Variant: ${item.variantName}", fontSize = 11.sp, color = Color.Gray)
                                }
                                Text(
                                    text = "৳${item.unitPrice}",
                                    fontSize = 14.sp,
                                    fontWeight = FontWeight.Bold,
                                    color = MaterialTheme.colorScheme.primary
                                )
                            }

                            // Quantity Adjuster
                            Row(
                                modifier = Modifier
                                    .clip(RoundedCornerShape(8.dp))
                                    .background(MaterialTheme.colorScheme.surfaceVariant.copy(alpha = 0.5f)),
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                IconButton(
                                    onClick = {
                                        if (item.quantity > 1) onQuantityChange(item, item.quantity - 1)
                                        else onRemoveItem(item)
                                    },
                                    modifier = Modifier.size(28.dp)
                                ) {
                                    Icon(imageVector = if (item.quantity > 1) Icons.Default.Remove else Icons.Default.Delete, contentDescription = null, modifier = Modifier.size(14.dp))
                                }
                                Text(text = "${item.quantity}", fontSize = 12.sp, fontWeight = FontWeight.Bold, modifier = Modifier.padding(horizontal = 4.dp))
                                IconButton(
                                    onClick = { onQuantityChange(item, item.quantity + 1) },
                                    modifier = Modifier.size(28.dp)
                                ) {
                                    Icon(imageVector = Icons.Default.Add, contentDescription = null, modifier = Modifier.size(14.dp))
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

package com.dreamerspcb.ecommerce.presentation.ui

import androidx.compose.foundation.*
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
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
import androidx.compose.ui.text.style.TextDecoration
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil.compose.AsyncImage
import com.dreamerspcb.ecommerce.data.model.ProductDetailDto
import com.dreamerspcb.ecommerce.data.model.ProductVariantDto

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ProductDetailScreen(
    product: ProductDetailDto?,
    isLoading: Boolean,
    onBackClick: () -> Unit,
    onAddToCartClick: (ProductDetailDto, ProductVariantDto?, Int) -> Unit,
    onBuyNowClick: (ProductDetailDto, ProductVariantDto?, Int) -> Unit
) {
    var selectedVariant by remember { mutableStateOf<ProductVariantDto?>(null) }
    var quantity by remember { mutableIntStateOf(1) }
    var selectedImageIndex by remember { mutableIntStateOf(0) }

    // Initialize default variant if available
    LaunchedEffect(product) {
        if (product != null && product.variants.isNotEmpty()) {
            selectedVariant = product.variants.firstOrNull { it.inStock } ?: product.variants.first()
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text(text = "Product Details", fontSize = 16.sp, fontWeight = FontWeight.Bold) },
                navigationIcon = {
                    IconButton(onClick = onBackClick) {
                        Icon(imageVector = Icons.Default.ArrowBack, contentDescription = "Back")
                    }
                },
                actions = {
                    IconButton(onClick = { /* Share product link */ }) {
                        Icon(imageVector = Icons.Default.Share, contentDescription = "Share")
                    }
                }
            )
        },
        bottomBar = {
            if (product != null) {
                Surface(
                    shadowElevation = 8.dp,
                    color = MaterialTheme.colorScheme.surface
                ) {
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .navigationBarsPadding()
                            .padding(horizontal = 16.dp, vertical = 10.dp),
                        horizontalArrangement = Arrangement.spacedBy(10.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        // Quantity Adjuster
                        Row(
                            modifier = Modifier
                                .clip(RoundedCornerShape(12.dp))
                                .background(MaterialTheme.colorScheme.surfaceVariant.copy(alpha = 0.5f))
                                .padding(horizontal = 4.dp, vertical = 2.dp),
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            IconButton(
                                onClick = { if (quantity > 1) quantity-- },
                                modifier = Modifier.size(32.dp)
                            ) {
                                Icon(imageVector = Icons.Default.Remove, contentDescription = "Decrease", modifier = Modifier.size(16.dp))
                            }
                            Text(text = "$quantity", fontWeight = FontWeight.Bold, fontSize = 14.sp, modifier = Modifier.padding(horizontal = 6.dp))
                            IconButton(
                                onClick = { quantity++ },
                                modifier = Modifier.size(32.dp)
                            ) {
                                Icon(imageVector = Icons.Default.Add, contentDescription = "Increase", modifier = Modifier.size(16.dp))
                            }
                        }

                        // Add to Cart Button
                        OutlinedButton(
                            onClick = { onAddToCartClick(product, selectedVariant, quantity) },
                            modifier = Modifier.weight(1f).height(46.dp),
                            shape = RoundedCornerShape(12.dp),
                            border = BorderStroke(1.5.dp, MaterialTheme.colorScheme.primary)
                        ) {
                            Icon(imageVector = Icons.Default.AddShoppingCart, contentDescription = null, modifier = Modifier.size(18.dp))
                            Spacer(modifier = Modifier.width(6.dp))
                            Text(text = "Add to Cart", fontSize = 13.sp, fontWeight = FontWeight.Bold)
                        }

                        // Buy Now Button
                        Button(
                            onClick = { onBuyNowClick(product, selectedVariant, quantity) },
                            modifier = Modifier.weight(1f).height(46.dp),
                            shape = RoundedCornerShape(12.dp),
                            colors = ButtonDefaults.buttonColors(containerColor = MaterialTheme.colorScheme.primary)
                        ) {
                            Text(text = "Buy Now", fontSize = 13.sp, fontWeight = FontWeight.Black)
                        }
                    }
                }
            }
        }
    ) { padding ->
        if (isLoading) {
            Box(modifier = Modifier.fillMaxSize().padding(padding), contentAlignment = Alignment.Center) {
                CircularProgressIndicator()
            }
        } else if (product != null) {
            val effectivePrice = selectedVariant?.effectivePrice ?: product.effectivePrice
            val regularPrice = selectedVariant?.sellingPrice ?: product.sellingPrice
            val inStock = selectedVariant?.inStock ?: product.inStock

            LazyColumn(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(padding),
                contentPadding = PaddingValues(bottom = 20.dp)
            ) {
                // 1. Gallery Image Display
                item {
                    val currentImg = product.images.getOrNull(selectedImageIndex)?.image
                    Box(
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(280.dp)
                            .background(Color(0xFFF8FAFC)),
                        contentAlignment = Alignment.Center
                    ) {
                        AsyncImage(
                            model = currentImg,
                            contentDescription = product.name,
                            modifier = Modifier.fillMaxSize().padding(16.dp),
                            contentScale = ContentScale.Fit
                        )
                    }

                    // Thumbnail selector
                    if (product.images.size > 1) {
                        LazyRow(
                            modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp, vertical = 8.dp),
                            horizontalArrangement = Arrangement.spacedBy(8.dp)
                        ) {
                            items(product.images.indices.toList()) { index ->
                                Box(
                                    modifier = Modifier
                                        .size(54.dp)
                                        .clip(RoundedCornerShape(8.dp))
                                        .border(
                                            width = if (selectedImageIndex == index) 2.dp else 1.dp,
                                            color = if (selectedImageIndex == index) MaterialTheme.colorScheme.primary else Color.LightGray,
                                            shape = RoundedCornerShape(8.dp)
                                        )
                                        .clickable { selectedImageIndex = index }
                                        .padding(4.dp)
                                ) {
                                    AsyncImage(
                                        model = product.images[index].image,
                                        contentDescription = null,
                                        modifier = Modifier.fillMaxSize(),
                                        contentScale = ContentScale.Fit
                                    )
                                }
                            }
                        }
                    }
                }

                // 2. Pricing & Title
                item {
                    Column(modifier = Modifier.padding(16.dp)) {
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Row(verticalAlignment = Alignment.Bottom) {
                                Text(
                                    text = "৳$effectivePrice",
                                    fontSize = 24.sp,
                                    fontWeight = FontWeight.Black,
                                    color = MaterialTheme.colorScheme.primary
                                )
                                if (regularPrice > effectivePrice) {
                                    Spacer(modifier = Modifier.width(8.dp))
                                    Text(
                                        text = "৳$regularPrice",
                                        fontSize = 15.sp,
                                        textDecoration = TextDecoration.LineThrough,
                                        color = Color.Gray
                                    )
                                }
                            }

                            // Stock status badge
                            Surface(
                                shape = RoundedCornerShape(6.dp),
                                color = if (inStock) Color(0xFFDCFCE7) else Color(0xFFFFE4E6)
                            ) {
                                Text(
                                    text = if (inStock) "In Stock" else "Out of Stock",
                                    fontSize = 11.sp,
                                    fontWeight = FontWeight.Bold,
                                    color = if (inStock) Color(0xFF15803D) else Color(0xFFBE123C),
                                    modifier = Modifier.padding(horizontal = 8.dp, vertical = 4.dp)
                                )
                            }
                        }

                        Spacer(modifier = Modifier.height(8.dp))
                        Text(text = product.name, fontSize = 18.sp, fontWeight = FontWeight.Bold)

                        if (!product.sku.isNullOrEmpty()) {
                            Text(text = "SKU: ${product.sku}", fontSize = 12.sp, color = Color.Gray)
                        }

                        // Warranty badge
                        if (!product.warranty.isNullOrEmpty()) {
                            Spacer(modifier = Modifier.height(10.dp))
                            Row(
                                modifier = Modifier
                                    .clip(RoundedCornerShape(8.dp))
                                    .background(Color(0xFFEFF6FF))
                                    .padding(horizontal = 10.dp, vertical = 6.dp),
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Icon(
                                    imageVector = Icons.Default.VerifiedUser,
                                    contentDescription = null,
                                    tint = Color(0xFF2563EB),
                                    modifier = Modifier.size(16.dp)
                                )
                                Spacer(modifier = Modifier.width(6.dp))
                                Text(
                                    text = "Warranty: ${product.warranty}",
                                    fontSize = 12.sp,
                                    fontWeight = FontWeight.SemiBold,
                                    color = Color(0xFF1D4ED8)
                                )
                            }
                        }
                    }
                }

                // 3. Variant Selector Pills
                if (product.variants.isNotEmpty()) {
                    item {
                        Column(modifier = Modifier.padding(horizontal = 16.dp, vertical = 6.dp)) {
                            Text(text = "Select Variant / Color / Size", fontSize = 14.sp, fontWeight = FontWeight.Bold)
                            Spacer(modifier = Modifier.height(8.dp))
                            LazyRow(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                                items(product.variants) { variant ->
                                    val isSelected = selectedVariant?.id == variant.id
                                    FilterChip(
                                        selected = isSelected,
                                        onClick = { selectedVariant = variant },
                                        label = {
                                            Text(
                                                text = variant.name ?: "${variant.color?.name ?: ""} ${variant.size?.name ?: ""}".trim(),
                                                fontSize = 12.sp,
                                                fontWeight = if (isSelected) FontWeight.Bold else FontWeight.Normal
                                            )
                                        },
                                        leadingIcon = if (variant.color != null) {
                                            {
                                                Box(
                                                    modifier = Modifier
                                                        .size(12.dp)
                                                        .clip(CircleShape)
                                                        .background(Color.DarkGray)
                                                )
                                            }
                                        } else null
                                    )
                                }
                            }
                        }
                    }
                }

                // 4. Description & Specifications
                item {
                    Divider(modifier = Modifier.padding(vertical = 12.dp))
                    Column(modifier = Modifier.padding(horizontal = 16.dp)) {
                        Text(text = "Overview & Specifications", fontSize = 15.sp, fontWeight = FontWeight.Bold)
                        Spacer(modifier = Modifier.height(6.dp))
                        Text(
                            text = product.shortDescription ?: product.description ?: "High-quality electronics PCB product designed for durability and performance.",
                            fontSize = 13.sp,
                            lineHeight = 20.sp,
                            color = MaterialTheme.colorScheme.onSurfaceVariant
                        )
                    }
                }
            }
        }
    }
}

package com.dreamerspcb.ecommerce.presentation.ui

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
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
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dreamerspcb.ecommerce.data.model.OrderTrackingDto

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun TrackOrderScreen(
    trackingData: OrderTrackingDto?,
    isLoading: Boolean,
    onBackClick: () -> Unit,
    onSearchOrder: (String, String?) -> Unit
) {
    var orderNoInput by remember { mutableStateOf("") }
    var phoneInput by remember { mutableStateOf("") }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text(text = "Live Order Tracking", fontSize = 16.sp, fontWeight = FontWeight.Bold) },
                navigationIcon = {
                    IconButton(onClick = onBackClick) {
                        Icon(imageVector = Icons.Default.ArrowBack, contentDescription = "Back")
                    }
                }
            )
        }
    ) { padding ->
        LazyColumn(
            modifier = Modifier.fillMaxSize().padding(padding),
            contentPadding = PaddingValues(16.dp),
            verticalArrangement = Arrangement.spacedBy(14.dp)
        ) {
            // 1. Search Box
            item {
                Card(shape = RoundedCornerShape(12.dp)) {
                    Column(modifier = Modifier.padding(14.dp)) {
                        Text(text = "🔍 Track Any Order", fontSize = 14.sp, fontWeight = FontWeight.Bold)
                        Spacer(modifier = Modifier.height(10.dp))
                        OutlinedTextField(
                            value = orderNoInput,
                            onValueChange = { orderNoInput = it },
                            label = { Text("Order Number (e.g. ORD-XXXX-1234)") },
                            modifier = Modifier.fillMaxWidth(),
                            singleLine = true
                        )
                        Spacer(modifier = Modifier.height(8.dp))
                        OutlinedTextField(
                            value = phoneInput,
                            onValueChange = { phoneInput = it },
                            label = { Text("Phone Number (Optional)") },
                            modifier = Modifier.fillMaxWidth(),
                            singleLine = true
                        )
                        Spacer(modifier = Modifier.height(10.dp))
                        Button(
                            onClick = {
                                if (orderNoInput.isNotBlank()) {
                                    onSearchOrder(orderNoInput.trim(), phoneInput.trim().ifEmpty { null })
                                }
                            },
                            modifier = Modifier.fillMaxWidth().height(46.dp),
                            shape = RoundedCornerShape(10.dp)
                        ) {
                            if (isLoading) {
                                CircularProgressIndicator(color = Color.White, modifier = Modifier.size(20.dp))
                            } else {
                                Text(text = "Track Order", fontWeight = FontWeight.Bold)
                            }
                        }
                    }
                }
            }

            // 2. Milestone Timeline
            if (trackingData != null) {
                item {
                    Card(shape = RoundedCornerShape(12.dp)) {
                        Column(modifier = Modifier.padding(16.dp)) {
                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                horizontalArrangement = Arrangement.SpaceBetween,
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Column {
                                    Text(text = trackingData.orderNo, fontSize = 16.sp, fontWeight = FontWeight.Black)
                                    Text(text = "Total: ৳${trackingData.grandTotal}", fontSize = 13.sp, color = MaterialTheme.colorScheme.primary, fontWeight = FontWeight.Bold)
                                }
                                Surface(
                                    shape = RoundedCornerShape(8.dp),
                                    color = Color(0xFFDCFCE7)
                                ) {
                                    Text(
                                        text = trackingData.status.uppercase(),
                                        fontSize = 11.sp,
                                        fontWeight = FontWeight.Bold,
                                        color = Color(0xFF15803D),
                                        modifier = Modifier.padding(horizontal = 8.dp, vertical = 4.dp)
                                    )
                                }
                            }

                            Divider(modifier = Modifier.padding(vertical = 12.dp))

                            Text(text = "Delivery Milestone Timeline", fontSize = 14.sp, fontWeight = FontWeight.Bold)
                            Spacer(modifier = Modifier.height(12.dp))

                            trackingData.timeline.forEachIndexed { index, milestone ->
                                Row(modifier = Modifier.fillMaxWidth()) {
                                    // Step Indicator Circle & Connector Line
                                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                        Box(
                                            modifier = Modifier
                                                .size(24.dp)
                                                .clip(CircleShape)
                                                .background(
                                                    if (milestone.completed) Color(0xFF10B981)
                                                    else Color(0xFFE2E8F0)
                                                ),
                                            contentAlignment = Alignment.Center
                                        ) {
                                            if (milestone.completed) {
                                                Icon(
                                                    imageVector = Icons.Default.Check,
                                                    contentDescription = null,
                                                    tint = Color.White,
                                                    modifier = Modifier.size(14.dp)
                                                )
                                            } else {
                                                Text(text = "${milestone.step}", fontSize = 10.sp, color = Color.Gray, fontWeight = FontWeight.Bold)
                                            }
                                        }

                                        if (index < trackingData.timeline.size - 1) {
                                            Box(
                                                modifier = Modifier
                                                    .width(2.dp)
                                                    .height(38.dp)
                                                    .background(
                                                        if (milestone.completed) Color(0xFF10B981)
                                                        else Color(0xFFE2E8F0)
                                                    )
                                            )
                                        }
                                    }

                                    Spacer(modifier = Modifier.width(12.dp))

                                    // Milestone Content
                                    Column(modifier = Modifier.weight(1f)) {
                                        Text(
                                            text = milestone.title,
                                            fontSize = 13.sp,
                                            fontWeight = if (milestone.isCurrent) FontWeight.Black else FontWeight.SemiBold,
                                            color = if (milestone.completed) Color.Black else Color.Gray
                                        )
                                        Text(text = milestone.description, fontSize = 11.sp, color = Color.Gray)
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

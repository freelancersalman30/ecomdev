package com.dreamerspcb.ecommerce.presentation.ui

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
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
import com.dreamerspcb.ecommerce.data.model.WarrantyDto

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun WarrantyScannerScreen(
    scannedWarranty: WarrantyDto?,
    isLoading: Boolean,
    onBackClick: () -> Unit,
    onManualSearch: (String) -> Unit,
    onClaimWarranty: (WarrantyDto) -> Unit
) {
    var manualSerial by remember { mutableStateOf("") }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text(text = "Scan Serial / Warranty", fontSize = 16.sp, fontWeight = FontWeight.Bold) },
                navigationIcon = {
                    IconButton(onClick = onBackClick) {
                        Icon(imageVector = Icons.Default.ArrowBack, contentDescription = "Back")
                    }
                }
            )
        }
    ) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(padding)
                .padding(16.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            // Camera Scanner Mock / Placeholder Viewfinder
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .height(220.dp)
                    .clip(RoundedCornerShape(16.dp))
                    .background(Color(0xFF0F172A)),
                contentAlignment = Alignment.Center
            ) {
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    Icon(
                        imageVector = Icons.Default.QrCodeScanner,
                        contentDescription = "Scanner",
                        tint = Color(0xFF10B981),
                        modifier = Modifier.size(64.dp)
                    )
                    Spacer(modifier = Modifier.height(8.dp))
                    Text(
                        text = "Point camera at product barcode / QR code",
                        color = Color.White,
                        fontSize = 12.sp
                    )
                }
            }

            Spacer(modifier = Modifier.height(16.dp))

            // Manual Serial Input
            Card(shape = RoundedCornerShape(12.dp), modifier = Modifier.fillMaxWidth()) {
                Column(modifier = Modifier.padding(14.dp)) {
                    Text(text = "Or Enter Serial Number Manually", fontSize = 13.sp, fontWeight = FontWeight.Bold)
                    Spacer(modifier = Modifier.height(8.dp))
                    Row(modifier = Modifier.fillMaxWidth()) {
                        OutlinedTextField(
                            value = manualSerial,
                            onValueChange = { manualSerial = it },
                            placeholder = { Text("e.g. SN-PCB-998877") },
                            modifier = Modifier.weight(1f),
                            singleLine = true
                        )
                        Spacer(modifier = Modifier.width(8.dp))
                        Button(
                            onClick = {
                                if (manualSerial.isNotBlank()) {
                                    onManualSearch(manualSerial.trim())
                                }
                            },
                            modifier = Modifier.height(56.dp)
                        ) {
                            Text("Verify")
                        }
                    }
                }
            }

            Spacer(modifier = Modifier.height(16.dp))

            // Scanned Warranty Result Card
            if (isLoading) {
                CircularProgressIndicator()
            } else if (scannedWarranty != null) {
                Card(
                    shape = RoundedCornerShape(14.dp),
                    colors = CardDefaults.cardColors(containerColor = if (scannedWarranty.isValid) Color(0xFFF0FDF4) else Color(0xFFFFF1F2)),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Column(modifier = Modifier.padding(16.dp)) {
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text(
                                text = if (scannedWarranty.isValid) "✓ Valid Warranty" else "✗ Expired / Invalid",
                                fontSize = 15.sp,
                                fontWeight = FontWeight.Black,
                                color = if (scannedWarranty.isValid) Color(0xFF15803D) else Color(0xFFBE123C)
                            )
                            if (scannedWarranty.daysRemaining != null) {
                                Text(
                                    text = "${scannedWarranty.daysRemaining} days remaining",
                                    fontSize = 12.sp,
                                    fontWeight = FontWeight.Bold,
                                    color = Color(0xFF15803D)
                                )
                            }
                        }

                        Divider(modifier = Modifier.padding(vertical = 8.dp))

                        Text(text = scannedWarranty.productName, fontSize = 14.sp, fontWeight = FontWeight.Bold)
                        Text(text = "Serial No: ${scannedWarranty.serialNumber}", fontSize = 12.sp, color = Color.DarkGray)
                        Text(text = "Period: ${scannedWarranty.warrantyPeriod}", fontSize = 12.sp, color = Color.DarkGray)
                        Text(text = "Expires: ${scannedWarranty.endDate ?: "N/A"}", fontSize = 12.sp, color = Color.DarkGray)

                        if (scannedWarranty.isValid) {
                            Spacer(modifier = Modifier.height(12.dp))
                            Button(
                                onClick = { onClaimWarranty(scannedWarranty) },
                                modifier = Modifier.fillMaxWidth(),
                                shape = RoundedCornerShape(10.dp)
                            ) {
                                Text(text = "Submit Warranty Claim", fontWeight = FontWeight.Bold)
                            }
                        }
                    }
                }
            }
        }
    }
}

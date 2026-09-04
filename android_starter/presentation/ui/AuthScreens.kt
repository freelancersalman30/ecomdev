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
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.dreamerspcb.ecommerce.data.model.CustomerDto

@Composable
fun LoginScreen(
    isLoading: Boolean,
    onLoginSubmit: (String, String) -> Unit,
    onNavigateToRegister: () -> Unit
) {
    var loginInput by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(24.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.Center
    ) {
        Icon(
            imageVector = Icons.Default.Lock,
            contentDescription = null,
            modifier = Modifier.size(56.dp),
            tint = MaterialTheme.colorScheme.primary
        )
        Spacer(modifier = Modifier.height(12.dp))
        Text(text = "Customer Login", fontSize = 22.sp, fontWeight = FontWeight.Black)
        Text(text = "Sign in with your phone number or email", fontSize = 13.sp, color = Color.Gray)

        Spacer(modifier = Modifier.height(24.dp))

        OutlinedTextField(
            value = loginInput,
            onValueChange = { loginInput = it },
            label = { Text("Phone Number or Email") },
            modifier = Modifier.fillMaxWidth(),
            singleLine = true
        )

        Spacer(modifier = Modifier.height(12.dp))

        OutlinedTextField(
            value = password,
            onValueChange = { password = it },
            label = { Text("Password") },
            visualTransformation = PasswordVisualTransformation(),
            modifier = Modifier.fillMaxWidth(),
            singleLine = true
        )

        Spacer(modifier = Modifier.height(20.dp))

        Button(
            onClick = {
                if (loginInput.isNotBlank() && password.isNotBlank()) {
                    onLoginSubmit(loginInput.trim(), password)
                }
            },
            enabled = !isLoading && loginInput.isNotBlank() && password.isNotBlank(),
            modifier = Modifier.fillMaxWidth().height(48.dp),
            shape = RoundedCornerShape(12.dp)
        ) {
            if (isLoading) {
                CircularProgressIndicator(color = Color.White, modifier = Modifier.size(20.dp))
            } else {
                Text(text = "Sign In", fontWeight = FontWeight.Bold)
            }
        }

        Spacer(modifier = Modifier.height(16.dp))

        TextButton(onClick = onNavigateToRegister) {
            Text(text = "Don't have an account? Register here")
        }
    }
}

@Composable
fun ProfileScreen(
    customer: CustomerDto?,
    onLogoutClick: () -> Unit,
    onOrdersClick: () -> Unit,
    onWarrantiesClick: () -> Unit
) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(16.dp)
    ) {
        Text(text = "Customer Account", fontSize = 20.sp, fontWeight = FontWeight.Black)
        Spacer(modifier = Modifier.height(14.dp))

        // Profile Details Card
        Card(shape = RoundedCornerShape(14.dp), modifier = Modifier.fillMaxWidth()) {
            Column(modifier = Modifier.padding(16.dp)) {
                Text(text = customer?.name ?: "Customer", fontSize = 16.sp, fontWeight = FontWeight.Bold)
                Text(text = customer?.phone ?: "", fontSize = 13.sp, color = Color.Gray)
                if (!customer?.email.isNullOrEmpty()) {
                    Text(text = customer?.email ?: "", fontSize = 13.sp, color = Color.Gray)
                }

                Spacer(modifier = Modifier.height(12.dp))

                // Loyalty Points Badge
                Row(
                    modifier = Modifier
                        .clip(RoundedCornerShape(8.dp))
                        .background(Color(0xFFFEF3C7))
                        .padding(horizontal = 12.dp, vertical = 6.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Icon(imageVector = Icons.Default.Stars, contentDescription = null, tint = Color(0xFFD97706), modifier = Modifier.size(18.dp))
                    Spacer(modifier = Modifier.width(6.dp))
                    Text(text = "Loyalty Points: ${customer?.loyaltyPoints ?: 0} pts", fontSize = 12.sp, fontWeight = FontWeight.Bold, color = Color(0xFFB45309))
                }
            }
        }

        Spacer(modifier = Modifier.height(16.dp))

        // Action Options
        Card(shape = RoundedCornerShape(14.dp), modifier = Modifier.fillMaxWidth()) {
            Column {
                ListItem(
                    headlineContent = { Text("My Previous Orders") },
                    leadingContent = { Icon(Icons.Default.ShoppingBag, contentDescription = null) },
                    trailingContent = { Icon(Icons.Default.ChevronRight, contentDescription = null) },
                    modifier = Modifier.padding(vertical = 4.dp)
                )
                Divider()
                ListItem(
                    headlineContent = { Text("Product Warranties & Claims") },
                    leadingContent = { Icon(Icons.Default.VerifiedUser, contentDescription = null) },
                    trailingContent = { Icon(Icons.Default.ChevronRight, contentDescription = null) },
                    modifier = Modifier.padding(vertical = 4.dp)
                )
                Divider()
                ListItem(
                    headlineContent = { Text("Sign Out", color = Color(0xFFBE123C)) },
                    leadingContent = { Icon(Icons.Default.ExitToApp, contentDescription = null, tint = Color(0xFFBE123C)) },
                    modifier = Modifier.padding(vertical = 4.dp)
                )
            }
        }
    }
}

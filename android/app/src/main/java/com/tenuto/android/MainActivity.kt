package com.tenuto.android

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.activity.viewModels
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import com.tenuto.android.auth.AuthViewModel
import com.tenuto.android.auth.LoginScreen
import com.tenuto.android.home.HomeScreen
import com.tenuto.android.ui.theme.TenutoTheme

class MainActivity : ComponentActivity() {
    private val authViewModel: AuthViewModel by viewModels()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContent {
            TenutoTheme {
                val token by authViewModel.token.collectAsState()
                if (token != null) {
                    HomeScreen(authViewModel)
                } else {
                    LoginScreen(authViewModel)
                }
            }
        }
    }
}
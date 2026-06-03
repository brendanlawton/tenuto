package com.tenuto.android.auth

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow

class AuthViewModel(application: Application) : AndroidViewModel(application) {
    private val tokenStore = TokenStore(application)

    private val _token = MutableStateFlow(tokenStore.load())
    val token: StateFlow<String?> = _token.asStateFlow()

    fun login(token: String) {
        _token.value = token
        tokenStore.save(token)
    }

    fun logout() {
        _token.value = null
        tokenStore.delete()
    }
}

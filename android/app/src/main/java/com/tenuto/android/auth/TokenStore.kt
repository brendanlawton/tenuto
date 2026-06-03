package com.tenuto.android.auth

import android.content.Context
import androidx.security.crypto.EncryptedSharedPreferences
import androidx.security.crypto.MasterKey

class TokenStore(context: Context) {
    private val masterKey = MasterKey.Builder(context)
        .setKeyScheme(MasterKey.KeyScheme.AES256_GCM)
        .build()

    private val prefs = EncryptedSharedPreferences.create(
        context,
        "tenuto_auth_prefs",
        masterKey,
        EncryptedSharedPreferences.PrefKeyEncryptionScheme.AES256_SIV,
        EncryptedSharedPreferences.PrefValueEncryptionScheme.AES256_GCM,
    )

    fun save(token: String) = prefs.edit().putString(KEY, token).apply()
    fun load(): String? = prefs.getString(KEY, null)
    fun delete() = prefs.edit().remove(KEY).apply()

    companion object {
        private const val KEY = "tenuto.auth.token"
    }
}

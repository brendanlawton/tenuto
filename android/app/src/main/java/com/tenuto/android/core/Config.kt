package com.tenuto.android.core

import com.tenuto.android.BuildConfig

object Config {
    val apiBaseUrl: String
        get() = if (BuildConfig.DEBUG) "http://10.0.2.2/api/v1/" else "https://api.tenuto.com/api/v1/"
}

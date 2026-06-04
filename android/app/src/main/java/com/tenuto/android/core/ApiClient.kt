package com.tenuto.android.core

import com.jakewharton.retrofit2.converter.kotlinx.serialization.asConverterFactory
import kotlinx.serialization.Serializable
import kotlinx.serialization.json.Json
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.OkHttpClient
import retrofit2.Response
import retrofit2.Retrofit
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.Header
import retrofit2.http.POST

@Serializable
private data class LoginRequest(val email: String, val password: String)

@Serializable
private data class RegisterRequest(
    val name: String,
    val email: String,
    val password: String,
    val password_confirmation: String,
)

@Serializable
private data class GoogleSignInRequest(val id_token: String)

@Serializable
data class TokenResponse(val token: String)

@Serializable
data class UserResponse(val id: Int, val name: String, val email: String)

@Serializable
private data class ValidationErrorResponse(val errors: Map<String, List<String>>) {
    val firstMessage: String? get() = errors.values.firstOrNull()?.firstOrNull()
}

sealed class ApiError : Exception() {
    data class Unprocessable(val apiMessage: String) : ApiError()
    data object InvalidResponse : ApiError()
    data object ServerError : ApiError()
    data object Unauthorized : ApiError()
}

private interface AuthService {
    @POST("auth/token")
    suspend fun login(@Body request: LoginRequest): Response<TokenResponse>

    @POST("auth/register")
    suspend fun register(@Body request: RegisterRequest): Response<Unit>

    @POST("auth/google/token")
    suspend fun googleSignIn(@Body request: GoogleSignInRequest): Response<TokenResponse>

    @GET("user")
    suspend fun fetchUser(@Header("Authorization") auth: String): Response<UserResponse>
}

@OptIn(kotlinx.serialization.ExperimentalSerializationApi::class)
class ApiClient {
    private val json = Json { ignoreUnknownKeys = true }

    private val retrofit = Retrofit.Builder()
        .baseUrl(Config.apiBaseUrl)
        .client(OkHttpClient())
        .addConverterFactory(json.asConverterFactory("application/json; charset=UTF-8".toMediaType()))
        .build()

    private val service = retrofit.create(AuthService::class.java)

    suspend fun login(email: String, password: String): String {
        val response = service.login(LoginRequest(email, password))
        return when (response.code()) {
            200 -> response.body()?.token ?: throw ApiError.InvalidResponse
            422 -> throw ApiError.Unprocessable(parseValidationError(response.errorBody()?.string()) ?: "Invalid credentials.")
            else -> throw ApiError.ServerError
        }
    }

    suspend fun register(name: String, email: String, password: String, passwordConfirmation: String) {
        val response = service.register(RegisterRequest(name, email, password, passwordConfirmation))
        when (response.code()) {
            201 -> return
            422 -> throw ApiError.Unprocessable(parseValidationError(response.errorBody()?.string()) ?: "Registration failed.")
            else -> throw ApiError.ServerError
        }
    }

    suspend fun googleSignIn(idToken: String): String {
        val response = service.googleSignIn(GoogleSignInRequest(idToken))
        return when (response.code()) {
            200 -> response.body()?.token ?: throw ApiError.InvalidResponse
            422 -> throw ApiError.Unprocessable(parseValidationError(response.errorBody()?.string()) ?: "Google sign-in failed.")
            else -> throw ApiError.ServerError
        }
    }

    suspend fun fetchUser(token: String): UserResponse {
        val response = service.fetchUser("Bearer $token")
        return when (response.code()) {
            200 -> response.body() ?: throw ApiError.InvalidResponse
            401 -> throw ApiError.Unauthorized
            else -> throw ApiError.ServerError
        }
    }

    private fun parseValidationError(body: String?): String? {
        if (body == null) return null
        return try {
            json.decodeFromString<ValidationErrorResponse>(body).firstMessage
        } catch (e: Exception) {
            null
        }
    }
}

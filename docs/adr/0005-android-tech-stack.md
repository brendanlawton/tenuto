# ADR 0005 — Android Tech Stack and Platform Parity Principle

## Status
Accepted

## Context
The Android app is the second mobile client after iOS. A key question was whether to mirror the iOS implementation choices (plain URLSession, Security framework directly) or use idiomatic Android libraries.

iOS uses URLSession because it is a built-in, zero-dependency HTTP client that is idiomatic Swift. The Android equivalent — `HttpURLConnection` — is verbose and not idiomatic. Android has a mature, widely-adopted ecosystem of libraries (Retrofit, EncryptedSharedPreferences) that sit at the same conceptual level as the iOS built-ins.

The same question applies to auth state management: iOS uses `@Observable` class with Keychain; Android has `ViewModel + StateFlow` and `EncryptedSharedPreferences` as its idiomatic equivalents.

## Decision
Use idiomatic per-platform patterns rather than forcing implementation parity across iOS and Android. The platforms share intent (same API contract, same UX flow, same domain model) but not implementation.

Concretely:

| Concern | iOS | Android |
|---|---|---|
| HTTP | URLSession (built-in) | Retrofit + OkHttp |
| JSON | Codable (built-in) | kotlinx.serialization |
| Secure storage | Security framework / Keychain | EncryptedSharedPreferences |
| Auth state | `@Observable` class + SwiftUI environment | `AndroidViewModel` + `StateFlow` |
| UI | SwiftUI | Jetpack Compose |

**Retrofit over plain `HttpURLConnection`**: Retrofit is the de facto Android HTTP standard. It provides type-safe, suspend-function-native HTTP with minimal boilerplate — the idiomatic Android equivalent of URLSession, not a heavier dependency. `HttpURLConnection` would require the same manual request/response plumbing as URLSession but with significantly more verbose Java-era API surface.

**kotlinx.serialization over Gson**: Gson is reflection-based and not Kotlin-native. kotlinx.serialization is annotation-driven, compile-time safe, and aligns with the direction the Kotlin ecosystem is moving. It is the Android equivalent of Swift's `Codable`.

**`EncryptedSharedPreferences` at `1.1.0-alpha06`**: The `1.0.0` stable release uses the deprecated `MasterKeys` API. The `1.1.0-alpha06` `MasterKey.Builder` API is the standard approach in current production apps despite the alpha label — the library has been stable in practice for years.

## Consequences
- iOS and Android implementations are not line-for-line mirrors, but they share the same API contract, auth flow, and UX behaviour.
- A developer working on both platforms needs to understand two different idioms; the trade-off is that each codebase is readable to a platform-native developer.
- Future features must be evaluated per-platform: if there is a clearly better idiomatic approach on one platform, use it.

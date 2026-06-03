//
//  iosTests.swift
//  iosTests
//
//  Created by Brendan Lawton on 03/06/2026.
//

import Foundation
import Testing
@testable import ios

struct KeychainStoreTests {

    @Test func savingTokenAllowsRetrieval() throws {
        let store = KeychainStore(key: "test.token.\(UUID().uuidString)")
        defer { store.delete() }

        store.save("test-bearer-token")

        #expect(store.load() == "test-bearer-token")
    }

    @Test func deletingTokenReturnsNilOnRetrieval() throws {
        let store = KeychainStore(key: "test.token.\(UUID().uuidString)")

        store.save("test-bearer-token")
        store.delete()

        #expect(store.load() == nil)
    }

}

@MainActor
struct AuthStateTests {

    @Test func startsUnauthenticated() {
        let keychainStore = KeychainStore(key: "test.authstate.\(UUID().uuidString)")
        defer { keychainStore.delete() }
        let authState = AuthState(keychainStore: keychainStore)

        #expect(authState.isAuthenticated == false)
        #expect(authState.token == nil)
    }

    @Test func loginSetsAuthenticatedStateAndPersistsToken() async throws {
        let keychainStore = KeychainStore(key: "test.authstate.\(UUID().uuidString)")
        defer { keychainStore.delete() }
        let authState = AuthState(keychainStore: keychainStore)

        authState.login(token: "sanctum-token-123")

        #expect(authState.isAuthenticated == true)
        #expect(authState.token == "sanctum-token-123")
        #expect(keychainStore.load() == "sanctum-token-123")
    }

    @Test func logoutClearsAuthenticatedStateAndRemovesToken() throws {
        let keychainStore = KeychainStore(key: "test.authstate.\(UUID().uuidString)")
        defer { keychainStore.delete() }
        let authState = AuthState(keychainStore: keychainStore)

        authState.login(token: "sanctum-token-123")
        authState.logout()

        #expect(authState.isAuthenticated == false)
        #expect(authState.token == nil)
        #expect(keychainStore.load() == nil)
    }

}

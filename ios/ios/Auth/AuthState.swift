import Foundation

@MainActor
@Observable
final class AuthState {
    private(set) var token: String?
    private let keychainStore: KeychainStore

    var isAuthenticated: Bool { token != nil }

    init(keychainStore: KeychainStore = KeychainStore(key: "tenuto.auth.token")) {
        self.keychainStore = keychainStore
        self.token = keychainStore.load()
    }

    func login(token: String) {
        self.token = token
        keychainStore.save(token)
    }

    func logout() {
        self.token = nil
        keychainStore.delete()
    }
}

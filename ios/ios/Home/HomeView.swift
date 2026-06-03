import SwiftUI

struct HomeView: View {
    @Environment(AuthState.self) private var authState

    @State private var userEmail: String?
    @State private var isLoading = true

    var body: some View {
        VStack(spacing: 24) {
            if isLoading {
                ProgressView()
            } else {
                Text("Welcome")
                    .font(.largeTitle.bold())

                if let userEmail {
                    Text(userEmail)
                        .foregroundStyle(.secondary)
                }

                Button("Log out") {
                    authState.logout()
                }
                .buttonStyle(.bordered)
            }
        }
        .task { await loadUser() }
    }

    private func loadUser() async {
        let client = APIClient(authState: authState)
        if let user = try? await client.fetchUser() {
            userEmail = user.email
        }
        isLoading = false
    }
}

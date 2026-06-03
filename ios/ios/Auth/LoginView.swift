import SwiftUI

struct LoginView: View {
    @Environment(AuthState.self) private var authState

    @State private var email = ""
    @State private var password = ""
    @State private var errorMessage: String?
    @State private var isLoading = false

    var body: some View {
        VStack(spacing: 24) {
            Text("Tenuto")
                .font(.largeTitle.bold())

            VStack(spacing: 12) {
                TextField("Email", text: $email)
                    .keyboardType(.emailAddress)
                    .textContentType(.emailAddress)
                    .autocapitalization(.none)
                    .textFieldStyle(.roundedBorder)

                SecureField("Password", text: $password)
                    .textContentType(.password)
                    .textFieldStyle(.roundedBorder)
            }

            if let errorMessage {
                Text(errorMessage)
                    .foregroundStyle(.red)
                    .font(.caption)
            }

            Button {
                Task { await login() }
            } label: {
                if isLoading {
                    ProgressView()
                        .frame(maxWidth: .infinity)
                } else {
                    Text("Log in")
                        .frame(maxWidth: .infinity)
                }
            }
            .buttonStyle(.borderedProminent)
            .disabled(isLoading || email.isEmpty || password.isEmpty)
        }
        .padding(32)
    }

    private func login() async {
        isLoading = true
        errorMessage = nil
        let client = APIClient(authState: authState)

        do {
            let token = try await client.login(email: email, password: password)
            authState.login(token: token)
        } catch APIError.unprocessable(let message) {
            errorMessage = message
        } catch {
            errorMessage = "Something went wrong. Please try again."
        }

        isLoading = false
    }
}

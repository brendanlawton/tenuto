import GoogleSignIn
import SwiftUI

struct LoginView: View {
    @Environment(AuthState.self) private var authState

    @State private var email = ""
    @State private var password = ""
    @State private var errorMessage: String?
    @State private var isLoading = false
    @State private var showRegister = false

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

            HStack {
                VStack { Divider() }
                Text("or").foregroundStyle(.secondary).font(.caption)
                VStack { Divider() }
            }

            Button {
                Task { await signInWithGoogle() }
            } label: {
                HStack(spacing: 8) {
                    Image(systemName: "g.circle.fill")
                    Text("Sign in with Google")
                }
                .frame(maxWidth: .infinity)
            }
            .buttonStyle(.bordered)
            .disabled(isLoading)

            Button("Don't have an account? Register") {
                showRegister = true
            }
            .font(.caption)
        }
        .padding(32)
        .sheet(isPresented: $showRegister) {
            RegisterView()
                .environment(authState)
        }
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

    private func signInWithGoogle() async {
        isLoading = true
        errorMessage = nil

        do {
            guard let windowScene = UIApplication.shared.connectedScenes.first as? UIWindowScene,
                  let rootViewController = windowScene.windows.first?.rootViewController
            else { throw APIError.invalidResponse }

            let result: GIDSignInResult = try await withCheckedThrowingContinuation { continuation in
                GIDSignIn.sharedInstance.signIn(withPresenting: rootViewController) { signInResult, error in
                    if let error {
                        continuation.resume(throwing: error)
                    } else if let signInResult {
                        continuation.resume(returning: signInResult)
                    } else {
                        continuation.resume(throwing: APIError.invalidResponse)
                    }
                }
            }

            guard let idToken = result.user.idToken?.tokenString else {
                throw APIError.invalidResponse
            }

            let client = APIClient(authState: authState)
            let token = try await client.googleSignIn(idToken: idToken)
            authState.login(token: token)
        } catch APIError.unprocessable(let message) {
            errorMessage = message
        } catch {
            errorMessage = "Google sign-in failed. Please try again."
        }

        isLoading = false
    }
}

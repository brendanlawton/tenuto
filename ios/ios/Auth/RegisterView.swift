import SwiftUI

struct RegisterView: View {
    @Environment(AuthState.self) private var authState
    @Environment(\.dismiss) private var dismiss

    @State private var name = ""
    @State private var email = ""
    @State private var password = ""
    @State private var passwordConfirmation = ""
    @State private var errorMessage: String?
    @State private var isLoading = false
    @State private var registrationSucceeded = false

    var body: some View {
        VStack(spacing: 24) {
            Text("Create Account")
                .font(.largeTitle.bold())

            if registrationSucceeded {
                VStack(spacing: 12) {
                    Image(systemName: "envelope.circle")
                        .font(.system(size: 60))
                        .foregroundStyle(.secondary)

                    Text("Check your email")
                        .font(.title2.bold())

                    Text("We've sent a verification link to \(email). Please verify your email before logging in.")
                        .multilineTextAlignment(.center)
                        .foregroundStyle(.secondary)

                    Button("Back to Login") {
                        dismiss()
                    }
                    .buttonStyle(.borderedProminent)
                }
            } else {
                VStack(spacing: 12) {
                    TextField("Name", text: $name)
                        .textContentType(.name)
                        .textFieldStyle(.roundedBorder)

                    TextField("Email", text: $email)
                        .keyboardType(.emailAddress)
                        .textContentType(.emailAddress)
                        .autocapitalization(.none)
                        .textFieldStyle(.roundedBorder)

                    SecureField("Password", text: $password)
                        .textContentType(.newPassword)
                        .textFieldStyle(.roundedBorder)

                    SecureField("Confirm Password", text: $passwordConfirmation)
                        .textContentType(.newPassword)
                        .textFieldStyle(.roundedBorder)
                }

                if let errorMessage {
                    Text(errorMessage)
                        .foregroundStyle(.red)
                        .font(.caption)
                }

                Button {
                    Task { await register() }
                } label: {
                    if isLoading {
                        ProgressView()
                            .frame(maxWidth: .infinity)
                    } else {
                        Text("Create Account")
                            .frame(maxWidth: .infinity)
                    }
                }
                .buttonStyle(.borderedProminent)
                .disabled(isLoading || name.isEmpty || email.isEmpty || password.isEmpty || passwordConfirmation.isEmpty)

                Button("Already have an account? Log in") {
                    dismiss()
                }
                .font(.caption)
            }
        }
        .padding(32)
    }

    private func register() async {
        isLoading = true
        errorMessage = nil
        let client = APIClient(authState: authState)

        do {
            try await client.register(
                name: name,
                email: email,
                password: password,
                passwordConfirmation: passwordConfirmation
            )
            registrationSucceeded = true
        } catch APIError.unprocessable(let message) {
            errorMessage = message
        } catch {
            errorMessage = "Something went wrong. Please try again."
        }

        isLoading = false
    }
}

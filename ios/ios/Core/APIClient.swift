import Foundation

enum APIError: Error {
    case invalidResponse
    case unauthorized
    case unprocessable(String)
    case serverError
}

struct APIClient {
    private let authState: AuthState

    init(authState: AuthState) {
        self.authState = authState
    }

    func login(email: String, password: String) async throws -> String {
        var request = URLRequest(url: Config.apiBaseURL.appendingPathComponent("auth/token"))
        request.httpMethod = "POST"
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.setValue("application/json", forHTTPHeaderField: "Accept")
        request.httpBody = try JSONEncoder().encode(["email": email, "password": password])

        let (data, response) = try await URLSession.shared.data(for: request)

        guard let http = response as? HTTPURLResponse else { throw APIError.invalidResponse }

        switch http.statusCode {
        case 200:
            let body = try JSONDecoder().decode([String: String].self, from: data)
            guard let token = body["token"] else { throw APIError.invalidResponse }
            return token
        case 422:
            let body = try? JSONDecoder().decode(ValidationErrorResponse.self, from: data)
            throw APIError.unprocessable(body?.firstMessage ?? "Invalid credentials.")
        default:
            throw APIError.serverError
        }
    }

    func register(name: String, email: String, password: String, passwordConfirmation: String) async throws {
        var request = URLRequest(url: Config.apiBaseURL.appendingPathComponent("auth/register"))
        request.httpMethod = "POST"
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.setValue("application/json", forHTTPHeaderField: "Accept")
        request.httpBody = try JSONEncoder().encode([
            "name": name,
            "email": email,
            "password": password,
            "password_confirmation": passwordConfirmation,
        ])

        let (data, response) = try await URLSession.shared.data(for: request)

        guard let http = response as? HTTPURLResponse else { throw APIError.invalidResponse }

        switch http.statusCode {
        case 201:
            return
        case 422:
            let body = try? JSONDecoder().decode(ValidationErrorResponse.self, from: data)
            throw APIError.unprocessable(body?.firstMessage ?? "Registration failed.")
        default:
            throw APIError.serverError
        }
    }

    func fetchUser() async throws -> UserResponse {
        guard let token = authState.token else { throw APIError.unauthorized }

        var request = URLRequest(url: Config.apiBaseURL.appendingPathComponent("user"))
        request.setValue("application/json", forHTTPHeaderField: "Accept")
        request.setValue("Bearer \(token)", forHTTPHeaderField: "Authorization")

        let (data, response) = try await URLSession.shared.data(for: request)

        guard let http = response as? HTTPURLResponse else { throw APIError.invalidResponse }
        guard http.statusCode == 200 else { throw APIError.serverError }

        return try JSONDecoder().decode(UserResponse.self, from: data)
    }
}

struct UserResponse: Decodable {
    let id: Int
    let name: String
    let email: String
}

private struct ValidationErrorResponse: Decodable {
    let errors: [String: [String]]

    var firstMessage: String? {
        errors.values.first?.first
    }
}

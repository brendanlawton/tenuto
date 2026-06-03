import Foundation

enum Config {
    #if DEBUG
    static let apiBaseURL = URL(string: "http://localhost:80/api/v1")!
    #else
    static let apiBaseURL = URL(string: "https://api.tenuto.com/api/v1")!
    #endif

    static let googleClientID = "135148528461-dlsfpqckk6farkk0v3g88mqdr6ahhr1d.apps.googleusercontent.com"
}

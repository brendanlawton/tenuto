//
//  iosApp.swift
//  ios
//
//  Created by Brendan Lawton on 03/06/2026.
//

import GoogleSignIn
import SwiftUI

@main
struct iosApp: App {
    @State private var authState = AuthState()

    init() {
        GIDSignIn.sharedInstance.configuration = GIDConfiguration(clientID: Config.googleClientID)
    }

    var body: some Scene {
        WindowGroup {
            Group {
                if authState.isAuthenticated {
                    HomeView()
                } else {
                    LoginView()
                }
            }
            .environment(authState)
            .onOpenURL { url in
                GIDSignIn.sharedInstance.handle(url)
            }
        }
    }
}

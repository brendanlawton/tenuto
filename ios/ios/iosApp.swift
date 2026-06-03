//
//  iosApp.swift
//  ios
//
//  Created by Brendan Lawton on 03/06/2026.
//

import SwiftUI

@main
struct iosApp: App {
    @State private var authState = AuthState()

    var body: some Scene {
        WindowGroup {
            if authState.isAuthenticated {
                HomeView()
                    .environment(authState)
            } else {
                LoginView()
                    .environment(authState)
            }
        }
    }
}

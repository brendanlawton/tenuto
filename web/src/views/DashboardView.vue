<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
      <span class="font-semibold text-gray-900">Tenuto</span>
      <button class="text-sm text-gray-600 hover:text-gray-900" @click="handleLogout">
        Sign out
      </button>
    </header>

    <main class="max-w-4xl mx-auto p-8">
      <h1 class="text-2xl font-semibold text-gray-900">
        Welcome, {{ auth.user?.name }}
      </h1>
      <p class="mt-2 text-gray-600">Your practice dashboard is coming soon.</p>
    </main>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()

async function handleLogout() {
  await auth.logout()
  router.push({ name: 'login' })
}
</script>

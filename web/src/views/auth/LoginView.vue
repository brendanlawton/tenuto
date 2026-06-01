<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-md space-y-8 p-8 bg-white rounded-2xl shadow-sm">
      <h1 class="text-2xl font-semibold text-gray-900">Sign in to Tenuto</h1>

      <form class="space-y-4" @submit.prevent="submit">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input
            v-model="form.email"
            type="email"
            autocomplete="email"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input
            v-model="form.password"
            type="password"
            autocomplete="current-password"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
          />
        </div>

        <p v-if="error" class="text-sm text-red-600">{{ error }}</p>

        <button
          type="submit"
          :disabled="loading"
          class="w-full py-2 px-4 bg-gray-900 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50"
        >
          {{ loading ? 'Signing in…' : 'Sign in' }}
        </button>
      </form>

      <div class="text-sm text-center space-y-1">
        <RouterLink to="/forgot-password" class="block text-gray-600 hover:text-gray-900">
          Forgot password?
        </RouterLink>
        <RouterLink to="/register" class="block text-gray-600 hover:text-gray-900">
          Don't have an account? Register
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()

const form = ref({ email: '', password: '' })
const loading = ref(false)
const error = ref('')

async function submit() {
  loading.value = true
  error.value = ''
  try {
    await auth.login(form.value)
    router.push({ name: 'dashboard' })
  } catch (e: any) {
    error.value = e.response?.data?.message ?? 'Login failed.'
  } finally {
    loading.value = false
  }
}
</script>

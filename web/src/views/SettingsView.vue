<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
      <span class="font-semibold text-gray-900">Tenuto</span>
      <RouterLink to="/dashboard" class="text-sm text-gray-600 hover:text-gray-900">
        Back to dashboard
      </RouterLink>
    </header>

    <main class="max-w-2xl mx-auto p-8 space-y-8">
      <h1 class="text-2xl font-semibold text-gray-900">Account Settings</h1>

      <section v-if="auth.user && !auth.user.has_password" class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
        <h2 class="text-lg font-medium text-gray-900">Add a password</h2>
        <p class="text-sm text-gray-600">
          Add email and password login to your account. You can still sign in with your social account afterwards.
        </p>

        <form class="space-y-4" @submit.prevent="submit">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">New password</label>
            <input
              v-model="form.password"
              type="password"
              autocomplete="new-password"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm password</label>
            <input
              v-model="form.password_confirmation"
              type="password"
              autocomplete="new-password"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
            />
          </div>

          <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
          <p v-if="success" class="text-sm text-green-600">{{ success }}</p>

          <button
            type="submit"
            :disabled="loading"
            class="w-full py-2 px-4 bg-gray-900 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50"
          >
            {{ loading ? 'Saving…' : 'Add password' }}
          </button>
        </form>
      </section>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

const form = ref({ password: '', password_confirmation: '' })
const loading = ref(false)
const error = ref('')
const success = ref('')

async function submit() {
  loading.value = true
  error.value = ''
  success.value = ''
  try {
    await auth.enrollPassword(form.value)
    success.value = 'Password added successfully.'
    form.value = { password: '', password_confirmation: '' }
  } catch (e: any) {
    error.value = e.response?.data?.errors?.password?.[0] ?? 'Failed to add password.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-md space-y-8 p-8 bg-white rounded-2xl shadow-sm">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900">Reset your password</h1>
        <p class="mt-1 text-sm text-gray-600">
          Enter your email and we'll send you a reset link.
        </p>
      </div>

      <form v-if="!sent" class="space-y-4" @submit.prevent="submit">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input
            v-model="email"
            type="email"
            autocomplete="email"
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
          {{ loading ? 'Sending…' : 'Send reset link' }}
        </button>
      </form>

      <p v-else class="text-sm text-green-700 bg-green-50 px-4 py-3 rounded-lg">
        {{ message }}
      </p>

      <RouterLink to="/login" class="block text-sm text-center text-gray-600 hover:text-gray-900">
        Back to sign in
      </RouterLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

const email = ref('')
const loading = ref(false)
const error = ref('')
const sent = ref(false)
const message = ref('')

async function submit() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await auth.forgotPassword(email.value)
    message.value = data.message
    sent.value = true
  } catch (e: any) {
    error.value = e.response?.data?.message ?? 'Something went wrong.'
  } finally {
    loading.value = false
  }
}
</script>

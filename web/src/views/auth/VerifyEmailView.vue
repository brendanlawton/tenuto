<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-md space-y-6 p-8 bg-white rounded-2xl shadow-sm">
      <h1 class="text-2xl font-semibold text-gray-900">Verify your email</h1>
      <p class="text-sm text-gray-600">
        We sent a verification link to <strong>{{ auth.user?.email }}</strong>. Click the link in
        that email to continue.
      </p>

      <p v-if="resent" class="text-sm text-green-700 bg-green-50 px-4 py-3 rounded-lg">
        Verification email resent.
      </p>

      <button
        :disabled="loading"
        class="w-full py-2 px-4 bg-gray-900 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50"
        @click="resend"
      >
        {{ loading ? 'Sending…' : 'Resend verification email' }}
      </button>

      <button class="w-full text-sm text-gray-600 hover:text-gray-900" @click="auth.logout().then(() => router.push({ name: 'login' }))">
        Sign out
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()

const loading = ref(false)
const resent = ref(false)

async function resend() {
  loading.value = true
  resent.value = false
  try {
    await auth.resendVerificationEmail()
    resent.value = true
  } finally {
    loading.value = false
  }
}
</script>

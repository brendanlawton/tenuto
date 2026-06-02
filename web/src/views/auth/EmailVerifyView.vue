<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-md space-y-6 p-8 bg-white rounded-2xl shadow-sm">
      <template v-if="status === 'verifying'">
        <h1 class="text-2xl font-semibold text-gray-900">Verifying your email…</h1>
        <p class="text-sm text-gray-600">Just a moment.</p>
      </template>

      <template v-else-if="status === 'success'">
        <h1 class="text-2xl font-semibold text-gray-900">Email verified</h1>
        <p class="text-sm text-gray-600">Redirecting you to your dashboard…</p>
      </template>

      <template v-else>
        <h1 class="text-2xl font-semibold text-gray-900">Verification failed</h1>
        <p class="text-sm text-gray-600">
          This link has expired or is invalid. Request a new one below.
        </p>

        <p v-if="resent" class="text-sm text-green-700 bg-green-50 px-4 py-3 rounded-lg">
          Verification email resent.
        </p>

        <button
          :disabled="resending"
          class="w-full py-2 px-4 bg-gray-900 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50"
          @click="resend"
        >
          {{ resending ? 'Sending…' : 'Resend verification email' }}
        </button>

        <RouterLink
          to="/verify-email"
          class="block text-sm text-center text-gray-600 hover:text-gray-900"
        >
          Back
        </RouterLink>
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const route = useRoute()
const router = useRouter()

type Status = 'verifying' | 'success' | 'error'

const status = ref<Status>('verifying')
const resending = ref(false)
const resent = ref(false)

onMounted(async () => {
  try {
    const { id, hash } = route.params as { id: string; hash: string }
    const queryParams = Object.fromEntries(
      Object.entries(route.query).map(([k, v]) => [k, String(v)]),
    )
    await auth.verifyEmail(id, hash, queryParams)
    status.value = 'success'
    router.push({ name: 'dashboard' })
  } catch {
    status.value = 'error'
  }
})

async function resend() {
  resending.value = true
  resent.value = false
  try {
    await auth.resendVerificationEmail()
    resent.value = true
  } finally {
    resending.value = false
  }
}
</script>

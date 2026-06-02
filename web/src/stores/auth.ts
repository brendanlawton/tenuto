import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import api from '@/api/client'

export interface User {
  id: number
  name: string
  email: string
  email_verified_at: string | null
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const initialized = ref(false)

  const isAuthenticated = computed(() => user.value !== null)
  const isVerified = computed(() => user.value?.email_verified_at !== null)

  async function fetchUser() {
    try {
      const { data } = await api.get<User>('/api/v1/user')
      user.value = data
    } catch {
      user.value = null
    } finally {
      initialized.value = true
    }
  }

  async function login(credentials: { email: string; password: string }) {
    await api.get('/sanctum/csrf-cookie')
    await api.post('/login', credentials)
    await fetchUser()
  }

  async function register(payload: {
    name: string
    email: string
    password: string
    password_confirmation: string
  }) {
    await api.get('/sanctum/csrf-cookie')
    await api.post('/register', payload)
    await fetchUser()
  }

  async function logout() {
    await api.post('/logout')
    user.value = null
  }

  async function forgotPassword(email: string) {
    await api.get('/sanctum/csrf-cookie')
    return api.post<{ message: string }>('/forgot-password', { email })
  }

  async function resetPassword(payload: {
    token: string
    email: string
    password: string
    password_confirmation: string
  }) {
    await api.get('/sanctum/csrf-cookie')
    return api.post<{ message: string }>('/reset-password', payload)
  }

  async function resendVerificationEmail() {
    return api.post('/email/verification-notification')
  }

  async function verifyEmail(id: string, hash: string, queryParams: Record<string, string>) {
    await api.get(`/email/verify/${id}/${hash}`, { params: queryParams })
    await fetchUser()
  }

  return {
    user,
    initialized,
    isAuthenticated,
    isVerified,
    fetchUser,
    login,
    register,
    logout,
    forgotPassword,
    resetPassword,
    resendVerificationEmail,
    verifyEmail,
  }
})

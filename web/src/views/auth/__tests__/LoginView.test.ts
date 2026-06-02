import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import LoginView from '../LoginView.vue'

vi.mock('@/stores/auth', () => ({
  useAuthStore: () => ({ login: vi.fn() }),
}))

vi.mock('vue-router', () => ({
  useRouter: () => ({ push: vi.fn() }),
  RouterLink: { template: '<a><slot /></a>' },
}))

describe('LoginView', () => {
  it('renders a Continue with Google link pointing at the API redirect', () => {
    const wrapper = mount(LoginView)

    const link = wrapper.findAll('a').find((a) => a.text().includes('Continue with Google'))

    expect(link?.exists()).toBe(true)
    expect(link?.attributes('href')).toBe('/auth/google/redirect')
  })
})

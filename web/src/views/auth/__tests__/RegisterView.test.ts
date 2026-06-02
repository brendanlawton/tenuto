import { describe, it, expect, vi } from 'vitest'
import { mount, flushPromises } from '@vue/test-utils'
import RegisterView from '../RegisterView.vue'

const mockRegister = vi.hoisted(() => vi.fn())

vi.mock('@/stores/auth', () => ({
  useAuthStore: () => ({ register: mockRegister }),
}))

vi.mock('vue-router', () => ({
  useRouter: () => ({ push: vi.fn() }),
  RouterLink: { template: '<a><slot /></a>' },
}))

describe('RegisterView', () => {
  it('renders a Continue with Google link pointing at the API redirect', () => {
    const wrapper = mount(RegisterView)

    const link = wrapper.findAll('a').find((a) => a.text().includes('Continue with Google'))

    expect(link?.exists()).toBe(true)
    expect(link?.attributes('href')).toBe('/auth/google/redirect')
  })

  it('shows a duplicate email message when registration returns a 422 email error', async () => {
    mockRegister.mockRejectedValue({
      response: { data: { errors: { email: ['The email has already been taken.'] } } },
    })

    const wrapper = mount(RegisterView)
    await wrapper.find('form').trigger('submit')
    await flushPromises()

    expect(wrapper.text()).toContain('An account with this email already exists. Sign in instead.')
  })
})

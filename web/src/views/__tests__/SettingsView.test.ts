import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import SettingsView from '../SettingsView.vue'

const mockUser = { id: 1, name: 'Test User', email: 'test@example.com', has_password: false }
const mockEnrollPassword = vi.fn()

vi.mock('@/stores/auth', () => ({
  useAuthStore: () => ({
    user: mockUser,
    enrollPassword: mockEnrollPassword,
  }),
}))

vi.mock('vue-router', () => ({
  RouterLink: { template: '<a><slot /></a>' },
}))

describe('SettingsView', () => {
  it('renders the Add a password form when has_password is false', () => {
    mockUser.has_password = false
    const wrapper = mount(SettingsView)

    expect(wrapper.text()).toContain('Add a password')
    expect(wrapper.find('input[type="password"]').exists()).toBe(true)
  })

  it('does not render the Add a password form when has_password is true', () => {
    mockUser.has_password = true
    const wrapper = mount(SettingsView)

    expect(wrapper.text()).not.toContain('Add a password')
    expect(wrapper.find('input[type="password"]').exists()).toBe(false)
  })
})

import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount, flushPromises } from '@vue/test-utils'
import EmailVerifyView from '../EmailVerifyView.vue'

const mockVerifyEmail = vi.hoisted(() => vi.fn())
const mockResendVerificationEmail = vi.hoisted(() => vi.fn())
const mockPush = vi.hoisted(() => vi.fn())

vi.mock('@/stores/auth', () => ({
  useAuthStore: () => ({
    verifyEmail: mockVerifyEmail,
    resendVerificationEmail: mockResendVerificationEmail,
  }),
}))

vi.mock('vue-router', () => ({
  useRoute: () => ({
    params: { id: '1', hash: 'abc123hash' },
    query: { expires: '9999999999', signature: 'test-signature' },
  }),
  useRouter: () => ({ push: mockPush }),
  RouterLink: { template: '<a><slot /></a>' },
}))

describe('EmailVerifyView', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('calls verifyEmail with id, hash, and query params on mount', async () => {
    mockVerifyEmail.mockResolvedValue(undefined)

    mount(EmailVerifyView)
    await flushPromises()

    expect(mockVerifyEmail).toHaveBeenCalledWith('1', 'abc123hash', {
      expires: '9999999999',
      signature: 'test-signature',
    })
  })

  it('redirects to dashboard after successful verification', async () => {
    mockVerifyEmail.mockResolvedValue(undefined)

    mount(EmailVerifyView)
    await flushPromises()

    expect(mockPush).toHaveBeenCalledWith({ name: 'dashboard' })
  })

  it('shows error state and does not redirect when verification fails', async () => {
    mockVerifyEmail.mockRejectedValue(new Error('Forbidden'))

    const wrapper = mount(EmailVerifyView)
    await flushPromises()

    expect(wrapper.text()).toContain('Verification failed')
    expect(mockPush).not.toHaveBeenCalled()
  })
})

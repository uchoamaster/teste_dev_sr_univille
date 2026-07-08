import { computed, reactive } from 'vue'
import {
  apiRequest,
  clearStoredSession,
  formatApiError,
  loadStoredSession,
  persistSession,
} from '../lib/api'

const state = reactive({
  token: null,
  user: null,
  error: '',
  isSubmitting: false,
})

function applySession(token, user) {
  state.token = token
  state.user = user
  persistSession(token, user)
}

function clearSession() {
  state.token = null
  state.user = null
  state.error = ''
  clearStoredSession()
}

async function authenticate(endpoint, payload) {
  state.isSubmitting = true
  state.error = ''

  try {
    const response = await apiRequest(endpoint, {
      method: 'POST',
      body: payload,
    })

    applySession(response.token, response.user)
  } catch (error) {
    state.error = formatApiError(error)
    throw error
  } finally {
    state.isSubmitting = false
  }
}

export function useSessionStore() {
  return {
    state,
    isAuthenticated: computed(() => Boolean(state.token)),
    restore() {
      if (state.token) {
        return
      }

      const session = loadStoredSession()
      state.token = session.token
      state.user = session.user
    },
    async login(payload) {
      await authenticate('/login', payload)
    },
    async register(payload) {
      await authenticate('/register', payload)
    },
    async logout() {
      try {
        if (state.token) {
          await apiRequest('/logout', { method: 'POST', token: state.token })
        }
      } finally {
        clearSession()
      }
    },
    clearSession,
  }
}
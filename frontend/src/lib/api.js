const TOKEN_KEY = 'univille-bank.token'
const USER_KEY = 'univille-bank.user'

const baseUrl = import.meta.env.VITE_API_BASE_URL || '/api'

export class ApiError extends Error {
  constructor(status, message, details = null) {
    super(message)
    this.name = 'ApiError'
    this.status = status
    this.details = details
  }
}

function toUrl(path, query) {
  const normalizedPath = path.startsWith('/') ? path : `/${path}`
  const url = new URL(`${baseUrl}${normalizedPath}`, window.location.origin)

  Object.entries(query || {}).forEach(([key, value]) => {
    if (value !== '' && value !== null && value !== undefined) {
      url.searchParams.set(key, value)
    }
  })

  return url.toString()
}

export function loadStoredSession() {
  const token = window.localStorage.getItem(TOKEN_KEY)
  const user = window.localStorage.getItem(USER_KEY)

  return {
    token,
    user: user ? JSON.parse(user) : null,
  }
}

export function persistSession(token, user) {
  window.localStorage.setItem(TOKEN_KEY, token)
  window.localStorage.setItem(USER_KEY, JSON.stringify(user))
}

export function clearStoredSession() {
  window.localStorage.removeItem(TOKEN_KEY)
  window.localStorage.removeItem(USER_KEY)
}

export async function apiRequest(path, options = {}) {
  const { method = 'GET', body, query, token } = options
  const headers = {
    Accept: 'application/json',
  }

  if (body) {
    headers['Content-Type'] = 'application/json'
  }

  const activeToken = token || loadStoredSession().token

  if (activeToken) {
    headers.Authorization = `Bearer ${activeToken}`
  }

  const response = await fetch(toUrl(path, query), {
    method,
    headers,
    body: body ? JSON.stringify(body) : undefined,
  })

  const rawBody = await response.text()
  const data = rawBody ? JSON.parse(rawBody) : null

  if (!response.ok) {
    const message = data?.message || 'Unexpected API error.'
    throw new ApiError(response.status, message, data)
  }

  return data
}

export function formatApiError(error) {
  if (error instanceof ApiError) {
    const validationErrors = error.details?.errors

    if (validationErrors) {
      return Object.values(validationErrors).flat().join(' ')
    }

    return error.message
  }

  return 'Nao foi possivel concluir a operacao.'
}
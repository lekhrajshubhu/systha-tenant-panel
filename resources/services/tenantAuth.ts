export const TENANTPANEL_ACCESS_TOKEN_KEY = 'tenantpanel_access_token'
export const TENANTPANEL_ACCOUNT_KEY = 'tenantpanel_account'
const REFRESH_BUFFER_MS = 600_000

export type TenantAccount = {
  id: number | string
  name: string
  email: string
}

export const getAccessToken = (): string | null => {
  return localStorage.getItem(TENANTPANEL_ACCESS_TOKEN_KEY)
}

export const setAccessToken = (token: string): void => {
  localStorage.setItem(TENANTPANEL_ACCESS_TOKEN_KEY, token)
}

export const setAuthSession = (token: string, account: TenantAccount): void => {
  setAccessToken(token)
  localStorage.setItem(TENANTPANEL_ACCOUNT_KEY, JSON.stringify(account))
}

export const clearAuthSession = (): void => {
  localStorage.removeItem(TENANTPANEL_ACCESS_TOKEN_KEY)
  localStorage.removeItem(TENANTPANEL_ACCOUNT_KEY)
}

let refreshTimer: number | null = null
let refreshPromise: Promise<string | null> | null = null

const decodeBase64Url = (value: string): string | null => {
  try {
    const normalized = value.replace(/-/g, '+').replace(/_/g, '/')
    const padded = normalized.padEnd(Math.ceil(normalized.length / 4) * 4, '=')
    return atob(padded)
  } catch {
    return null
  }
}

export const getTokenExpiry = (token: string): number | null => {
  const parts = token.split('.')
  if (parts.length < 2) return null
  const payload = decodeBase64Url(parts[1])
  if (!payload) return null
  try {
    const data = JSON.parse(payload) as { exp?: number }
    return typeof data.exp === 'number' ? data.exp : null
  } catch {
    return null
  }
}

export const stopTokenAutoRefresh = (): void => {
  if (refreshTimer !== null) {
    window.clearTimeout(refreshTimer)
    refreshTimer = null
  }
}

export const startTokenAutoRefresh = (): void => {
  stopTokenAutoRefresh()
  const token = getAccessToken()
  if (!token) return

  const exp = getTokenExpiry(token)
  if (!exp) return

  const delay = exp * 1000 - Date.now() - REFRESH_BUFFER_MS
  const scheduleDelay = delay <= 0 ? 0 : delay

  refreshTimer = window.setTimeout(async () => {
    let refreshed: string | null = null

    try {
      refreshed = await refreshTokenOnce()
      if (refreshed) {
        setAccessToken(refreshed)
      }
    } catch {
      refreshed = null
    }

    if (refreshed) {
      startTokenAutoRefresh()
      return
    }

    // Refresh failed or returned no token; avoid a tight retry loop.
    clearAuthSession()
    stopTokenAutoRefresh()
    redirectToLogin()
  }, scheduleDelay)
}

const redirectToLogin = async (): Promise<void> => {
  try {
    const { router } = await import('../router')
    if (router.currentRoute.value.name !== 'tenant.login') {
      await router.push({ name: 'tenant.login' })
    }
  } catch {
    // If router isn't available yet, fall back to a hard redirect.
    window.location.href = '/tenant-panel/login'
  }
}

const refreshTokenOnce = async (): Promise<string | null> => {
  if (refreshPromise) return refreshPromise
  const { refreshTenantToken } = await import('./auth.api')
  refreshPromise = refreshTenantToken()
    .then((payload) => payload.access_token ?? null)
    .finally(() => {
      refreshPromise = null
    })
  return refreshPromise
}

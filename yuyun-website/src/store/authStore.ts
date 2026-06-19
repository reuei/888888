import { create } from 'zustand'
import type { Admin } from '../types/index.js'
import { login as loginApi, getMe } from '../api/index.js'

interface AuthState {
  admin: Admin | null
  token: string | null
  isAuthenticated: boolean
  login: (username: string, password: string) => Promise<boolean>
  logout: () => void
  checkAuth: () => Promise<void>
}

const token = localStorage.getItem('yuyun_admin_token')

export const useAuthStore = create<AuthState>((set) => ({
  admin: null,
  token,
  isAuthenticated: Boolean(token),

  login: async (username: string, password: string) => {
    const res = await loginApi(username, password)
    if (res.success && res.token) {
      localStorage.setItem('yuyun_admin_token', res.token as string)
      set({ admin: res.admin as Admin, token: res.token as string, isAuthenticated: true })
      return true
    }
    return false
  },

  logout: () => {
    localStorage.removeItem('yuyun_admin_token')
    set({ admin: null, token: null, isAuthenticated: false })
  },

  checkAuth: async () => {
    const currentToken = localStorage.getItem('yuyun_admin_token')
    if (!currentToken) {
      set({ isAuthenticated: false })
      return
    }
    try {
      const res = await getMe()
      if (res.success && res.admin) {
        set({ admin: res.admin as Admin, token: currentToken, isAuthenticated: true })
      } else {
        localStorage.removeItem('yuyun_admin_token')
        set({ admin: null, token: null, isAuthenticated: false })
      }
    } catch {
      localStorage.removeItem('yuyun_admin_token')
      set({ admin: null, token: null, isAuthenticated: false })
    }
  },
}))

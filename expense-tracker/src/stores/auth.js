import { defineStore } from 'pinia'
import api from '@/config/api'
import { login as loginService, register as registerService, logout as logoutService, fetchUser as fetchUserService } from '@/services/authService'

export const useAuthStore = defineStore('auth', {
  state: () => {
    const token = localStorage.getItem('token')
    if (token) {
      api.defaults.headers.common['Authorization'] = `Bearer ${token}`
    }
    return {
      user: null,
      token: token,
      loading: false,
      error: null
    }
  },

  getters: {
    isAuthenticated: (state) => !!state.token,
    currentUser: (state) => state.user
  },

  actions: {
    async login(email, password) {
      this.loading = true
      this.error = null
      try {
        const response = await loginService({ email, password })
        this.token = response.data.token
        this.user = response.data.user
        localStorage.setItem('token', this.token)
        api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Login failed'
        throw error
      } finally {
        this.loading = false
      }
    },

    async register(name, email, password, password_confirmation) {
      this.loading = true
      this.error = null
      try {
        const response = await registerService({
          name,
          email,
          password,
          password_confirmation
        })
        this.token = response.data.token
        this.user = response.data.user
        localStorage.setItem('token', this.token)
        api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Registration failed'
        throw error
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        if (this.token) {
          await logoutService()
        }
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.token = null
        this.user = null
        localStorage.removeItem('token')
        delete api.defaults.headers.common['Authorization']
      }
    },

    async fetchUser() {
      if (!this.token) return

      this.loading = true
      try {
        const response = await fetchUserService()
        this.user = response.data
      } catch (error) {
        this.token = null
        this.user = null
        localStorage.removeItem('token')
        delete api.defaults.headers.common['Authorization']
        throw error
      } finally {
        this.loading = false
      }
    },

    // Initialize auth state from localStorage
    initAuth() {
      const token = localStorage.getItem('token')
      if (token) {
        this.token = token
        api.defaults.headers.common['Authorization'] = `Bearer ${token}`
        this.fetchUser()
      }
    }
  }
})

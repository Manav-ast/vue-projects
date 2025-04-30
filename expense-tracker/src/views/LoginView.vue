<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Sign in to your account
        </h2>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <div class="rounded-md shadow-sm space-y-4">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
            <input
              id="email"
              v-model="email"
              name="email"
              type="email"
              required
              class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="Email address"
              @focus="clearError('email')"
            />
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
          </div>
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input
              id="password"
              v-model="password"
              name="password"
              type="password"
              required
              class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="Password"
              @focus="clearError('password')"
            />
            <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
          </div>
        </div>

        <div v-if="errors.general" class="text-center text-red-600 text-sm mb-4">
          {{ errors.general }}
        </div>

        <div>
          <button
            type="submit"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            :disabled="isLoading"
          >
            <span v-if="isLoading">Signing in...</span>
            <span v-else>Sign in</span>
          </button>
        </div>
      </form>
      <div class="text-center">
        <router-link to="/register" class="font-medium text-indigo-600 hover:text-indigo-500">
          Don't have an account? Register
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const isLoading = ref(false)
const errors = reactive({
  email: '',
  password: '',
  general: ''
})

const validateForm = () => {
  let isValid = true
  errors.email = ''
  errors.password = ''
  errors.general = ''

  // Email validation
  if (!email.value) {
    errors.email = 'Email is required'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    errors.email = 'Please enter a valid email address'
    isValid = false
  }

  // Password validation
  if (!password.value) {
    errors.password = 'Password is required'
    isValid = false
  } else if (password.value.length < 6) {
    errors.password = 'Password must be at least 6 characters long'
    isValid = false
  }

  return isValid
}

const clearError = (field) => {
  errors[field] = ''
}

const handleLogin = async () => {
  if (!validateForm()) return

  isLoading.value = true
  try {
    await authStore.login(email.value, password.value)
    router.push('/')
  } catch (error) {
    if (error.response?.data?.errors) {
      // Handle specific validation errors from the backend
      const backendErrors = error.response.data.errors
      Object.keys(backendErrors).forEach(key => {
        errors[key] = backendErrors[key][0]
      })
    } else {
      errors.general = error.response?.data?.message || 'An error occurred during login'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Create your account</h2>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
        <div class="rounded-md shadow-sm space-y-4">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input
              id="name"
              v-model="name"
              name="name"
              type="text"
              required
              class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="Full name"
              @focus="clearError('name')"
            />
            <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
          </div>
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
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input
              id="password_confirmation"
              v-model="password_confirmation"
              name="password_confirmation"
              type="password"
              required
              class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="Confirm password"
              @focus="clearError('password_confirmation')"
            />
            <p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">{{ errors.password_confirmation }}</p>
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
            <span v-if="isLoading">Creating account...</span>
            <span v-else>Register</span>
          </button>
        </div>
      </form>
      <div class="text-center">
        <router-link to="/login" class="font-medium text-indigo-600 hover:text-indigo-500">
          Already have an account? Sign in
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

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const isLoading = ref(false)

const errors = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  general: ''
})

const validateForm = () => {
  let isValid = true
  // Reset all errors
  Object.keys(errors).forEach(key => errors[key] = '')

  // Name validation
  if (!name.value) {
    errors.name = 'Name is required'
    isValid = false
  } else if (name.value.length < 2) {
    errors.name = 'Name must be at least 2 characters long'
    isValid = false
  }

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

  // Password confirmation validation
  if (!password_confirmation.value) {
    errors.password_confirmation = 'Please confirm your password'
    isValid = false
  } else if (password.value !== password_confirmation.value) {
    errors.password_confirmation = 'Passwords do not match'
    isValid = false
  }

  return isValid
}

const clearError = (field) => {
  errors[field] = ''
}

const handleRegister = async () => {
  if (!validateForm()) return

  isLoading.value = true
  try {
    await authStore.register(name.value, email.value, password.value, password_confirmation.value)
    router.push('/')
  } catch (error) {
    if (error.response?.data?.errors) {
      // Display validation errors from the backend
      const errorMessages = Object.values(error.response.data.errors).flat()
      alert(errorMessages.join('\n'))
    } else {
      console.error('Registration failed:', error)
    }
  }
}
</script>

<template>
  <template v-if="authStore.isAuthenticated">
    <span class="text-gray-500 mr-4">{{ authStore.currentUser?.name }}</span>
    <button
      @click="handleLogout"
      class="bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    >
      Logout
    </button>
  </template>
  <template v-else>
    <router-link
      to="/login"
      :class="[
        $route.path === '/login' ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700',
        'px-3 py-2 rounded-md text-sm font-medium',
      ]"
    >
      Login
    </router-link>
    <router-link
      to="/register"
      class="ml-4 bg-indigo-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-indigo-700"
    >
      Register
    </router-link>
  </template>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter, useRoute } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()
const $route = useRoute()

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

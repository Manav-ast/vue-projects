<template>
  <Transition name="mobile-menu">
    <div v-if="isOpen" class="sm:hidden mobile-menu">
      <div class="flex flex-col space-y-4">
        <router-link
          to="/"
          :class="[
            $route.path === '/' ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700',
            'px-3 py-2 rounded-md text-sm font-medium',
          ]"
          @click="$emit('close')"
        >
          Dashboard
        </router-link>
        <router-link
          to="/expenses"
          :class="[
            $route.path === '/expenses' ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700',
            'px-3 py-2 rounded-md text-sm font-medium',
          ]"
          @click="$emit('close')"
        >
          Expenses
        </router-link>
        <template v-if="authStore.isAuthenticated">
          <div class="border-t border-gray-200 pt-4">
            <span class="text-gray-500 block px-3 py-2">{{ authStore.currentUser?.name }}</span>
            <button
              @click="handleLogout"
              class="w-full text-left px-3 py-2 text-gray-500 hover:text-gray-700 text-sm font-medium"
            >
              Logout
            </button>
          </div>
        </template>
        <template v-else>
          <div class="border-t border-gray-200 pt-4">
            <router-link
              to="/login"
              class="block px-3 py-2 text-gray-500 hover:text-gray-700 text-sm font-medium"
              @click="$emit('close')"
            >
              Login
            </router-link>
            <router-link
              to="/register"
              class="block px-3 py-2 bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 rounded-md mt-2"
              @click="$emit('close')"
            >
              Register
            </router-link>
          </div>
        </template>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter, useRoute } from 'vue-router'

defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
})

defineEmits(['close'])

const authStore = useAuthStore()
const router = useRouter()
const $route = useRoute()

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.mobile-menu-enter-active,
.mobile-menu-leave-active {
  transition:
    opacity 0.3s ease,
    transform 0.3s ease;
}

.mobile-menu-enter-from,
.mobile-menu-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.mobile-menu {
  position: fixed;
  top: 64px;
  left: 0;
  right: 0;
  background-color: white;
  padding: 1rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  border-top: 1px solid #e5e7eb;
  z-index: 50;
}
</style>

<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-sm sticky top-0 nav-content">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <button @click="isMenuOpen = !isMenuOpen" class="sm:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
              <span class="sr-only">Open main menu</span>
              <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
            <div class="flex-shrink-0 flex items-center ml-2 sm:ml-0">
              <router-link to="/" class="text-xl font-bold text-indigo-600">
                Expense Tracker
              </router-link>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <router-link
                to="/"
                :class="[
                  $route.path === '/'
                    ? 'border-indigo-500 text-gray-900'
                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                  'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium',
                ]"
              >
                Dashboard
              </router-link>
              <router-link
                to="/expenses"
                :class="[
                  $route.path === '/expenses'
                    ? 'border-indigo-500 text-gray-900'
                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                  'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium',
                ]"
              >
                Expenses
              </router-link>
            </div>
          </div>
          <div class="hidden sm:ml-6 sm:flex sm:items-center">
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
                  $route.path === '/login'
                    ? 'text-indigo-600'
                    : 'text-gray-500 hover:text-gray-700',
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
          </div>
        </div>

        <!-- Mobile menu -->
        <Transition name="mobile-menu">
          <div v-if="isMenuOpen" class="sm:hidden mobile-menu">
            <div class="flex flex-col space-y-4">
              <router-link
                to="/"
                :class="[
                  $route.path === '/'
                    ? 'text-indigo-600'
                    : 'text-gray-500 hover:text-gray-700',
                  'px-3 py-2 rounded-md text-sm font-medium',
                ]"
                @click="isMenuOpen = false"
              >
                Dashboard
              </router-link>
              <router-link
                to="/expenses"
                :class="[
                  $route.path === '/expenses'
                    ? 'text-indigo-600'
                    : 'text-gray-500 hover:text-gray-700',
                  'px-3 py-2 rounded-md text-sm font-medium',
                ]"
                @click="isMenuOpen = false"
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
                    @click="isMenuOpen = false"
                  >
                    Login
                  </router-link>
                  <router-link
                    to="/register"
                    class="block px-3 py-2 bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 rounded-md mt-2"
                    @click="isMenuOpen = false"
                  >
                    Register
                  </router-link>
                </div>
              </template>
            </div>
          </div>
        </Transition>
      </div>
    </nav>

    <main>
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import { ref } from 'vue'

const authStore = useAuthStore()
const router = useRouter()
const isMenuOpen = ref(false)

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<style>
/* Mobile menu transition */
.mobile-menu-enter-active,
.mobile-menu-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.mobile-menu-enter-from,
.mobile-menu-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

@media (max-width: 640px) {
  .nav-content {
    padding: 1rem;
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
}
</style>

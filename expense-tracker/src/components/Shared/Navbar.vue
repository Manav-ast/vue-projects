<template>
  <nav class="bg-white shadow-sm sticky top-0 nav-content">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <button
            @click="isMenuOpen = !isMenuOpen"
            class="sm:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
          >
            <span class="sr-only">Open main menu</span>
            <svg
              class="h-6 w-6"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
          </button>
          <div class="flex-shrink-0 flex items-center ml-2 sm:ml-0">
            <router-link to="/" class="text-xl font-bold text-indigo-600">
              Expense Tracker
            </router-link>
          </div>
          <NavLinks class="hidden sm:ml-6 sm:flex sm:space-x-8" />
        </div>
        <div class="hidden sm:ml-6 sm:flex sm:items-center">
          <AuthButtons />
        </div>
      </div>

      <MobileMenu :is-open="isMenuOpen" @close="isMenuOpen = false" />
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue'
import NavLinks from './NavLinks.vue'
import AuthButtons from './AuthButtons.vue'
import MobileMenu from './MobileMenu.vue'

const isMenuOpen = ref(false)
</script>

<style scoped>

@media (max-width: 640px) {
  .nav-content {
    padding: 1rem;
  }
}
</style>

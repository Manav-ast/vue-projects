<template>
  <div :class="className">
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
    <router-link
      to="/chat"
      :class="[
        $route.path === '/chat'
          ? 'border-indigo-500 text-gray-900'
          : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
        'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium',
      ]"
    >
      Chat
    </router-link>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'

defineProps({
  className: {
    type: String,
    default: '',
  },
})

const $route = useRoute()
</script>

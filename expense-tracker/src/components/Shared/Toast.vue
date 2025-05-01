<template>
  <Transition
    enter-active-class="transform ease-out duration-300 transition"
    enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
    leave-active-class="transition ease-in duration-100"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="show"
      class="fixed left-1/2 transform -translate-x-1/2 z-50 pointer-events-auto flex w-[calc(100%-2rem)] sm:w-full max-w-md rounded-lg shadow-lg sm:left-auto sm:right-4 sm:transform-none"
      :class="[typeClasses, {'bottom-4': !isMobile, 'bottom-safe-4': isMobile}]"
    >
      <div class="w-0 flex-1 flex items-center p-4">
        <div class="w-full">
          <p class="text-sm font-medium text-white">
            {{ message }}
          </p>
        </div>
      </div>
      <div class="flex border-l border-gray-200">
        <button
          @click="$emit('close')"
          class="w-full border border-transparent rounded-none rounded-r-lg p-4 flex items-center justify-center text-sm font-medium text-white hover:text-gray-200 focus:outline-none"
        >
          Close
        </button>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    required: true,
  },
  message: {
    type: String,
    required: true,
  },
  type: {
    type: String,
    default: 'success',
    validator: (value) => ['success', 'error', 'info'].includes(value),
  },
})

defineEmits(['close'])

const isMobile = ref(false)

const checkMobile = () => {
  isMobile.value = window.innerWidth < 640
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
})

const typeClasses = computed(() => {
  switch (props.type) {
    case 'success':
      return 'bg-green-500'
    case 'error':
      return 'bg-red-500'
    case 'info':
      return 'bg-blue-500'
    default:
      return 'bg-green-500'
  }
})
</script>

<style scoped>
.bottom-safe-4 {
  bottom: calc(1rem + env(safe-area-inset-bottom));
}
</style>

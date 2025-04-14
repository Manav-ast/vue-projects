<template>
  <button
    :class="[
      'py-2 px-4 rounded font-medium focus:outline-none transition',
      variant === 'primary' ? 'bg-green-500 hover:bg-green-600 text-white' :
      variant === 'secondary' ? 'bg-blue-500 hover:bg-blue-600 text-white' :
      variant === 'danger' ? 'bg-red-500 hover:bg-red-600 text-white' :
      'bg-gray-300 hover:bg-gray-400 text-gray-800'
    ]"
    :disabled="disabled"
    :type="type"
    @click="$emit('click')">
    <slot></slot>
  </button>
</template>

<script setup>
defineProps({
  variant: {
    type: String,
    default: 'default'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'button'
  }
})

defineEmits(['click'])
</script>

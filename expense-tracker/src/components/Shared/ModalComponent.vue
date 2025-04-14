<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center"
  >
    <div class="bg-white rounded-lg p-6 w-96 shadow-lg">
      <h3 class="text-xl font-semibold mb-4">{{ title }}</h3>
      <div class="text-gray-600 mb-4">
        <slot></slot>
      </div>
      <div class="flex justify-end space-x-4">
        <Button @click="$emit('cancel')">Cancel</Button>
        <Button variant="danger" @click="$emit('confirm')">{{ confirmText }}</Button>
      </div>
    </div>
  </div>
</template>

<script setup>
import Button from './ButtonComponent.vue'

defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
  title: {
    type: String,
    default: 'Confirm',
  },
  confirmText: {
    type: String,
    default: 'Confirm',
  },
})

defineEmits(['cancel', 'confirm'])
</script>

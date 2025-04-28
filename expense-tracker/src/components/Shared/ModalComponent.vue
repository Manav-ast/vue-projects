<template>
  <div
    v-show="isOpen"
    class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50"
  >
    <div class="bg-white rounded-lg p-6 w-96 shadow-lg transform transition-all">
      <h3 class="text-xl font-semibold mb-4">{{ title }}</h3>
      <div class="text-gray-600 mb-4">
        <slot></slot>
      </div>
      <div class="flex justify-end space-x-4">
        <Button @click="$emit('cancel')">Cancel</Button>
        <Button :variant="confirmVariant" @click="$emit('confirm')">{{ confirmText }}</Button>
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
  confirmVariant: {
    type: String,
    default: 'danger',
  },
})

defineEmits(['cancel', 'confirm'])
</script>

<style scoped>
.fixed {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 50;
}

.bg-white {
  background-color: white;
  border-radius: 0.5rem;
  padding: 1.5rem;
  width: 24rem;
  box-shadow:
    0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
</style>

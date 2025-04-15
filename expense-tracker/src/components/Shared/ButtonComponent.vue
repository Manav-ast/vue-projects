<template>
  <button
    :class="[
      'button',
      `button-${variant}`,
      { 'button-disabled': disabled }
    ]"
    :disabled="disabled"
    :type="type"
    @click="$emit('click')"
  >
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

<style scoped>
.button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.625rem 1.25rem;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 0.5rem;
  transition: all 0.2s ease;
  cursor: pointer;
  border: none;
  gap: 0.5rem;
}

.button:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.button-primary {
  background-color: var(--primary-color);
  color: white;
}

.button-primary:hover {
  background-color: var(--primary-dark);
}

.button-secondary {
  background-color: var(--hover-color);
  color: var(--text-primary);
}

.button-secondary:hover {
  background-color: var(--border-color);
}

.button-danger {
  background-color: var(--error-color);
  color: white;
}

.button-danger:hover {
  background-color: #dc2626;
}

.button-default {
  background-color: var(--border-color);
  color: var(--text-primary);
}

.button-default:hover {
  background-color: var(--hover-color);
}

.button-disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.button-disabled:hover {
  transform: none;
}

@media (max-width: 640px) {
  .button {
    padding: 0.5rem 1rem;
  }
}
</style>

<template>
  <div class="mb-4">
    <label :for="id" class="block text-sm font-medium mb-2">{{ label }}</label>
    <input
      :id="id"
      :type="type"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :placeholder="placeholder"
      :step="step"
      class="w-full p-2 border border-gray-300 rounded"
      :class="{ 'border-red-500': error }"
    />
    <p v-if="error" class="text-red-500 text-sm mt-1">{{ error }}</p>
  </div>
</template>

<script setup>
defineProps({
  id: {
    type: String,
    required: true
  },
  label: {
    type: String,
    required: true
  },
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  placeholder: {
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: ''
  },
  step: {
    type: String,
    default: undefined
  }
})

defineEmits(['update:modelValue'])
</script>

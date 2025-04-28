<template>
  <div class="form-field">
    <label :for="id" class="block text-sm font-medium mb-2">{{ label }}</label>
    <select
      :id="id"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      class="input"
      @focus="$emit('focus')"
      :disabled="disabled"
    >
      <option v-if="placeholder" value="" disabled selected>{{ placeholder }}</option>
      <option
        v-for="option in options"
        :key="getOptionValue(option)"
        :value="getOptionValue(option)"
      >
        {{ getOptionLabel(option) }}
      </option>
    </select>
    <p v-if="error" class="error-message">{{ error }}</p>
  </div>
</template>

<script setup>
const props = defineProps({
  id: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    required: true,
  },
  modelValue: {
    type: [String, Number],
    default: '',
  },
  options: {
    type: Array,
    required: true,
  },
  optionValue: {
    type: String,
    default: null,
  },
  optionLabel: {
    type: String,
    default: null,
  },
  placeholder: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
})

const getOptionValue = (option) => {
  if (props.optionValue) {
    return option[props.optionValue]
  }
  return option
}

const getOptionLabel = (option) => {
  if (props.optionLabel) {
    return option[props.optionLabel]
  }
  return option
}

defineEmits(['update:modelValue', 'focus'])
</script>

<style scoped>
.form-field {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: 0.5rem;
  background-color: var(--surface-color);
  color: var(--text-primary);
  transition: all 0.2s ease;
}

.input:focus {
  outline: none;
  border-color: var(--primary-light);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.error-message {
  font-size: 0.875rem;
  color: var(--error-color);
}
</style>

<!-- components/Expense/EditExpenseModal.vue -->
<template>
  <Modal
    :is-open="isOpen"
    title="Edit Expense"
    confirm-text="Update"
    confirm-variant="secondary"
    @cancel="closeModal"
    @confirm="handleUpdate"
  >
    <form @submit.prevent="handleUpdate" class="space-y-4">
      <SelectField
        id="edit-group"
        label="Group"
        v-model="form.group"
        :options="groupStore.groups"
        :error="errors.group"
      />

      <InputField
        id="edit-name"
        label="Expense Name"
        v-model="form.name"
        placeholder="Expense Name"
        :error="errors.name"
      />

      <InputField
        id="edit-amount"
        label="Amount"
        type="number"
        v-model.number="form.amount"
        step="0.01"
        placeholder="Amount"
        :error="errors.amount"
      />

      <InputField
        id="edit-date"
        label="Date"
        type="date"
        v-model="form.date"
        :error="errors.date"
      />
    </form>
  </Modal>
</template>

<script setup>
import { reactive, watch, onMounted } from 'vue'
import { useGroupStore } from '../../stores/group.js'
import { useExpenseStore } from '../../stores/expense.js'
import Modal from '../Shared/ModalComponent.vue'
import InputField from '../Shared/InputField.vue'
import SelectField from '../Shared/SelectField.vue'
import { validateRequired, validatePositiveNumber, validateAll } from '../../utils/validation.js'

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
  expense: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits(['close', 'update'])

const groupStore = useGroupStore()
const expenseStore = useExpenseStore()

const form = reactive({
  group: '',
  name: '',
  amount: null,
  date: '',
})

const errors = reactive({
  group: '',
  name: '',
  amount: '',
  date: '',
})

// Load groups when component is mounted
onMounted(() => {
  groupStore.loadGroups()
})

const validateForm = () => {
  // Reset errors
  errors.group = ''
  errors.name = ''
  errors.amount = ''
  errors.date = ''

  // Validate each field using the validation utility
  errors.group = validateRequired(form.group) || ''
  errors.name = validateRequired(form.name) || ''
  errors.amount = validateAll(form.amount, [validateRequired, validatePositiveNumber]) || ''
  errors.date = validateRequired(form.date) || ''

  // Check if any errors exist
  return !Object.values(errors).some(error => error !== '')
}

const handleUpdate = () => {
  if (validateForm()) {
    const updatedExpense = {
      group: form.group,
      name: form.name,
      amount: parseFloat(form.amount),
      date: form.date,
    }

    const result = expenseStore.updateExpense(props.expense, updatedExpense)

    if (result.success) {
      emit('update', updatedExpense)
      closeModal()
    } else {
      errors.name = result.error || 'An error occurred while updating the expense'
    }
  }
}

const closeModal = () => {
  emit('close')
}

// Watch for changes in the expense prop
watch(
  () => props.expense,
  (newExpense) => {
    if (newExpense) {
      form.group = newExpense.group
      form.name = newExpense.name
      form.amount = newExpense.amount
      form.date = newExpense.date
    }
  },
  { immediate: true },
)
</script>

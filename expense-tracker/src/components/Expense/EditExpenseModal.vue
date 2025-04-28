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
        v-model="form.group_id"
        :options="groupOptions"
        option-value="id"
        option-label="name"
        placeholder="Select a group"
        :error="errors.group_id"
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

import { computed } from 'vue' // Import computed

const groupStore = useGroupStore()
const expenseStore = useExpenseStore()

// Format groups for the SelectField
const groupOptions = computed(() => groupStore.groups.map((g) => ({ id: g.id, name: g.name })))

const form = reactive({
  group_id: null, // Changed from group: ''
  name: '',
  amount: null,
  date: '',
})

const errors = reactive({
  group_id: '', // Changed from group
  name: '',
  amount: '',
  date: '',
})

// Load groups when component is mounted
onMounted(async () => {
  await groupStore.loadGroups()
})

const validateForm = () => {
  // Reset errors
  errors.group_id = ''
  errors.name = ''
  errors.amount = ''
  errors.date = ''

  let isValid = true

  // Validate group_id - check if it's a valid number and exists in groupOptions
  if (!form.group_id || !groupOptions.value.some((g) => g.id === Number(form.group_id))) {
    errors.group_id = 'Group is required.'
    isValid = false
  }

  if (!form.name || form.name.trim() === '') {
    errors.name = 'Expense name is required.'
    isValid = false
  }

  if (!form.amount || form.amount <= 0) {
    errors.amount = 'Amount must be greater than 0.'
    isValid = false
  }

  if (!form.date) {
    errors.date = 'Date is required.'
    isValid = false
  }

  return isValid
}

const handleUpdate = async () => {
  // Ensure groups are loaded before validation
  if (groupStore.groups.length === 0) {
    await groupStore.loadGroups()
  }

  if (validateForm()) {
    // Prepare payload with group_id
    const payload = {
      group_id: parseInt(form.group_id), // Ensure group_id is a number
      name: form.name.trim(),
      amount: parseFloat(form.amount),
      date: form.date,
    }

    // Pass the original expense object (props.expense) and the new payload
    const result = await expenseStore.updateExpense(props.expense, payload)

    if (result.success) {
      emit('update', payload)
      closeModal()
    } else {
      console.error('Failed to update expense:', result.error)
      // Map backend errors to form fields if they exist
      if (result.error) {
        if (result.error.toLowerCase().includes('name')) {
          errors.name = result.error
        } else if (result.error.toLowerCase().includes('amount')) {
          errors.amount = result.error
        } else if (result.error.toLowerCase().includes('date')) {
          errors.date = result.error
        } else if (result.error.toLowerCase().includes('group')) {
          errors.group_id = result.error
        }
      }
    }
  }
}

const closeModal = () => {
  emit('close')
}

// Watch for changes in the expense prop
watch(
  () => props.expense,
  async (newExpense) => {
    // Ensure groups are loaded
    if (groupStore.groups.length === 0) {
      await groupStore.loadGroups()
    }

    console.log('EditExpenseModal received expense:', newExpense)
    if (newExpense && newExpense.id) {
      // Set form values and ensure proper type conversion
      form.group_id = parseInt(newExpense.group_id) || null
      form.name = newExpense.name || ''
      form.amount = parseFloat(newExpense.amount) || null
      form.date = newExpense.date || ''

      // Reset errors when modal opens with new data
      Object.keys(errors).forEach((key) => (errors[key] = ''))

      console.log('Form populated with:', {
        group_id: form.group_id,
        name: form.name,
        amount: form.amount,
        date: form.date,
      })
    } else {
      // Reset form if expense is null or invalid
      form.group_id = null
      form.name = ''
      form.amount = null
      form.date = ''
    }
  },
  { immediate: true, deep: true },
)

// Add a watcher for isOpen prop
watch(
  () => props.isOpen,
  (newValue) => {
    console.log('Modal isOpen state changed:', newValue)
    if (!newValue) {
      // Reset form when modal closes
      form.group_id = null
      form.name = ''
      form.amount = null
      form.date = ''
      Object.keys(errors).forEach((key) => (errors[key] = ''))
    }
  },
)
</script>

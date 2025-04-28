<!-- components/Expense/ExpenseForm.vue -->
<template>
  <form @submit.prevent="handleSubmit" class="mb-6">
    <h2 class="text-xl text-center mt-6 mb-4">Expense Management</h2>

    <SelectField
      id="expense-group"
      label="Group"
      v-model="form.groupName"
      :options="groupOptions"
      :error="errors.group"
    />

    <InputField
      id="expense-name"
      label="Expense Name"
      v-model="form.name"
      placeholder="Expense Name"
      :error="errors.name"
    />

    <InputField
      id="expense-amount"
      label="Amount"
      type="number"
      v-model.number="form.amount"
      step="0.01"
      placeholder="Amount"
      :error="errors.amount"
    />

    <InputField
      id="expense-date"
      label="Date"
      type="date"
      v-model="form.date"
      :error="errors.date"
    />

    <div class="flex space-x-2">
      <Button variant="primary" type="submit" class="flex-1">
        {{ editMode ? 'Update Expense' : 'Add Expense' }}
      </Button>

      <Button v-if="editMode" variant="secondary" @click="cancelEdit" class="flex-1">
        Cancel
      </Button>
    </div>
  </form>
</template>

<script setup>
import InputField from '../Shared/InputField.vue'
import SelectField from '../Shared/SelectField.vue'
import { reactive, ref, onMounted, computed, watch } from 'vue'
import { useGroupStore } from '../../stores/group.js'
import { useExpenseStore } from '../../stores/expense.js'
import { getTodayDate } from '../../utils/formatDate.js'
import { validateRequired, validatePositiveNumber, validateAll } from '../../utils/validation.js'
import Button from '../Shared/ButtonComponent.vue'

const groupStore = useGroupStore()
const expenseStore = useExpenseStore()

const form = reactive({
  groupId: '',
  groupName: '',
  name: '',
  amount: null,
  date: getTodayDate(),
})

// Get group names for the select dropdown
const groupOptions = computed(() => {
  return groupStore.groups.map((group) => group.name)
})

// Watch for changes in groupName and update groupId accordingly
watch(
  () => form.groupName,
  (newGroupName) => {
    const selectedGroup = groupStore.groups.find((g) => g.name === newGroupName)
    form.groupId = selectedGroup ? selectedGroup.id : ''
  },
)

const originalExpense = ref(null)
const editMode = ref(false)

const errors = reactive({
  group: '',
  name: '',
  amount: '',
  date: '',
})

const validateForm = () => {
  // Reset errors
  errors.group = ''
  errors.name = ''
  errors.amount = ''
  errors.date = ''

  // Validate each field using the validation utility
  errors.group = validateRequired(form.groupName) || ''
  errors.name = validateRequired(form.name) || ''
  errors.amount = validateAll(form.amount, [validateRequired, validatePositiveNumber]) || ''
  errors.date = validateRequired(form.date) || ''

  // Check if any errors exist
  return !Object.values(errors).some((error) => error !== '')
}

const handleSubmit = async () => {
  if (validateForm()) {
    const newExpense = {
      group_id: form.groupId, // Send group_id to the API
      name: form.name,
      amount: parseFloat(form.amount),
      date: form.date,
    }

    let result

    if (editMode.value && originalExpense.value) {
      result = await expenseStore.updateExpense(originalExpense.value, newExpense)
    } else {
      result = await expenseStore.addExpense(newExpense)
    }

    if (result.success) {
      resetForm()
      // Force a reload of expenses to ensure UI is updated
      expenseStore.loadExpenses()
    } else {
      // Show the error message in the form
      errors.name = result.error || 'An error occurred while saving the expense'
    }
  }
}

const resetForm = () => {
  form.groupId = ''
  form.groupName = ''
  form.name = ''
  form.amount = null
  form.date = getTodayDate()
  originalExpense.value = null
  editMode.value = false
  // Clear any error messages
  errors.group = ''
  errors.name = ''
  errors.amount = ''
  errors.date = ''
}

const cancelEdit = () => {
  resetForm()
}

const startEdit = (expense) => {
  if (!expense) {
    console.error('No expense provided for editing')
    return
  }

  // Set form values from the expense object
  form.groupName = expense.group
  // Find the group id from the name
  const selectedGroup = groupStore.groups.find((g) => g.name === expense.group)
  form.groupId = selectedGroup ? selectedGroup.id : ''

  form.name = expense.name
  form.amount = expense.amount
  form.date = expense.date

  // Store a copy of the original expense
  originalExpense.value = { ...expense }

  editMode.value = true
}

// Expose the startEdit method for the parent component
defineExpose({ startEdit })

onMounted(() => {
  groupStore.loadGroups()
})
</script>

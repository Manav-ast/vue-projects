<!-- components/Expense/ExpenseForm.vue -->
<template>
  <form @submit.prevent="handleSubmit" class="mb-6">
    <h2 class="text-xl text-center mt-6 mb-4">Expense Management</h2>

    <div class="mb-4">
      <label for="expense-group" class="block text-sm font-medium mb-2">Group</label>
      <select
        id="expense-group"
        v-model="form.group"
        class="w-full p-2 border border-gray-300 rounded"
      >
        <option v-for="group in groupStore.groups" :key="group" :value="group">
          {{ group }}
        </option>
      </select>
      <p v-if="errors.group" class="text-red-500 text-sm mt-1">{{ errors.group }}</p>
    </div>

    <div class="mb-4">
      <label for="expense-name" class="block text-sm font-medium mb-2">Expense Name</label>
      <input
        type="text"
        id="expense-name"
        v-model="form.name"
        class="w-full p-2 border border-gray-300 rounded"
        placeholder="Expense Name"
      />
      <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
    </div>

    <div class="mb-4">
      <label for="expense-amount" class="block text-sm font-medium mb-2">Amount</label>
      <input
        type="number"
        id="expense-amount"
        v-model.number="form.amount"
        step="0.01"
        class="w-full p-2 border border-gray-300 rounded"
        placeholder="Amount"
      />
      <p v-if="errors.amount" class="text-red-500 text-sm mt-1">{{ errors.amount }}</p>
    </div>

    <div class="mb-4">
      <label for="expense-date" class="block text-sm font-medium mb-2">Date</label>
      <input
        type="date"
        id="expense-date"
        v-model="form.date"
        class="w-full p-2 border border-gray-300 rounded"
      />
      <p v-if="errors.date" class="text-red-500 text-sm mt-1">{{ errors.date }}</p>
    </div>

    <div class="flex space-x-2">
      <Button variant="primary" type="submit" class="flex-1">
        {{ editMode ? 'Update Expense' : 'Add Expense' }}
      </Button>

      <Button v-if="editMode" @click="cancelEdit" class="flex-1"> Cancel </Button>
    </div>
  </form>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useGroupStore } from '../../stores/group.js'
import { useExpenseStore } from '../../stores/expense.js'
import { getTodayDate } from '../../utils/formatDate.js'
import Button from '../Shared/ButtonComponent.vue'

const groupStore = useGroupStore()
const expenseStore = useExpenseStore()

const form = reactive({
  group: '',
  name: '',
  amount: null,
  date: getTodayDate(),
})

const originalExpense = ref(null)
const editMode = ref(false)

const errors = reactive({
  group: '',
  name: '',
  amount: '',
  date: '',
})

const validateForm = () => {
  let isValid = true

  // Reset errors
  errors.group = ''
  errors.name = ''
  errors.amount = ''
  errors.date = ''

  if (!form.group) {
    errors.group = 'Please select a group'
    isValid = false
  }

  if (!form.name) {
    errors.name = 'Please enter an expense name'
    isValid = false
  }

  if (!form.amount || form.amount <= 0) {
    errors.amount = 'Please enter a valid amount'
    isValid = false
  }

  if (!form.date) {
    errors.date = 'Please select a date'
    isValid = false
  }

  return isValid
}

const handleSubmit = () => {
  console.log('Form submitted. Edit mode:', editMode.value, 'Original:', originalExpense.value)

  if (validateForm()) {
    const newExpense = {
      group: form.group,
      name: form.name,
      amount: parseFloat(form.amount),
      date: form.date,
    }

    let result

    if (editMode.value && originalExpense.value) {
      console.log('Updating expense:', originalExpense.value, 'to', newExpense)
      result = expenseStore.updateExpense(originalExpense.value, newExpense)
      console.log('Update result:', result)
    } else {
      console.log('Adding new expense:', newExpense)
      result = expenseStore.addExpense(newExpense)
      console.log('Add result:', result)
    }

    if (result.success) {
      resetForm()
      // Force a reload of expenses to ensure UI is updated
      expenseStore.loadExpenses()
    } else {
      // Show the error message in the form
      errors.name = result.error || 'An error occurred while saving the expense'
      console.error('Error saving expense:', result.error)
    }
  }
}

const resetForm = () => {
  form.group = ''
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
  console.log('Starting edit for expense:', expense)

  if (!expense) {
    console.error('No expense provided for editing')
    return
  }

  // Convert Proxy to plain object if needed
  const plainExpense = JSON.parse(JSON.stringify(expense))

  // Ensure we have all required fields
  if (!plainExpense.group || !plainExpense.name || !plainExpense.amount || !plainExpense.date) {
    console.error('Invalid expense data:', plainExpense)
    return
  }

  // Set form values
  form.group = plainExpense.group
  form.name = plainExpense.name
  form.amount = plainExpense.amount
  form.date = plainExpense.date

  // Store a copy of the original expense
  originalExpense.value = {
    group: plainExpense.group,
    name: plainExpense.name,
    amount: plainExpense.amount,
    date: plainExpense.date,
  }

  editMode.value = true

  console.log(
    'Form updated:',
    form,
    'Original:',
    originalExpense.value,
    'Edit mode:',
    editMode.value,
  )
}

// Expose the startEdit method for the parent component
defineExpose({ startEdit })

onMounted(() => {
  groupStore.loadGroups()
})
</script>

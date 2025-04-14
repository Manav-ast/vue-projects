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

    <Button variant="primary" type="submit" class="w-full">Add Expense</Button>
  </form>
</template>

<script setup>
import { reactive, onMounted } from 'vue'
import { useGroupStore } from '../../stores/group'
import { useExpenseStore } from '../../stores/expense'
import { getTodayDate } from '../../utils/formatDate'
import Button from '../Shared/ButtonComponent.vue'

const groupStore = useGroupStore()
const expenseStore = useExpenseStore()

const form = reactive({
  group: '',
  name: '',
  amount: null,
  date: getTodayDate(),
})

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
  if (validateForm()) {
    const result = expenseStore.addExpense({
      group: form.group,
      name: form.name,
      amount: parseFloat(form.amount),
      date: form.date,
    })

    if (result.success) {
      // Reset form
      form.name = ''
      form.amount = null
      form.date = getTodayDate()
    } else {
      errors.name = result.error
    }
  }
}

onMounted(() => {
  groupStore.loadGroups()
})
</script>

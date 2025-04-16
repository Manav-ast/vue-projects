<template>
  <div class="section">
    <h2 class="section-title">Expense Management</h2>
    <form @submit.prevent="addExpense" class="form-group">
      <SelectField
        id="expense-group"
        label="Group"
        v-model="newExpense.group"
        :options="groups"
        placeholder="Select Group"
        :error="expenseErrors.group"
        @focus="clearExpenseErrorMessage('group')"
      />

      <InputField
        id="expense-name"
        label="Expense Name"
        v-model="newExpense.name"
        placeholder="Expense Name"
        :error="expenseErrors.name"
        @focus="clearExpenseErrorMessage('name')"
      />

      <InputField
        id="expense-amount"
        label="Amount"
        type="number"
        v-model.number="newExpense.amount"
        placeholder="Amount"
        :error="expenseErrors.amount"
        @focus="clearExpenseErrorMessage('amount')"
      />

      <InputField
        id="expense-date"
        label="Date"
        type="date"
        v-model="newExpense.date"
        :error="expenseErrors.date"
        @focus="clearExpenseErrorMessage('date')"
      />

      <Button type="submit" variant="primary" class="btn btn-primary w-full">
        Add Expense
      </Button>
    </form>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useGroupStore } from '../stores/group'
import { useExpenseStore } from '../stores/expense'
import { validateRequired, validatePositiveNumber, validateAll } from '../utils/validation.js'
import Button from './Shared/ButtonComponent.vue'
import InputField from './Shared/InputField.vue'
import SelectField from './Shared/SelectField.vue'

const groupStore = useGroupStore()
const expenseStore = useExpenseStore()

const groups = computed(() => groupStore.groups)

const newExpense = ref({
  group: '',
  name: '',
  amount: '',
  date: new Date().toISOString().split('T')[0],
})

const expenseErrors = ref({
  group: '',
  name: '',
  amount: '',
  date: '',
})

function clearExpenseErrorMessage(field) {
  expenseErrors.value[field] = ''
}

function validateExpense() {
  // Reset errors
  expenseErrors.value.group = ''
  expenseErrors.value.name = ''
  expenseErrors.value.amount = ''
  expenseErrors.value.date = ''

  // Validate each field using the validation utility
  expenseErrors.value.group = validateRequired(newExpense.value.group) || ''
  expenseErrors.value.name = validateRequired(newExpense.value.name) || ''
  expenseErrors.value.amount = validateAll(newExpense.value.amount, [validateRequired, validatePositiveNumber]) || ''
  expenseErrors.value.date = validateRequired(newExpense.value.date) || ''

  // Check if any errors exist
  let isValid = !Object.values(expenseErrors.value).some(error => error !== '')

  if (isValid) {
    const isDuplicate = expenseStore.isDuplicateExpense(
      newExpense.value.group,
      newExpense.value.name,
      newExpense.value.amount,
      newExpense.value.date,
    )

    if (isDuplicate) {
      expenseErrors.value.name = 'This expense is already added.'
      isValid = false
    }
  }

  return isValid
}

function addExpense() {
  if (validateExpense()) {
    expenseStore.addExpense({ ...newExpense.value })

    newExpense.value = {
      group: '',
      name: '',
      amount: '',
      date: new Date().toISOString().split('T')[0],
    }
  }
}
</script>

<style scoped>
.section {
  margin-bottom: 2.5rem;
}

.section-title {
  font-size: 1.25rem;
  font-weight: 500;
  color: var(--text-primary);
  text-align: center;
  margin-bottom: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
</style>

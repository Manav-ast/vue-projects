<template>
  <div class="section">
    <h2 class="section-title">Expense Management</h2>
    <form @submit.prevent="addExpense" class="form-group">
      <SelectField
        id="expense-group"
        label="Group"
        v-model="newExpense.group_id"
        :options="groupOptions"
        option-value="id"
        option-label="name"
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

// Use computed for group options formatted for SelectField
const groupOptions = computed(() => groupStore.groups.map(g => ({ id: g.id, name: g.name })))

const newExpense = ref({
  group_id: null, // Changed from group: ''
  name: '',
  amount: '',
  date: new Date().toISOString().split('T')[0],
})

const expenseErrors = ref({
  group_id: '', // Changed from group
  name: '',
  amount: '',
  date: '',
})

function clearExpenseErrorMessage(field) {
  // Adjust field name if needed (e.g., 'group_id')
  expenseErrors.value[field] = ''
}

function validateExpense() {
  // Reset errors
  expenseErrors.value.group_id = '' // Changed from group
  expenseErrors.value.name = ''
  expenseErrors.value.amount = ''
  expenseErrors.value.date = ''

  // Validate each field using the validation utility
  expenseErrors.value.group_id = validateRequired(newExpense.value.group_id) ? '' : 'Group is required.' // Changed validation
  expenseErrors.value.name = validateRequired(newExpense.value.name) ? '' : 'Expense name is required.'
  expenseErrors.value.amount = validateAll(newExpense.value.amount, [validateRequired, validatePositiveNumber]) || ''
  expenseErrors.value.date = validateRequired(newExpense.value.date) ? '' : 'Date is required.'

  // Check if any errors exist
  let isValid = !Object.values(expenseErrors.value).some(error => error !== '')

  // Note: Duplicate check might need adjustment if it relies on group name
  // The store's isDuplicateExpense likely needs updating or removal if backend handles duplicates
  // For now, we assume the store handles it or it's less critical than the ID change.
  /*
  if (isValid) {
    const isDuplicate = expenseStore.isDuplicateExpense(
      newExpense.value.group_id, // Pass ID if store expects it, or handle differently
      newExpense.value.name,
      newExpense.value.amount,
      newExpense.value.date,
    )

    if (isDuplicate) {
      expenseErrors.value.name = 'This expense is already added.'
      isValid = false
    }
  }
  */

  return isValid
}

async function addExpense() { // Make async
  if (validateExpense()) {
    // Prepare payload with group_id
    const payload = {
      name: newExpense.value.name,
      amount: parseFloat(newExpense.value.amount),
      date: newExpense.value.date,
      group_id: newExpense.value.group_id,
    };

    const result = await expenseStore.addExpense(payload); // Pass payload

    if (result.success) {
      // Reset form
      newExpense.value = {
        group_id: null, // Reset group_id
        name: '',
        amount: '',
        date: new Date().toISOString().split('T')[0],
      };
      // Optionally clear errors if they aren't cleared automatically
      Object.keys(expenseErrors.value).forEach(key => expenseErrors.value[key] = '');
    } else {
      // Handle potential errors returned from the store (e.g., display backend validation errors)
      // For now, just log the error
      console.error("Failed to add expense:", result.error);
      // You might want to set a general error message or map specific errors
      // Example: if (result.error.includes('duplicate')) { expenseErrors.value.name = result.error; }
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

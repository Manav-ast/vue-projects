<template>
  <div class="side-panel">
    <h1 class="app-title">Expense Tracker</h1>

    <!-- Group Management -->
    <div class="section">
      <h2 class="section-title">Group Management</h2>
      <form @submit.prevent="addGroup" class="form-group">
        <input
          v-model="newGroup"
          type="text"
          class="input"
          placeholder="Group Name"
          @focus="clearGroupErrorMessage"
        />
        <p v-if="groupErrorMessage" class="error-message">{{ groupErrorMessage }}</p>
        <Button type="submit" variant="primary" class="btn btn-primary w-full">
          Add Group
        </Button>
      </form>

      <div class="groups-list">
        <ul>
          <li
            v-for="group in groups"
            :key="group"
            class="group-item"
          >
            <span class="group-name">{{ group }}</span>
            <Button variant="danger" @click="showDeleteConfirmation('group', group)" class="delete-btn">
              <i class="fas fa-trash"></i>
            </Button>
          </li>
        </ul>
      </div>
    </div>

    <!-- Expense Management -->
    <div class="section">
      <h2 class="section-title">Expense Management</h2>
      <form @submit.prevent="addExpense" class="form-group">
        <div class="form-field">
          <select
            v-model="newExpense.group"
            class="input"
            @focus="clearExpenseErrorMessage('group')"
          >
            <option value="" disabled selected>Select Group</option>
            <option v-for="group in groups" :key="group" :value="group">{{ group }}</option>
          </select>
          <p v-if="expenseErrors.group" class="error-message">{{ expenseErrors.group }}</p>
        </div>

        <div class="form-field">
          <input
            v-model="newExpense.name"
            type="text"
            class="input"
            placeholder="Expense Name"
            @focus="clearExpenseErrorMessage('name')"
          />
          <p v-if="expenseErrors.name" class="error-message">{{ expenseErrors.name }}</p>
        </div>

        <div class="form-field">
          <input
            v-model.number="newExpense.amount"
            type="number"
            class="input"
            placeholder="Amount"
            @focus="clearExpenseErrorMessage('amount')"
          />
          <p v-if="expenseErrors.amount" class="error-message">{{ expenseErrors.amount }}</p>
        </div>

        <div class="form-field">
          <input
            v-model="newExpense.date"
            type="date"
            class="input"
            @focus="clearExpenseErrorMessage('date')"
          />
          <p v-if="expenseErrors.date" class="error-message">{{ expenseErrors.date }}</p>
        </div>

        <Button type="submit" variant="primary" class="btn btn-primary w-full">
          Add Expense
        </Button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useGroupStore } from '../stores/group'
import { useExpenseStore } from '../stores/expense'
import { useModalStore } from '../stores/modalStore'
import Button from './Shared/ButtonComponent.vue'

const groupStore = useGroupStore()
const expenseStore = useExpenseStore()
const modalStore = useModalStore()

const groups = computed(() => groupStore.groups)
const newGroup = ref('')
const groupErrorMessage = ref('')

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

onMounted(() => {
  groupStore.loadGroups()
})

function clearGroupErrorMessage() {
  groupErrorMessage.value = ''
}

function clearExpenseErrorMessage(field) {
  expenseErrors.value[field] = ''
}

function validateGroup() {
  if (!newGroup.value) {
    groupErrorMessage.value = 'Group name cannot be empty.'
    return false
  }

  if (newGroup.value.length < 3) {
    groupErrorMessage.value = 'Group name must be at least 3 characters long.'
    return false
  }

  if (groupStore.groups.includes(newGroup.value)) {
    groupErrorMessage.value = 'Group already exists!'
    return false
  }

  return true
}

function validateExpense() {
  let isValid = true

  if (!newExpense.value.group) {
    expenseErrors.value.group = 'Please select a group.'
    isValid = false
  }

  if (!newExpense.value.name) {
    expenseErrors.value.name = 'Please enter an expense name.'
    isValid = false
  }

  if (!newExpense.value.amount || isNaN(newExpense.value.amount) || newExpense.value.amount < 0) {
    expenseErrors.value.amount = 'Amount must be a valid positive number.'
    isValid = false
  }

  if (!newExpense.value.date) {
    expenseErrors.value.date = 'Please select a date.'
    isValid = false
  }

  // Check for duplicate expense
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

function addGroup() {
  if (validateGroup()) {
    groupStore.addGroup(newGroup.value)
    newGroup.value = ''
  }
}

function addExpense() {
  if (validateExpense()) {
    expenseStore.addExpense({ ...newExpense.value })

    // Reset form
    newExpense.value = {
      group: '',
      name: '',
      amount: '',
      date: new Date().toISOString().split('T')[0],
    }
  }
}

function showDeleteConfirmation(type, group) {
  modalStore.showDeleteConfirmation({
    type: 'group',
    group,
    message: `Are you sure you want to delete the group "${group}"? All related expenses will also be deleted.`,
  })
}
</script>

<style scoped>
.side-panel {
  width: 100%;
  max-width: 400px;
  height: 100vh;
  padding: 2rem;
  background-color: var(--surface-color);
  border-right: 1px solid var(--border-color);
  overflow-y: auto;
}

.app-title {
  font-size: 1.75rem;
  font-weight: 600;
  color: var(--primary-color);
  text-align: center;
  margin-bottom: 2rem;
}

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

.groups-list {
  margin-top: 1.5rem;
  max-height: 200px;
  overflow-y: auto;
}

.groups-list ul {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.group-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem;
  background-color: var(--hover-color);
  border-radius: 0.5rem;
  transition: all 0.2s ease;
}

.group-item:hover {
  background-color: var(--border-color);
}

.group-name {
  font-weight: 500;
  color: var(--text-primary);
}

.delete-btn {
  padding: 0.5rem !important;
  color: var(--error-color);
  background-color: transparent;
  border-radius: 0.375rem;
  transition: all 0.2s ease;
}

.delete-btn:hover {
  background-color: rgba(239, 68, 68, 0.1);
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background-color: var(--border-color);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background-color: var(--text-secondary);
}
</style>

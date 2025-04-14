<template>
  <div class="w-full md:w-1/4 p-4 border-r-2 border-gray-200">
    <h1 class="text-3xl font-semibold text-center mb-6">Expense Tracker</h1>

    <!-- Group Management -->
    <h2 class="text-xl text-center mb-4">Group Management</h2>
    <form @submit.prevent="addGroup">
      <input
        v-model="newGroup"
        type="text"
        class="w-full p-2 mb-4 border border-gray-300 rounded"
        placeholder="Group Name"
        @focus="clearGroupErrorMessage"
      />
      <p v-if="groupErrorMessage" class="text-red-500 text-sm mb-2">{{ groupErrorMessage }}</p>
      <button type="submit" class="w-full py-2 bg-green-500 text-white rounded hover:bg-green-600">
        Add Group
      </button>
    </form>

    <div class="overflow-y-auto h-36 mt-6">
      <ul class="space-y-2">
        <li
          v-for="group in groups"
          :key="group"
          class="flex justify-between items-center p-2 bg-gray-100 rounded"
        >
          {{ group }}
          <button @click="showDeleteConfirmation('group', group)" class="text-red-600">
            <i class="fas fa-trash"></i>
          </button>
        </li>
      </ul>
    </div>

    <!-- Expense Management -->
    <h2 class="text-xl text-center mt-6 mb-4">Expense Management</h2>
    <form @submit.prevent="addExpense">
      <select
        v-model="newExpense.group"
        class="w-full p-2 mb-4 border border-gray-300 rounded"
        @focus="clearExpenseErrorMessage('group')"
      >
        <option v-for="group in groups" :key="group" :value="group">{{ group }}</option>
      </select>
      <p v-if="expenseErrors.group" class="text-red-500 text-sm mb-2">{{ expenseErrors.group }}</p>

      <input
        v-model="newExpense.name"
        type="text"
        class="w-full p-2 mb-4 border border-gray-300 rounded"
        placeholder="Expense Name"
        @focus="clearExpenseErrorMessage('name')"
      />
      <p v-if="expenseErrors.name" class="text-red-500 text-sm mb-2">{{ expenseErrors.name }}</p>

      <input
        v-model.number="newExpense.amount"
        type="number"
        class="w-full p-2 mb-4 border border-gray-300 rounded"
        placeholder="Amount"
        @focus="clearExpenseErrorMessage('amount')"
      />
      <p v-if="expenseErrors.amount" class="text-red-500 text-sm mb-2">
        {{ expenseErrors.amount }}
      </p>

      <input
        v-model="newExpense.date"
        type="date"
        class="w-full p-2 mb-4 border border-gray-300 rounded"
        @focus="clearExpenseErrorMessage('date')"
      />
      <p v-if="expenseErrors.date" class="text-red-500 text-sm mb-2">{{ expenseErrors.date }}</p>

      <button type="submit" class="w-full py-2 bg-green-500 text-white rounded hover:bg-green-600">
        Add Expense
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useGroupStore } from '../stores/group'
import { useExpenseStore } from '../stores/expense'
import { useModalStore } from '../stores/modalStore'

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

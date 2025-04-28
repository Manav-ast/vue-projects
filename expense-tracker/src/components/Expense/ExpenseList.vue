<!-- components/Expense/ExpenseList.vue -->
<template>
  <div class="mt-8 mb-4">
    <h2 class="text-xl font-semibold mb-4">Your Expenses</h2>
    <div class="mb-4 flex justify-between items-center">
      <div class="flex gap-2">
        <input
          type="month"
          v-model="selectedMonth"
          class="border border-gray-300 rounded px-2 py-1"
          @change="handleMonthChange"
        />
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Search expenses..."
          class="border border-gray-300 rounded px-2 py-1"
          @input="handleSearch"
        />
      </div>
      <div class="flex gap-2">
        <button class="bg-blue-500 text-white px-3 py-1 rounded" @click="sortBy('name')">
          Sort by Name
        </button>
        <button class="bg-blue-500 text-white px-3 py-1 rounded" @click="sortBy('amount')">
          Sort by Amount
        </button>
      </div>
    </div>

    <div v-if="!expenseStore.filteredExpenses.length" class="text-center py-8 text-gray-500">
      No expenses found. Add some expenses to get started!
    </div>

    <div v-for="(expenses, groupName) in groupedExpenses" :key="groupName" class="mb-6">
      <div class="flex justify-between items-center mb-2">
        <h3 class="text-lg font-semibold">{{ groupName }}</h3>
        <span class="text-sm font-medium">
          Total: {{ formatCurrency(groupTotals[groupName] || 0) }}
        </span>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Name
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Amount
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Date
              </th>
              <th
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="expense in expenses" :key="expense.id">
              <td class="px-6 py-4 whitespace-nowrap">{{ expense.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">${{ expense.amount.toFixed(2) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(expense.date) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button
                  @click="editExpense(expense)"
                  class="text-indigo-600 hover:text-indigo-900 mr-2"
                >
                  <i class="fa-solid fa-pen"></i>
                </button>
                <button @click="confirmDelete(expense)" class="text-red-600 hover:text-red-900">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Modal
      :is-open="showDeleteModal"
      title="Confirm Deletion"
      confirm-text="Delete"
      @cancel="cancelDelete"
      @confirm="deleteExpense"
    >
      Are you sure you want to delete this expense?
    </Modal>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useExpenseStore } from '../../stores/expense.js'
import Modal from '../Shared/ModalComponent.vue'

const expenseStore = useExpenseStore()
const selectedMonth = ref(new Date().toISOString().slice(0, 7))
const searchQuery = ref('')
const showDeleteModal = ref(false)
const expenseToDelete = ref(null)

const emit = defineEmits(['edit', 'delete-success'])

const groupedExpenses = computed(() => expenseStore.groupedExpenses)
const groupTotals = computed(() => expenseStore.groupTotals)

// Format date from YYYY-MM-DD to readable format
const formatDate = (dateString) => {
  const date = new Date(dateString + 'T00:00:00')
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR',
  }).format(amount)
}

const handleMonthChange = () => {
  expenseStore.setSelectedMonth(selectedMonth.value)
}

const handleSearch = () => {
  expenseStore.setSearchQuery(searchQuery.value)
}

const sortBy = (criteria) => {
  expenseStore.sortExpenses(criteria)
}

const editExpense = (expense) => {
  console.log('Emitting edit event for expense:', expense)
  if (!expense || !expense.id) {
    console.error('Invalid expense data for editing:', expense)
    return
  }
  // Create a plain object from the Proxy
  const plainExpense = {
    id: expense.id,
    group_id: expense.group_id,
    name: expense.name,
    amount: expense.amount,
    date: expense.date,
    group: expense.group,
  }
  emit('edit', plainExpense)
}

const confirmDelete = (expense) => {
  expenseToDelete.value = expense
  showDeleteModal.value = true
}

const cancelDelete = () => {
  showDeleteModal.value = false
  expenseToDelete.value = null
}

const deleteExpense = async () => {
  const result = await expenseStore.deleteExpense(expenseToDelete.value)
  if (result.success) {
    emit('delete-success')
  } else {
    console.error(result.error)
  }
  showDeleteModal.value = false
  expenseToDelete.value = null
}
</script>

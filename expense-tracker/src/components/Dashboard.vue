<template>
  <div class="flex-1 sm:w-full md:w-3/4 p-4">
    <h2 class="text-xl font-semibold text-center mb-6">Dashboard</h2>
    <label for="month-selector" class="block text-sm font-medium mb-2">Select Month:</label>
    <input
      v-model="selectedMonth"
      type="month"
      id="month-selector"
      class="w-full p-2 border border-gray-300 rounded mb-6"
    />

    <div class="flex flex-col sm:flex-row sm:space-x-4 mb-6">
      <div class="flex-1 p-4 bg-gray-100 rounded-lg text-center mb-4 sm:mb-0">
        <p class="text-sm">Lifetime Expenses:</p>
        <span class="font-semibold text-lg">₹ {{ lifetimeExpenses }}</span>
      </div>
      <div class="flex-1 p-4 bg-gray-100 rounded-lg text-center mb-4 sm:mb-0">
        <p class="text-sm">Total this Month:</p>
        <span class="font-semibold text-lg">₹ {{ totalExpenses }}</span>
      </div>
      <div class="flex-1 p-4 bg-gray-100 rounded-lg text-center">
        <p class="text-sm">Highest this Month:</p>
        <span class="font-semibold text-lg">₹ {{ highestExpense }}</span>
      </div>
    </div>

    <!-- Real-time Search -->
    <label for="search" class="block text-sm font-medium mb-2"
      >Search by Group or Expense Name:</label
    >
    <input
      v-model="searchQuery"
      type="text"
      id="search"
      class="w-full p-2 border border-gray-300 rounded mb-6"
      placeholder="Search..."
    />

    <!-- Sort Buttons -->
    <div class="flex flex-wrap justify-center gap-4 mt-4">
      <button
        @click="sortBy('name')"
        class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 w-full sm:w-auto"
      >
        Sort by Name
      </button>
      <button
        @click="sortBy('amount')"
        class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 w-full sm:w-auto"
      >
        Sort by Amount
      </button>
    </div>

    <h3 class="text-lg font-semibold mt-8 mb-4">Expenses by Group</h3>

    <!-- Expenses Table -->
    <div class="overflow-x-auto mt-4 max-h-72">
      <div v-for="(expenses, group) in groupedExpenses" :key="group" class="mb-6">
        <h4 class="font-semibold text-xl">
          {{ group }} (Total: <span>₹ </span>{{ calculateGroupTotal(expenses) }})
        </h4>
        <table
          class="min-w-full bg-gray-100 border border-gray-300 rounded-lg mt-2 overflow-hidden"
        >
          <thead>
            <tr class="bg-green-500 text-white rounded-t-lg">
              <th class="py-2 px-4 text-left">Expense Name</th>
              <th class="py-2 px-4 text-left">Amount</th>
              <th class="py-2 px-4 text-left">Date</th>
              <th class="py-2 px-4 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="rounded-b-lg">
            <tr v-for="expense in expenses" :key="`${expense.name}-${expense.date}`">
              <td class="py-2 px-4">{{ expense.name }}</td>
              <td class="py-2 px-4"><span>₹ </span>{{ expense.amount }}</td>
              <td class="py-2 px-4">{{ expense.date }}</td>
              <td class="py-2 px-4 text-center">
                <button class="text-red-600" @click="showDeleteConfirmation(expense)">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useExpenseStore } from '../stores/expense'
import { useModalStore } from '../stores/modalStore'

const expenseStore = useExpenseStore()
const modalStore = useModalStore()

const selectedMonth = ref(new Date().toISOString().substring(0, 7))
const searchQuery = ref('')
const sortCriteria = ref({ field: null, direction: 'asc' })

// Load expenses when component mounts
onMounted(() => {
  expenseStore.loadExpenses()
})

// Watch for changes to update filtered expenses
watch([selectedMonth, searchQuery], () => {
  expenseStore.setFilters({
    month: selectedMonth.value,
    search: searchQuery.value,
  })
})

// Calculate dashboard metrics
const lifetimeExpenses = computed(() => {
  return expenseStore.allExpenses.reduce((total, expense) => total + expense.amount, 0)
})

const totalExpenses = computed(() => {
  return expenseStore.filteredExpenses.reduce((total, expense) => total + expense.amount, 0)
})

const highestExpense = computed(() => {
  if (expenseStore.filteredExpenses.length === 0) return 0
  return Math.max(...expenseStore.filteredExpenses.map((expense) => expense.amount))
})

// Group and sort expenses
const groupedExpenses = computed(() => {
  // First apply the sort
  let sortedExpenses = [...expenseStore.filteredExpenses]

  if (sortCriteria.value.field) {
    sortedExpenses.sort((a, b) => {
      if (sortCriteria.value.field === 'name') {
        return a.name.localeCompare(b.name)
      } else if (sortCriteria.value.field === 'amount') {
        return a.amount - b.amount
      }
      return 0
    })
  }

  // Then group the sorted expenses
  return sortedExpenses.reduce((groups, expense) => {
    if (!groups[expense.group]) {
      groups[expense.group] = []
    }
    groups[expense.group].push(expense)
    return groups
  }, {})
})

function calculateGroupTotal(expenses) {
  return expenses.reduce((total, expense) => total + expense.amount, 0)
}

function sortBy(field) {
  sortCriteria.value.field = field
}

function showDeleteConfirmation(expense) {
  modalStore.showDeleteConfirmation({
    type: 'expense',
    group: expense.group,
    name: expense.name,
    amount: expense.amount,
    date: expense.date,
    message: `Are you sure you want to delete the expense "${expense.name}" of ₹ ${expense.amount}?`,
  })
}
</script>

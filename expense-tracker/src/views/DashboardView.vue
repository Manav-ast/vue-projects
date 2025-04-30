<!-- views/Dashboard.vue -->
<template>
  <div class="mx-auto p-4">
    <div class="max-w-7xl mx-auto bg-white rounded-lg shadow-lg flex flex-col md:flex-row">
      <!-- Left Section (Smaller) -->
      <div class="w-full md:w-1/4 p-4 border-r-2 border-gray-200">
        <h1 class="text-3xl font-semibold text-center mb-6">Expense Tracker</h1>

        <GroupForm />
        <ExpenseForm />
      </div>

      <!-- Right Section -->
      <div class="flex-1 sm:w-full md:w-3/4 p-4">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-semibold">Dashboard</h2>
          <div class="flex gap-2">
            <button
              @click="handlePDFExport"
              class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
            >
              Export PDF
            </button>
            <button
              @click="handleCSVExport"
              class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors"
            >
              Export CSV
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <div class="stats-card">
            <h3 class="text-lg font-semibold mb-2">Lifetime Total</h3>
            <p class="text-3xl font-bold">{{ formatCurrency(lifetimeTotal) }}</p>
          </div>

          <div class="stats-card">
            <h3 class="text-lg font-semibold mb-2">Monthly Total</h3>
            <p class="text-3xl font-bold">{{ formatCurrency(monthlyTotal) }}</p>
          </div>

          <div class="stats-card">
            <h3 class="text-lg font-semibold mb-2">Highest Expense</h3>
            <p class="text-3xl font-bold">{{ formatCurrency(highestExpense) }}</p>
          </div>
        </div>

        <div class="mx-auto max-w-2xl">
          <ExpenseChart />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import { useExpenseStore } from '../stores/expense'
import GroupForm from '../components/Group/GroupForm.vue'
import ExpenseForm from '../components/Expense/ExpenseForm.vue'
import ExpenseChart from '../components/Dashboard/ExpenseChart.vue'
import { exportToPDF, exportToCSV } from '../utils/exportUtils'

const expenseStore = useExpenseStore()

const lifetimeTotal = computed(() => expenseStore.lifetimeTotal)
const monthlyTotal = computed(() => expenseStore.monthlyTotal)
const highestExpense = computed(() => expenseStore.highestMonthlyExpense)
const groupedExpenses = computed(() => expenseStore.groupedExpenses)
const groupTotals = computed(() => expenseStore.groupTotals)

onMounted(() => {
  expenseStore.loadExpenses()
})

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR',
  }).format(amount)
}

const handlePDFExport = async () => {
  try {
    console.log('Starting PDF export with data:', {
      groupedExpenses: groupedExpenses.value,
      groupTotals: groupTotals.value,
    })
    await exportToPDF(groupedExpenses.value, groupTotals.value)
  } catch (error) {
    console.error('PDF export failed:', error)
    alert('Failed to export PDF. Please check the console for details.')
  }
}

const handleCSVExport = () => {
  try {
    exportToCSV(groupedExpenses.value, groupTotals.value)
  } catch (error) {
    console.error('CSV export failed:', error)
    alert('Failed to export CSV. Please check the console for details.')
  }
}
</script>

<style scoped>
.dashboard {
  padding: 1.5rem;
}

.stats-card {
  background-color: var(--surface-color);
  border-radius: 0.5rem;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}
</style>

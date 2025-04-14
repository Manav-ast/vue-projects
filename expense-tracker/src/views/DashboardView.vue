<!-- views/Dashboard.vue -->
<template>
  <div class="max-w-7xl mx-auto bg-white rounded-lg shadow-lg flex flex-col md:flex-row">
    <!-- Left Section (Smaller) -->
    <div class="w-full md:w-1/4 p-4 border-r-2 border-gray-200">
      <h1 class="text-3xl font-semibold text-center mb-6">Expense Tracker</h1>

      <GroupForm />
      <ExpenseForm />
    </div>

    <!-- Right Section -->
    <div class="flex-1 sm:w-full md:w-3/4 p-4">
      <h2 class="text-xl font-semibold text-center mb-6">Dashboard</h2>

      <div class="flex flex-col sm:flex-row sm:space-x-4 mb-6">
        <div class="flex-1 p-4 bg-gray-100 rounded-lg text-center mb-4 sm:mb-0">
          <p class="text-sm">Lifetime Expenses:</p>
          <span class="font-semibold text-lg">₹ {{ expenseStore.lifetimeTotal }}</span>
        </div>
        <div class="flex-1 p-4 bg-gray-100 rounded-lg text-center mb-4 sm:mb-0">
          <p class="text-sm">Total this Month:</p>
          <span class="font-semibold text-lg">₹ {{ expenseStore.monthlyTotal }}</span>
        </div>
        <div class="flex-1 p-4 bg-gray-100 rounded-lg text-center">
          <p class="text-sm">Highest this Month:</p>
          <span class="font-semibold text-lg">₹ {{ expenseStore.highestMonthlyExpense }}</span>
        </div>
      </div>

      <ExpenseList />
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useExpenseStore } from '../stores/expense'
import GroupForm from '../components/Expense/GroupForm.vue'
import ExpenseForm from '../components/Expense/ExpenseForm.vue'
import ExpenseList from '../components/Expense/ExpenseList.vue'

const expenseStore = useExpenseStore()

onMounted(() => {
  expenseStore.loadExpenses()
})
</script>

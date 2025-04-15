<!-- views/DashboardView.vue -->
<template>
  <div class="max-w-7xl mx-auto bg-white rounded-lg shadow-lg flex flex-col md:flex-row">
    <!-- Left Section (Smaller) -->
    <div class="w-full md:w-1/4 p-4 border-r-2 border-gray-200">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold">Expense Tracker</h1>
      </div>

      <GroupForm />
      <ExpenseForm ref="expenseFormRef" />
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

      <ExpenseList 
        @edit-expense="handleEditExpense" 
        :show-header="false" 
        :show-navigation="false"
        :use-form-edit="true"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useExpenseStore } from '../stores/expense'
import GroupForm from '../components/Expense/GroupForm.vue'
import ExpenseForm from '../components/Expense/ExpenseForm.vue'
import ExpenseList from '../components/Expense/ExpenseList.vue'

const expenseStore = useExpenseStore()
const expenseFormRef = ref(null)

const handleEditExpense = (expense) => {
  // Scroll the page to the expense form
  window.scrollTo({
    top: 0,
    behavior: 'smooth',
  })

  // After a short delay to allow scroll, focus the form and populate it
  setTimeout(() => {
    if (expenseFormRef.value && expenseFormRef.value.startEdit) {
      expenseFormRef.value.startEdit(expense)
    }
  }, 300) // Reduced delay to 300ms for better responsiveness
}

onMounted(() => {
  expenseStore.loadExpenses()
})
</script>

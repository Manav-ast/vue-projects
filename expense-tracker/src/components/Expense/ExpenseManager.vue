<!-- components/Expense/ExpenseManager.vue -->
<template>
  <div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold text-center my-6">Expense Tracker</h1>

    <ExpenseForm ref="expenseForm" />

    <ExpenseList @edit="handleEditExpense" />

    <div class="mt-8 mb-6">
      <h2 class="text-xl font-semibold mb-4">Summary</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-blue-100 p-4 rounded shadow">
          <h3 class="text-lg font-medium text-blue-800">Monthly Total</h3>
          <p class="text-2xl font-bold">${{ expenseStore.monthlyTotal.toFixed(2) }}</p>
        </div>
        <div class="bg-green-100 p-4 rounded shadow">
          <h3 class="text-lg font-medium text-green-800">Lifetime Total</h3>
          <p class="text-2xl font-bold">${{ expenseStore.lifetimeTotal.toFixed(2) }}</p>
        </div>
        <div class="bg-purple-100 p-4 rounded shadow">
          <h3 class="text-lg font-medium text-purple-800">Highest Expense</h3>
          <p class="text-2xl font-bold">${{ expenseStore.highestMonthlyExpense.toFixed(2) }}</p>
        </div>
      </div>
    </div>

    <EditExpenseModal
      :is-open="isEditModalOpen"
      :expense="expenseToEdit"
      @close="closeEditModal"
      @update="handleExpenseUpdate"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue' // Keep existing ref and onMounted
import ExpenseForm from './ExpenseForm.vue'
import ExpenseList from './ExpenseList.vue'
import EditExpenseModal from './EditExpenseModal.vue' // Import the edit modal
import { useExpenseStore } from '../../stores/expense.js'
import { useGroupStore } from '../../stores/group.js'

const expenseStore = useExpenseStore()
const groupStore = useGroupStore()
// const expenseForm = ref(null) // Removed as per failed replace block's apparent intent

const isEditModalOpen = ref(false)
const expenseToEdit = ref(null)

// Original handleEditExpense logic is replaced by the new one
function handleEditExpense(expense) {
  console.log('handleEditExpense called in Manager with:', expense)
  if (!expense || !expense.id) {
    console.error('No expense provided for editing')
    return
  }
  // Create a plain object from the expense data
  expenseToEdit.value = {
    id: expense.id,
    group_id: expense.group_id,
    name: expense.name,
    amount: expense.amount,
    date: expense.date,
    group: expense.group,
  }
  isEditModalOpen.value = true
  console.log('Modal state updated:', {
    isOpen: isEditModalOpen.value,
    expense: expenseToEdit.value,
  })
}

function closeEditModal() {
  isEditModalOpen.value = false
  expenseToEdit.value = null
}

async function handleExpenseUpdate(updatedExpense) {
  // Close the modal first
  closeEditModal()
  // Refresh the expenses list
  await expenseStore.loadExpenses()
  console.log('Expense updated and data refreshed:', updatedExpense)
}

// Keep original onMounted
onMounted(async () => {
  // First load groups
  await groupStore.loadGroups()

  // Then load expenses (which need groups for proper display)
  await expenseStore.loadExpenses()
})
</script>

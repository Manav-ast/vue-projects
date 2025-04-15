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

      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-4">Recent Expenses</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full bg-gray-100 border border-gray-300 rounded-lg">
            <thead>
              <tr class="bg-green-500 text-white">
                <th class="py-2 px-4 text-left">Group</th>
                <th class="py-2 px-4 text-left">Expense Name</th>
                <th class="py-2 px-4 text-left">Amount</th>
                <th class="py-2 px-4 text-left">Date</th>
                <th class="py-2 px-4 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="expense in recentExpenses"
                :key="`${expense.group}-${expense.name}-${expense.date}`"
              >
                <td class="py-2 px-4">{{ expense.group }}</td>
                <td class="py-2 px-4">{{ expense.name }}</td>
                <td class="py-2 px-4">₹ {{ expense.amount }}</td>
                <td class="py-2 px-4">{{ expense.date }}</td>
                <td class="py-2 px-4 text-center">
                  <div class="flex justify-center space-x-2">
                    <button
                      class="text-blue-600"
                      @click="handleEditExpense(expense)"
                      title="Edit Expense"
                    >
                      ✏️
                    </button>
                    <button
                      class="text-red-600"
                      @click="confirmDelete(expense)"
                      title="Delete Expense"
                    >
                      🗑️
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="recentExpenses.length === 0" class="text-center py-8 bg-gray-100 rounded">
          <p class="text-gray-500">No expenses found.</p>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Modal
      :is-open="showDeleteModal"
      title="Confirm Deletion"
      confirm-text="Delete"
      @cancel="cancelDelete"
      @confirm="deleteExpense"
    >
      Are you sure you want to delete the expense "{{ expenseToDelete?.name }}" of ₹
      {{ expenseToDelete?.amount }}?
    </Modal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useExpenseStore } from '../stores/expense'
import GroupForm from '../components/Expense/GroupForm.vue'
import ExpenseForm from '../components/Expense/ExpenseForm.vue'
import Modal from '../Shared/ModalComponent.vue'

const expenseStore = useExpenseStore()
const expenseFormRef = ref(null)
const showDeleteModal = ref(false)
const expenseToDelete = ref(null)

const recentExpenses = computed(() => {
  return expenseStore.expenses.slice(-5).reverse()
})

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
  }, 500)
}

const confirmDelete = (expense) => {
  expenseToDelete.value = expense
  showDeleteModal.value = true
}

const cancelDelete = () => {
  showDeleteModal.value = false
  expenseToDelete.value = null
}

const deleteExpense = () => {
  if (expenseToDelete.value) {
    expenseStore.deleteExpense(expenseToDelete.value)
    showDeleteModal.value = false
    expenseToDelete.value = null
  }
}

onMounted(() => {
  expenseStore.loadExpenses()
})
</script>

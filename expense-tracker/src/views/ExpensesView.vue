<!-- views/Expenses.vue -->
<template>
  <div class="max-w-7xl mx-auto p-4">
    <div class="bg-white rounded-lg shadow-lg p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Expenses</h1>
        <button
          @click="showAddModal = true"
          class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors"
        >
          Add Expense
        </button>
      </div>

      <ExpenseList
        @edit="handleEdit"
        @delete-success="showToast('Expense deleted successfully', 'success')"
      />
    </div>

    <AddExpenseModal
      :is-open="showAddModal"
      @close="showAddModal = false"
      @added="handleExpenseAdded"
    />

    <EditExpenseModal
      :is-open="showEditModal"
      :expense="expenseToEdit"
      @close="closeEditModal"
      @update="handleExpenseUpdated"
    />

    <ToastNotification
      :show="toast.show"
      :message="toast.message"
      :type="toast.type"
      @close="hideToast"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { useExpenseStore } from '../stores/expense'
import ExpenseList from '../components/Expense/ExpenseList.vue'
import AddExpenseModal from '../components/Expense/AddExpenseModal.vue'
import EditExpenseModal from '../components/Expense/EditExpenseModal.vue'
import ToastNotification from '../components/Shared/Toast.vue'

const expenseStore = useExpenseStore()
const showAddModal = ref(false)
const showEditModal = ref(false)
const expenseToEdit = ref(null)

const toast = reactive({
  show: false,
  message: '',
  type: 'success',
})

onMounted(() => {
  expenseStore.loadExpenses()
})

const showToast = (message, type = 'success') => {
  toast.message = message
  toast.type = type
  toast.show = true
  // Auto hide after 3 seconds
  setTimeout(() => {
    hideToast()
  }, 3000)
}

const hideToast = () => {
  toast.show = false
}

const handleExpenseAdded = () => {
  showAddModal.value = false
  expenseStore.loadExpenses() // Refresh the list
  showToast('Expense added successfully')
}

const handleEdit = (expense) => {
  expenseToEdit.value = expense
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
  expenseToEdit.value = null
}

const handleExpenseUpdated = () => {
  closeEditModal()
  expenseStore.loadExpenses() // Refresh the list
  showToast('Expense updated successfully')
}
</script>

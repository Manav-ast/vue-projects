<!-- components/Expense/ExpenseList.vue -->
<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-lg font-semibold">Expenses by Group</h3>
      <div class="flex gap-2">
        <!-- Conditional routing -->
        <router-link 
          v-if="$route.path !== '/'" 
          to="/" 
          class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
          Go to Dashboard
        </router-link>
        <router-link 
          v-if="$route.path !== '/expenses'" 
          to="/expenses" 
          class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
          All Expenses
        </router-link>
      </div>
    </div>

    <div class="flex flex-col mb-6 sm:flex-row sm:space-x-4">
      <div class="mb-4 sm:mb-0">
        <label for="month-selector" class="block text-sm font-medium mb-2">Select Month:</label>
        <input
          type="month"
          id="month-selector"
          v-model="selectedMonth"
          class="w-full p-2 border border-gray-300 rounded"
        />
      </div>

      <div class="flex-1">
        <label for="search" class="block text-sm font-medium mb-2">Search by Group or Expense Name:</label>
        <input
          type="text"
          id="search"
          v-model="searchQuery"
          class="w-full p-2 border border-gray-300 rounded"
          placeholder="Search..."
        />
      </div>
    </div>

    <div class="flex flex-wrap justify-center gap-4 mb-6">
      <Button variant="secondary" @click="sortExpenses('name')" class="w-full sm:w-auto">
        Sort by Name
      </Button>
      <Button variant="secondary" @click="sortExpenses('amount')" class="w-full sm:w-auto">
        Sort by Amount
      </Button>
    </div>

    <div class="overflow-x-auto mt-4 max-h-[70vh]">
      <div v-for="(expenses, group) in groupedExpenses" :key="group" class="mb-6">
        <h4 class="font-semibold text-xl">
          {{ group }} (Total: ₹ {{ calculateGroupTotal(expenses) }})
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
            <tr
              v-for="expense in expenses"
              :key="`${expense.name}-${expense.amount}-${expense.date}`"
            >
              <td class="py-2 px-4">{{ expense.name }}</td>
              <td class="py-2 px-4">₹ {{ expense.amount }}</td>
              <td class="py-2 px-4">{{ expense.date }}</td>
              <td class="py-2 px-4 text-center">
                <div class="flex justify-center space-x-2">
                  <button
                    class="text-blue-600"
                    @click="handleEdit(expense)"
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

      <div
        v-if="Object.keys(groupedExpenses).length === 0"
        class="text-center py-8 bg-gray-100 rounded"
      >
        <p class="text-gray-500">No expenses found for the selected filters.</p>
      </div>
    </div>

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

    <EditExpenseModal
      :is-open="showEditModal"
      :expense="expenseToEdit"
      @close="closeEditModal"
      @update="handleExpenseUpdate"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, defineEmits } from 'vue'
import { useExpenseStore } from '../../stores/expense.js'
import { useRoute } from 'vue-router'
import Button from '../Shared/ButtonComponent.vue'
import Modal from '../Shared/ModalComponent.vue'
import EditExpenseModal from './EditExpenseModal.vue'

const expenseStore = useExpenseStore()
const route = useRoute()
const selectedMonth = ref(new Date().toISOString().slice(0, 7))
const searchQuery = ref('')
const showDeleteModal = ref(false)
const expenseToDelete = ref(null)
const showEditModal = ref(false)
const expenseToEdit = ref(null)

// Define emits
const emit = defineEmits(['edit-expense'])

const groupedExpenses = computed(() => expenseStore.groupedExpenses)

const calculateGroupTotal = (expenses) => {
  return expenses.reduce((total, expense) => total + expense.amount, 0)
}

// Method to handle edit button click
// This will either use the modal or emit the event based on props
const handleEdit = (expense) => {
  console.log('Edit button clicked for expense:', expense)
  
  // Check if we're on the dashboard page - use direct form update
  if (route.path === '/') {
    emit('edit-expense', expense) 
    console.log('Emitted edit-expense event')
  } else {
    // Use modal approach on other pages
    openEditModal(expense)
  }
}

const openEditModal = (expense) => {
  expenseToEdit.value = expense
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
  expenseToEdit.value = null
}

const handleExpenseUpdate = () => {
  expenseStore.loadExpenses()
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

const sortExpenses = (criteria) => {
  expenseStore.sortExpenses(criteria)
}

watch(selectedMonth, (newValue) => {
  expenseStore.setSelectedMonth(newValue)
})

watch(searchQuery, (newValue) => {
  expenseStore.setSearchQuery(newValue)
})

onMounted(() => {
  expenseStore.loadExpenses()
  expenseStore.setSelectedMonth(selectedMonth.value)
})
</script>
<!-- components/Expense/ExpenseList.vue -->
<template>
  <div class="mt-8 mb-4">
    <h2 class="text-xl font-semibold mb-4">Your Expenses</h2>
    <div class="mb-4 sm:flex sm:justify-between sm:items-center space-y-4 sm:space-y-0">
      <div class="flex flex-col sm:flex-row gap-2">
        <InputField
          id="month-filter"
          type="month"
          label=""
          v-model="selectedMonth"
          @update:modelValue="handleMonthChange"
        />
        <InputField
          id="search-filter"
          type="text"
          label=""
          v-model="searchQuery"
          placeholder="Search expenses..."
          @update:modelValue="handleSearch"
        />
      </div>
      <div class="flex gap-2">
        <Button
          variant="primary"
          @click="sortBy('name')"
          class="flex-1 sm:flex-none"
        >
          Sort by Name
        </Button>
        <Button
          variant="primary"
          @click="sortBy('amount')"
          class="flex-1 sm:flex-none"
        >
          Sort by Amount
        </Button>
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
      <!-- Desktop View -->
      <div class="hidden sm:block">
        <TableComponent
          :headers="[
            { key: 'name', label: 'Name' },
            { key: 'amount', label: 'Amount' },
            { key: 'date', label: 'Date' },
            { key: 'actions', label: 'Actions' }
          ]"
          :data="expenses"
          row-key="id"
          empty-message="No expenses found. Add some expenses to get started!"
        >
          <template #amount="{ row }">
            {{ formatCurrency(row.amount) }}
          </template>
          <template #date="{ row }">
            {{ formatDate(row.date) }}
          </template>
          <template #actions="{ row }">
            <div class="flex gap-2">
              <button @click="editExpense(row)" class="text-indigo-600 hover:text-indigo-900">
                <i class="fa-solid fa-pen"></i>
              </button>
              <button @click="confirmDelete(row)" class="text-red-600 hover:text-red-900">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </template>
        </TableComponent>
      </div>
      <!-- Mobile View -->
      <div class="sm:hidden space-y-4">
        <div v-for="expense in expenses" :key="expense.id" class="bg-white rounded-lg shadow p-4 space-y-2">
          <div class="flex justify-between items-start">
            <h4 class="font-medium text-gray-900">{{ expense.name }}</h4>
            <div class="flex space-x-2">
              <button @click="editExpense(expense)" class="text-indigo-600 hover:text-indigo-900">
                <i class="fa-solid fa-pen"></i>
              </button>
              <button @click="confirmDelete(expense)" class="text-red-600 hover:text-red-900">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
          <div class="text-sm text-gray-600">{{ formatDate(expense.date) }}</div>
          <div class="text-lg font-semibold text-gray-900">${{ expense.amount.toFixed(2) }}</div>
        </div>
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
import InputField from '../Shared/InputField.vue'
import Button from '../Shared/ButtonComponent.vue'
import TableComponent from '../Shared/TableComponent.vue'

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

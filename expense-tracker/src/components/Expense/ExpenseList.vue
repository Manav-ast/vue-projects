<!-- components/Expense/ExpenseList.vue -->
<template>
  <div>
    <div v-if="showHeader" class="flex justify-between items-center mb-6">
      <h3 class="text-lg font-semibold">Expenses by Group</h3>
      <div v-if="showNavigation" class="flex gap-2">
        <!-- Conditional routing -->
        <Button
          v-if="$route.path !== '/'"
          variant="secondary"
          @click="$router.push('/')"
        >
          Go to Dashboard
        </Button>
        <Button
          v-if="$route.path !== '/expenses'"
          variant="secondary"
          @click="$router.push('/expenses')"
        >
          All Expenses
        </Button>
      </div>
    </div>

    <div class="flex flex-col mb-6 sm:flex-row sm:space-x-4">
      <div class="mb-4 sm:mb-0">
        <InputField
          id="month-selector"
          type="month"
          label="Select Month:"
          v-model="selectedMonth"
        />
      </div>

      <div class="flex-1">
        <InputField
          id="search"
          type="text"
          label="Search by Group or Expense Name:"
          v-model="searchQuery"
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

        <Table
          :headers="tableHeaders"
          :data="expenses"
          row-key="name"
          class="mt-2"
          empty-message="No expenses found for the selected filters."
        >
          <template #amount="{ row }">
            ₹ {{ row.amount }}
          </template>
          <template #actions="{ row }">
            <div class="flex justify-center space-x-2">
              <Button
                variant="secondary"
                @click="handleEdit(row)"
                class="!p-1 text-blue-600"
                title="Edit Expense"
              >
                <i class="fa-solid fa-pen"></i>
              </Button>
              <Button
                variant="danger"
                @click="confirmDelete(row)"
                class="!p-1"
                title="Delete Expense"
              >
                <font-awesome-icon icon="trash" />
              </Button>
            </div>
          </template>
        </Table>
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
      v-if="!useFormEdit"
      :is-open="showEditModal"
      :expense="expenseToEdit"
      @close="closeEditModal"
      @update="handleExpenseUpdate"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, defineEmits, defineProps } from 'vue'
import { useExpenseStore } from '../../stores/expense.js'
import Button from '../Shared/ButtonComponent.vue'
import Modal from '../Shared/ModalComponent.vue'
import EditExpenseModal from './EditExpenseModal.vue'
import InputField from '../Shared/InputField.vue'
import Table from '../Shared/TableComponent.vue'

const props = defineProps({
  showHeader: {
    type: Boolean,
    default: true
  },
  showNavigation: {
    type: Boolean,
    default: true
  },
  useFormEdit: {
    type: Boolean,
    default: false
  }
})

const expenseStore = useExpenseStore()
const selectedMonth = ref(new Date().toISOString().slice(0, 7))
const searchQuery = ref('')
const showDeleteModal = ref(false)
const expenseToDelete = ref(null)
const showEditModal = ref(false)
const expenseToEdit = ref(null)

// Define emits
const emit = defineEmits(['edit-expense'])

const groupedExpenses = computed(() => expenseStore.groupedExpenses)

const tableHeaders = [
  { key: 'name', label: 'Expense Name' },
  { key: 'amount', label: 'Amount' },
  { key: 'date', label: 'Date' },
  { key: 'actions', label: 'Actions', align: 'text-center' }
]

const calculateGroupTotal = (expenses) => {
  return expenses.reduce((total, expense) => total + expense.amount, 0)
}

// Method to handle edit button click
const handleEdit = (expense) => {
  if (props.useFormEdit) {
    emit('edit-expense', expense)
  } else {
    openEditModal(expense)
  }
}

const openEditModal = (expense) => {
  expenseToEdit.value = { ...expense } // Create a copy of the expense object
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
  expenseToEdit.value = null
}

const handleExpenseUpdate = () => {
  expenseStore.loadExpenses()
  closeEditModal()
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

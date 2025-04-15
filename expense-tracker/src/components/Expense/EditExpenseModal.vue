<!-- components/Expense/EditExpenseModal.vue -->
<template>
  <Modal
    :is-open="isOpen"
    title="Edit Expense"
    confirm-text="Update"
    @cancel="closeModal"
    @confirm="handleUpdate"
  >
    <form @submit.prevent="handleUpdate" class="space-y-4">
      <div>
        <label for="edit-group" class="block text-sm font-medium mb-2">Group</label>
        <select
          id="edit-group"
          v-model="form.group"
          class="w-full p-2 border border-gray-300 rounded"
        >
          <option v-for="group in groupStore.groups" :key="group" :value="group">
            {{ group }}
          </option>
        </select>
        <p v-if="errors.group" class="text-red-500 text-sm mt-1">{{ errors.group }}</p>
      </div>

      <div>
        <label for="edit-name" class="block text-sm font-medium mb-2">Expense Name</label>
        <input
          type="text"
          id="edit-name"
          v-model="form.name"
          class="w-full p-2 border border-gray-300 rounded"
          placeholder="Expense Name"
        />
        <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
      </div>

      <div>
        <label for="edit-amount" class="block text-sm font-medium mb-2">Amount</label>
        <input
          type="number"
          id="edit-amount"
          v-model.number="form.amount"
          step="0.01"
          class="w-full p-2 border border-gray-300 rounded"
          placeholder="Amount"
        />
        <p v-if="errors.amount" class="text-red-500 text-sm mt-1">{{ errors.amount }}</p>
      </div>

      <div>
        <label for="edit-date" class="block text-sm font-medium mb-2">Date</label>
        <input
          type="date"
          id="edit-date"
          v-model="form.date"
          class="w-full p-2 border border-gray-300 rounded"
        />
        <p v-if="errors.date" class="text-red-500 text-sm mt-1">{{ errors.date }}</p>
      </div>
    </form>
  </Modal>
</template>

<script setup>
import { reactive, watch, onMounted } from 'vue'
import { useGroupStore } from '../../stores/group.js'
import { useExpenseStore } from '../../stores/expense.js'
import Modal from '../Shared/ModalComponent.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
  expense: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits(['close', 'update'])

const groupStore = useGroupStore()
const expenseStore = useExpenseStore()

const form = reactive({
  group: '',
  name: '',
  amount: null,
  date: '',
})

const errors = reactive({
  group: '',
  name: '',
  amount: '',
  date: '',
})

// Load groups when component is mounted
onMounted(() => {
  groupStore.loadGroups()
})

const validateForm = () => {
  let isValid = true

  // Reset errors
  errors.group = ''
  errors.name = ''
  errors.amount = ''
  errors.date = ''

  if (!form.group) {
    errors.group = 'Please select a group'
    isValid = false
  }

  if (!form.name) {
    errors.name = 'Please enter an expense name'
    isValid = false
  }

  if (!form.amount || form.amount <= 0) {
    errors.amount = 'Please enter a valid amount'
    isValid = false
  }

  if (!form.date) {
    errors.date = 'Please select a date'
    isValid = false
  }

  return isValid
}

const handleUpdate = () => {
  if (validateForm()) {
    const updatedExpense = {
      group: form.group,
      name: form.name,
      amount: parseFloat(form.amount),
      date: form.date,
    }

    const result = expenseStore.updateExpense(props.expense, updatedExpense)

    if (result.success) {
      emit('update', updatedExpense)
      closeModal()
    } else {
      errors.name = result.error || 'An error occurred while updating the expense'
    }
  }
}

const closeModal = () => {
  emit('close')
}

// Watch for changes in the expense prop
watch(
  () => props.expense,
  (newExpense) => {
    if (newExpense) {
      form.group = newExpense.group
      form.name = newExpense.name
      form.amount = newExpense.amount
      form.date = newExpense.date
    }
  },
  { immediate: true },
)
</script>

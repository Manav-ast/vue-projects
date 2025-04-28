<template>
  <Modal
    :is-open="isOpen"
    title="Add Expense"
    confirm-text="Add"
    confirm-variant="primary"
    @cancel="closeModal"
    @confirm="handleSubmit"
  >
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <SelectField
        id="group"
        label="Group"
        v-model="form.group_id"
        :options="groupOptions"
        option-value="id"
        option-label="name"
        placeholder="Select a group"
        :error="errors.group_id"
      />

      <InputField
        id="name"
        label="Expense Name"
        v-model="form.name"
        placeholder="Enter expense name"
        :error="errors.name"
      />

      <InputField
        id="amount"
        label="Amount"
        type="number"
        v-model.number="form.amount"
        step="0.01"
        placeholder="Enter amount"
        :error="errors.amount"
      />

      <InputField id="date" label="Date" type="date" v-model="form.date" :error="errors.date" />
    </form>
  </Modal>
</template>

<script setup>
import { reactive, computed, onMounted } from 'vue'
import { useGroupStore } from '../../stores/group'
import { useExpenseStore } from '../../stores/expense'
import Modal from '../Shared/ModalComponent.vue'
import InputField from '../Shared/InputField.vue'
import SelectField from '../Shared/SelectField.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits(['close', 'added'])

const groupStore = useGroupStore()
const expenseStore = useExpenseStore()

// Format groups for the SelectField
const groupOptions = computed(() => groupStore.groups.map((g) => ({ id: g.id, name: g.name })))

const form = reactive({
  group_id: null,
  name: '',
  amount: null,
  date: new Date().toISOString().slice(0, 10), // Set default date to today
})

const errors = reactive({
  group_id: '',
  name: '',
  amount: '',
  date: '',
})

// Load groups when component is mounted
onMounted(async () => {
  await groupStore.loadGroups()
})

const validateForm = () => {
  // Reset errors
  errors.group_id = ''
  errors.name = ''
  errors.amount = ''
  errors.date = ''

  let isValid = true

  // Validate group_id - check if it's a valid number and exists in groupOptions
  if (!form.group_id || !groupOptions.value.some((g) => g.id === Number(form.group_id))) {
    errors.group_id = 'Group is required.'
    isValid = false
  }

  if (!form.name || form.name.trim() === '') {
    errors.name = 'Expense name is required.'
    isValid = false
  }

  if (!form.amount || form.amount <= 0) {
    errors.amount = 'Amount must be greater than 0.'
    isValid = false
  }

  if (!form.date) {
    errors.date = 'Date is required.'
    isValid = false
  }

  return isValid
}

const handleSubmit = async () => {
  // Ensure groups are loaded before validation
  if (groupStore.groups.length === 0) {
    await groupStore.loadGroups()
  }

  if (validateForm()) {
    const payload = {
      group_id: parseInt(form.group_id),
      name: form.name.trim(),
      amount: parseFloat(form.amount),
      date: form.date,
    }

    const result = await expenseStore.addExpense(payload)

    if (result.success) {
      emit('added')
      closeModal()
      resetForm()
    } else {
      // Handle error
      if (result.error) {
        if (result.error.toLowerCase().includes('name')) {
          errors.name = result.error
        } else if (result.error.toLowerCase().includes('amount')) {
          errors.amount = result.error
        } else if (result.error.toLowerCase().includes('date')) {
          errors.date = result.error
        } else if (result.error.toLowerCase().includes('group')) {
          errors.group_id = result.error
        }
      }
    }
  }
}

const closeModal = () => {
  emit('close')
  resetForm()
}

const resetForm = () => {
  form.group_id = null
  form.name = ''
  form.amount = null
  form.date = new Date().toISOString().slice(0, 10)
  Object.keys(errors).forEach((key) => (errors[key] = ''))
}
</script>

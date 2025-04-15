<template>
  <Teleport to="body">
    <div
      v-if="modalStore.isModalVisible"
      class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg p-6 w-96 shadow-lg">
        <h3 class="text-xl font-semibold mb-4">Confirm Deletion</h3>
        <p class="text-gray-600 mb-4">{{ modalStore.deleteTarget.message }}</p>
        <div class="flex justify-end space-x-4">
          <Button @click="modalStore.hideModal" variant="secondary">
            Cancel
          </Button>
          <Button @click="confirmDelete" variant="danger">
            Delete
          </Button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { useModalStore } from '../stores/modalStore'
import { useGroupStore } from '../stores/group'
import { useExpenseStore } from '../stores/expense'
import Button from './Shared/ButtonComponent.vue'

const modalStore = useModalStore()
const groupStore = useGroupStore()
const expenseStore = useExpenseStore()

function confirmDelete() {
  const target = modalStore.deleteTarget

  if (target.type === 'expense') {
    expenseStore.deleteExpense(target.group, target.name, target.amount, target.date)
  } else if (target.type === 'group') {
    groupStore.deleteGroup(target.group)
    expenseStore.deleteExpensesByGroup(target.group)
  }

  modalStore.hideModal()
}
</script>

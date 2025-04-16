<template>
  <ModalComponent
    :isOpen="modalStore.isModalVisible"
    title="Confirm Deletion"
    confirmText="Delete"
    confirmVariant="danger"
    @cancel="modalStore.hideModal"
    @confirm="confirmDelete"
  >
    {{ modalStore.deleteTarget?.message }}
  </ModalComponent>
</template>

<script setup>
import { useModalStore } from '../../stores/modalStore'
import { useGroupStore } from '../../stores/group'
import { useExpenseStore } from '../../stores/expense'
import ModalComponent from './ModalComponent.vue'

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

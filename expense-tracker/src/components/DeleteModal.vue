<template>
  <Teleport to="body">
    <div
      v-if="modalStore.isModalVisible"
      class="modal-overlay"
      @click="modalStore.hideModal"
    >
      <div class="modal-content" @click.stop>
        <h3 class="modal-title">Confirm Deletion</h3>
        <p class="modal-message">{{ modalStore.deleteTarget.message }}</p>
        <div class="modal-actions">
          <Button @click="modalStore.hideModal" variant="secondary" class="btn btn-secondary">
            Cancel
          </Button>
          <Button @click="confirmDelete" variant="danger" class="btn btn-danger">
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

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background-color: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
  padding: 1rem;
}

.modal-content {
  background-color: var(--surface-color);
  border-radius: 1rem;
  padding: 2rem;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  transform: translateY(0);
  transition: transform 0.2s ease;
  animation: modal-appear 0.2s ease;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.modal-message {
  color: var(--text-secondary);
  margin-bottom: 1.5rem;
  line-height: 1.5;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.btn-danger {
  background-color: var(--error-color) !important;
  color: white !important;
}

.btn-danger:hover {
  background-color: #dc2626 !important;
}

@keyframes modal-appear {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 640px) {
  .modal-content {
    margin: 1rem;
    padding: 1.5rem;
  }
}
</style>

<template>
  <div class="section">
    <h2 class="section-title">Group Management</h2>
    <form @submit.prevent="addGroup" class="form-group">
      <InputField
        id="group-name"
        label="Group Name"
        v-model="newGroup"
        placeholder="Group Name"
        :error="groupErrorMessage"
        @focus="clearGroupErrorMessage"
      />
      <Button type="submit" variant="primary" class="btn btn-primary w-full"> Add Group </Button>
    </form>

    <div class="groups-list">
      <ul>
        <li v-for="group in groups" :key="group" class="group-item">
          <span class="group-name">{{ group.name }}</span>
          <div>
            <Button variant="secondary" @click="editGroup(group)" class="edit-btn mr-2">
              <i class="fas fa-edit"></i>
            </Button>
            <Button
              variant="danger"
              @click="showDeleteConfirmation('group', group.name)"
              class="delete-btn"
            >
              <i class="fas fa-trash"></i>
            </Button>
          </div>
        </li>
      </ul>
    </div>

    <ToastNotification
      :show="toast.show"
      :message="toast.message"
      :type="toast.type"
      @close="hideToast"
    />
  </div>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import { useGroupStore } from '../stores/group'
import { useModalStore } from '../stores/modalStore'
import Button from './Shared/ButtonComponent.vue'
import InputField from './Shared/InputField.vue'
import EditGroupModal from './Group/EditGroupModal.vue'
import ToastNotification from './Shared/Toast.vue'

const groupStore = useGroupStore()
const modalStore = useModalStore()

const groups = computed(() => groupStore.groups)
const newGroup = ref('')
const groupErrorMessage = ref('')
const isEditModalOpen = ref(false)
const groupToEdit = ref(null)

const toast = reactive({
  show: false,
  message: '',
  type: 'success',
})

function showToast(message, type = 'success') {
  toast.message = message
  toast.type = type
  toast.show = true
  // Auto hide after 3 seconds
  setTimeout(() => {
    hideToast()
  }, 3000)
}

function hideToast() {
  toast.show = false
}

function clearGroupErrorMessage() {
  groupErrorMessage.value = ''
}

function validateGroup() {
  if (!newGroup.value) {
    groupErrorMessage.value = 'Group name cannot be empty.'
    return false
  }

  if (newGroup.value.length < 3) {
    groupErrorMessage.value = 'Group name must be at least 3 characters long.'
    return false
  }

  if (groupStore.groups.some((g) => g.name === newGroup.value)) {
    groupErrorMessage.value = 'Group already exists!'
    return false
  }

  return true
}

async function addGroup() {
  if (validateGroup()) {
    const result = await groupStore.addGroup(newGroup.value)
    if (result.success) {
      newGroup.value = ''
      groupErrorMessage.value = ''
      showToast('Group added successfully')
    } else {
      groupErrorMessage.value = result.error || 'Failed to add group.'
      showToast(result.error || 'Failed to add group', 'error')
    }
  }
}

function openEditModal(group) {
  groupToEdit.value = { ...group }
  isEditModalOpen.value = true
}

function closeEditModal() {
  isEditModalOpen.value = false
  groupToEdit.value = null
}

function handleGroupUpdate(updatedGroup) {
  showToast('Group updated successfully')
}

function showDeleteConfirmation(type, group) {
  modalStore.showDeleteConfirmation({
    type: 'group',
    group,
    message: `Are you sure you want to delete the group "${group}"? All related expenses will also be deleted.`,
    onConfirm: async () => {
      const result = await groupStore.deleteGroup(group)
      if (result.success) {
        showToast('Group deleted successfully')
      } else {
        showToast(result.error || 'Failed to delete group', 'error')
      }
    },
  })
}

function editGroup(group) {
  openEditModal(group)
}
</script>

<style scoped>
.section {
  margin-bottom: 2.5rem;
}

.section-title {
  font-size: 1.25rem;
  font-weight: 500;
  color: var(--text-primary);
  text-align: center;
  margin-bottom: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.groups-list {
  margin-top: 1.5rem;
  max-height: 200px;
  overflow-y: auto;
}

.groups-list ul {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.group-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem;
  background-color: var(--hover-color);
  border-radius: 0.5rem;
  transition: all 0.2s ease;
}

.group-item:hover {
  background-color: var(--border-color);
}

.group-name {
  font-weight: 500;
  color: var(--text-primary);
}

.delete-btn {
  padding: 0.5rem !important;
  color: var(--error-color);
  background-color: transparent;
  border-radius: 0.375rem;
  transition: all 0.2s ease;
}

.delete-btn:hover {
  background-color: rgba(239, 68, 68, 0.1);
}
</style>

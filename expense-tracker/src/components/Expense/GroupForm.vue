<!-- components/Expense/GroupForm.vue -->
<template>
  <form @submit.prevent="handleSubmit" class="mb-6">
    <h2 class="text-xl text-center mb-4">Group Management</h2>

    <div class="mb-4">
      <label for="group-name" class="block text-sm font-medium mb-2">Group Name</label>
      <input
        type="text"
        id="group-name"
        v-model="groupName"
        class="w-full p-2 border border-gray-300 rounded"
        placeholder="Group Name">
      <p v-if="error" class="text-red-500 text-sm mt-1">{{ error }}</p>
    </div>

    <div class="flex space-x-2">
      <Button
        variant="primary"
        type="submit"
        class="flex-1">
        {{ editMode ? 'Update Group' : 'Add Group' }}
      </Button>

      <Button
        v-if="editMode"
        @click="cancelEdit"
        class="flex-1">
        Cancel
      </Button>
    </div>
  </form>

  <div class="overflow-y-auto h-48 mt-6 border border-gray-200 rounded">
    <ul class="space-y-2 p-2">
      <li
        v-for="group in groupStore.groups"
        :key="group"
        class="flex justify-between items-center p-2 bg-gray-100 rounded">
        {{ group }}
        <div class="flex space-x-2">
          <button
            class="text-blue-600"
            @click="startEdit(group)"
            title="Edit Group">
            ✏️
          </button>
          <button
            class="text-red-600"
            @click="confirmDelete(group)"
            title="Delete Group">
            🗑️
          </button>
        </div>
      </li>
    </ul>
  </div>

  <Modal
    :is-open="showDeleteModal"
    title="Confirm Deletion"
    confirm-text="Delete"
    @cancel="cancelDelete"
    @confirm="deleteGroup">
    Are you sure you want to delete the group "{{ groupToDelete }}"? All related expenses will also be deleted.
  </Modal>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useGroupStore } from '../../stores/group.js'
import Button from '../Shared/ButtonComponent.vue'
import Modal from '../Shared/ModalComponent.vue'

const groupStore = useGroupStore()
const groupName = ref('')
const error = ref('')
const showDeleteModal = ref(false)
const groupToDelete = ref('')
const editMode = ref(false)
const originalGroupName = ref('')

const handleSubmit = () => {
  if (groupName.value.trim().length < 3) {
    error.value = 'Group name must be at least 3 characters long'
    return
  }

  let result

  if (editMode.value) {
    result = groupStore.updateGroup(originalGroupName.value, groupName.value.trim())
  } else {
    result = groupStore.addGroup(groupName.value.trim())
  }

  if (result.success) {
    groupName.value = ''
    error.value = ''
    editMode.value = false
    originalGroupName.value = ''
  } else {
    error.value = result.error
  }
}

const startEdit = (group) => {
  groupName.value = group
  originalGroupName.value = group
  editMode.value = true
}

const cancelEdit = () => {
  groupName.value = ''
  originalGroupName.value = ''
  editMode.value = false
  error.value = ''
}

const confirmDelete = (group) => {
  groupToDelete.value = group
  showDeleteModal.value = true
}

const cancelDelete = () => {
  showDeleteModal.value = false
  groupToDelete.value = ''
}

const deleteGroup = () => {
  groupStore.deleteGroup(groupToDelete.value)
  showDeleteModal.value = false
  groupToDelete.value = ''
}

onMounted(() => {
  groupStore.loadGroups()
})
</script>

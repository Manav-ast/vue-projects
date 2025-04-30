<!-- components/Expense/GroupForm.vue -->
<template>
  <form @submit.prevent="handleSubmit" class="mb-6">
    <h2 class="text-xl text-center mb-4">Group Management</h2>

    <InputField
      id="group-name"
      label="Group Name"
      v-model="groupName"
      placeholder="Group Name"
      :error="error"
    />

    <div class="flex space-x-2">
      <Button variant="primary" type="submit" class="flex-1">
        {{ editMode ? 'Update Group' : 'Add Group' }}
      </Button>

      <Button v-if="editMode" @click="cancelEdit" class="flex-1"> Cancel </Button>
    </div>
  </form>

  <GroupList
    :groups="groupStore.groups.map((g) => g.name)"
    @edit="startEdit"
    @delete="confirmDelete"
  />

  <Modal
    :is-open="showDeleteModal"
    title="Confirm Deletion"
    confirm-text="Delete"
    @cancel="cancelDelete"
    @confirm="deleteGroup"
  >
    Are you sure you want to delete the group "{{ groupToDelete }}"? All related expenses will also
    be deleted.
  </Modal>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useGroupStore } from '../../stores/group.js'
import Button from '../Shared/ButtonComponent.vue'
import Modal from '../Shared/ModalComponent.vue'
import InputField from '../Shared/InputField.vue'
import GroupList from '../Shared/GroupList.vue'

const emit = defineEmits(['show-toast'])

const groupStore = useGroupStore()
const groupName = ref('')
const error = ref('')
const showDeleteModal = ref(false)
const groupToDelete = ref('')
const editMode = ref(false)
const originalGroupName = ref('')

const handleSubmit = async () => {
  if (groupName.value.trim().length < 3) {
    error.value = 'Group name must be at least 3 characters long'
    return
  }

  let result

  if (editMode.value) {
    result = await groupStore.updateGroup(originalGroupName.value, groupName.value.trim())
  } else {
    result = await groupStore.addGroup(groupName.value.trim())
  }

  if (result.success) {
    groupName.value = ''
    error.value = ''
    editMode.value = false
    originalGroupName.value = ''
    emit('show-toast', editMode.value ? 'Group updated successfully' : 'Group added successfully')
  } else {
    error.value = result.error
    emit('show-toast', result.error, 'error')
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

const deleteGroup = async () => {
  const result = await groupStore.deleteGroup(groupToDelete.value)
  if (result.success) {
    emit('show-toast', 'Group deleted successfully')
  } else {
    error.value = result.error
    emit('show-toast', result.error, 'error')
  }
  showDeleteModal.value = false
  groupToDelete.value = ''
}

onMounted(() => {
  groupStore.loadGroups()
})
</script>

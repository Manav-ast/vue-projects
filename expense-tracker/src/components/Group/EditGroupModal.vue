<!-- components/Group/EditGroupModal.vue -->
<template>
  <Modal
    :is-open="isOpen"
    title="Edit Group"
    confirm-text="Update"
    confirm-variant="secondary"
    @cancel="closeModal"
    @confirm="handleUpdate"
  >
    <form @submit.prevent="handleUpdate" class="space-y-4">
      <InputField
        id="edit-group-name"
        label="Group Name"
        v-model="form.name"
        placeholder="Group Name"
        :error="errors.name"
        @focus="clearError"
      />
    </form>
  </Modal>

  <ToastNotification
    :show="toast.show"
    :message="toast.message"
    :type="toast.type"
    @close="hideToast"
  />
</template>

<script setup>
import { reactive, watch } from 'vue'
import { useGroupStore } from '../../stores/group.js'
import Modal from '../Shared/ModalComponent.vue'
import InputField from '../Shared/InputField.vue'
import ToastNotification from '../Shared/Toast.vue'
import { validateRequired } from '../../utils/validation.js'

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
  group: {
    type: Object,
    default: () => ({ id: null, name: '' }),
  },
})

const emit = defineEmits(['close', 'update'])

const groupStore = useGroupStore()

const form = reactive({
  id: null,
  name: '',
})

const errors = reactive({
  name: '',
})

const toast = reactive({
  show: false,
  message: '',
  type: 'success',
})

function showToast(message, type = 'success') {
  toast.message = message
  toast.type = type
  toast.show = true
  setTimeout(() => {
    hideToast()
  }, 3000)
}

function hideToast() {
  toast.show = false
}

const clearError = () => {
  errors.name = ''
}

const validateForm = () => {
  errors.name = validateRequired(form.name) ? '' : 'Group name is required.'
  if (!errors.name && form.name.length < 3) {
    errors.name = 'Group name must be at least 3 characters long.'
  }
  // Check for duplicates (excluding the current group being edited)
  if (!errors.name && groupStore.groups.some((g) => g.name === form.name && g.id !== form.id)) {
    errors.name = 'A group with this name already exists.'
  }
  return !errors.name
}

const handleUpdate = async () => {
  if (validateForm()) {
    const result = await groupStore.updateGroup(props.group.name, form.name)
    if (result.success) {
      emit('update', { ...form })
      closeModal()
      showToast('Group updated successfully')
    } else {
      errors.name = result.error || 'Failed to update group.'
      showToast(result.error || 'Failed to update group', 'error')
    }
  }
}

const closeModal = () => {
  emit('close')
}

// Watch for changes in the group prop to populate the form
watch(
  () => props.group,
  (newGroup) => {
    if (newGroup && newGroup.id) {
      form.id = newGroup.id
      form.name = newGroup.name
      clearError() // Clear errors when modal opens with new data
    } else {
      // Reset form if group is null or invalid
      form.id = null
      form.name = ''
    }
  },
  { immediate: true, deep: true },
)
</script>

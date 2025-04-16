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
      <Button type="submit" variant="primary" class="btn btn-primary w-full">
        Add Group
      </Button>
    </form>

    <div class="groups-list">
      <ul>
        <li
          v-for="group in groups"
          :key="group"
          class="group-item"
        >
          <span class="group-name">{{ group }}</span>
          <Button variant="danger" @click="showDeleteConfirmation('group', group)" class="delete-btn">
            <i class="fas fa-trash"></i>
          </Button>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useGroupStore } from '../stores/group'
import { useModalStore } from '../stores/modalStore'
import Button from './Shared/ButtonComponent.vue'
import InputField from './Shared/InputField.vue'

const groupStore = useGroupStore()
const modalStore = useModalStore()

const groups = computed(() => groupStore.groups)
const newGroup = ref('')
const groupErrorMessage = ref('')

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

  if (groupStore.groups.includes(newGroup.value)) {
    groupErrorMessage.value = 'Group already exists!'
    return false
  }

  return true
}

function addGroup() {
  if (validateGroup()) {
    groupStore.addGroup(newGroup.value)
    newGroup.value = ''
  }
}

function showDeleteConfirmation(type, group) {
  modalStore.showDeleteConfirmation({
    type: 'group',
    group,
    message: `Are you sure you want to delete the group "${group}"? All related expenses will also be deleted.`,
  })
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

<template>
  <div class="side-panel">
    <h1 class="app-title">Expense Tracker</h1>
    <GroupManagement />
    <ExpenseManagement />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useGroupStore } from '../stores/group'
import GroupManagement from './GroupManagement.vue'
import ExpenseManagement from './ExpenseManagement.vue'

const groupStore = useGroupStore()

onMounted(() => {
  groupStore.loadGroups()
})
</script>

<style scoped>
.side-panel {
  width: 100%;
  max-width: 400px;
  height: 100vh;
  padding: 2rem;
  background-color: var(--surface-color);
  border-right: 1px solid var(--border-color);
  overflow-y: auto;
}

.app-title {
  font-size: 1.75rem;
  font-weight: 600;
  color: var(--primary-color);
  text-align: center;
  margin-bottom: 2rem;
}

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

.form-field {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: 0.5rem;
  background-color: var(--surface-color);
  color: var(--text-primary);
  transition: all 0.2s ease;
}

.input:focus {
  outline: none;
  border-color: var(--primary-light);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.error-message {
  font-size: 0.875rem;
  color: var(--error-color);
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

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background-color: var(--border-color);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background-color: var(--text-secondary);
}
</style>

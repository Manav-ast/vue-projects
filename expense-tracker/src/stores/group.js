// store/group.js
import { defineStore } from 'pinia'
import { useExpenseStore } from './expense.js'
import api from '../config/api.js'

export const useGroupStore = defineStore('group', {
  state: () => ({
    groups: [] // Will store {id, name} objects
  }),

  actions: {
    async loadGroups() {
      try {
        const response = await api.get('/groups')
        if (response && response.data) {
          this.groups = response.data.groups;
        }
      } catch (error) {
        console.log('Error loading groups:', error);
      }
    },

    async addGroup(groupName) {
      try {
        if (!this.groups.some(g => g.name === groupName)) {
          const response = await api.post('/groups/store', { name: groupName })
          if (response && response.data) {
            this.groups.push(response.data.group)
            return { success: true }
          }
        }

        return { success: false, error: 'Group already exists!' }
      } catch (error) {
        console.log('Error adding group:', error);
        return { success: false, error: error.response?.data?.message || 'Failed to add group' }
      }
    },

    async updateGroup(oldGroupName, newGroupName) {
      try {
        if (oldGroupName === newGroupName) {
          return { success: true }
        }

        if (!this.groups.some(g => g.name === newGroupName)) {
          const group = this.groups.find(g => g.name === oldGroupName)
          if (group) {
            const response = await api.patch(`/groups/update/${group.id}`, { name: newGroupName })

            if (response && response.data) {
              group.name = newGroupName

              // Update the group name in all related expenses
              const expenseStore = useExpenseStore()
              expenseStore.updateExpenseGroup(oldGroupName, newGroupName)

              return { success: true }
            }
          }
        }
        return { success: false, error: 'Group name already exists or original group not found' }
      } catch (error) {
        console.log('Error updating group:', error);
        return { success: false, error: error.response?.data?.message || 'Failed to update group' }
      }
    },

    async deleteGroup(groupName) {
      try {
        const group = this.groups.find(g => g.name === groupName)
        if (!group) {
          return { success: false, error: 'Group not found' }
        }

        const response = await api.delete(`/groups/delete/${group.id}`)

        if (response) {
          this.groups = this.groups.filter(g => g.id !== group.id)

          // Update the expense store to remove expenses from the deleted group
          const expenseStore = useExpenseStore()
          expenseStore.expenses = expenseStore.expenses.filter(expense => expense.group !== groupName)

          return { success: true }
        }
        return { success: false, error: 'Failed to delete group' }
      } catch (error) {
        console.log('Error deleting group:', error)
        return { success: false, error: error.response?.data?.message || 'Failed to delete group' }
      }
    }
  }
})

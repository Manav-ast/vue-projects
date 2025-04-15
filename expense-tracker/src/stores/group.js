// store/group.js
import { defineStore } from 'pinia'
import { useExpenseStore } from './expense.js'

export const useGroupStore = defineStore('group', {
  state: () => ({
    groups: []
  }),

  actions: {
    loadGroups() {
      const savedGroups = localStorage.getItem('groups')
      if (savedGroups) {
        this.groups = JSON.parse(savedGroups)
      }
    },

    addGroup(groupName) {
      if (!this.groups.includes(groupName)) {
        this.groups.push(groupName)
        localStorage.setItem('groups', JSON.stringify(this.groups))
        return { success: true }
      }
      return { success: false, error: 'Group already exists!' }
    },

    updateGroup(oldGroupName, newGroupName) {
      if (oldGroupName === newGroupName) {
        return { success: true }
      }

      if (!this.groups.includes(newGroupName)) {
        const index = this.groups.indexOf(oldGroupName)
        if (index !== -1) {
          this.groups[index] = newGroupName
          localStorage.setItem('groups', JSON.stringify(this.groups))

          // Update the group name in all related expenses
          const expenseStore = useExpenseStore()
          expenseStore.updateExpenseGroup(oldGroupName, newGroupName)

          return { success: true }
        }
      }
      return { success: false, error: 'Group name already exists or original group not found' }
    },

    deleteGroup(groupName) {
      this.groups = this.groups.filter(group => group !== groupName)
      localStorage.setItem('groups', JSON.stringify(this.groups))

      // Also delete related expenses
      const expenseStore = useExpenseStore()
      expenseStore.deleteGroupExpenses(groupName)
    }
  }
})

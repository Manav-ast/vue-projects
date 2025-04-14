// store/group.js
import { defineStore } from 'pinia'
import { useExpenseStore } from './expense'

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

    deleteGroup(groupName) {
      this.groups = this.groups.filter(group => group !== groupName)
      localStorage.setItem('groups', JSON.stringify(this.groups))

      // Also delete related expenses
      const expenseStore = useExpenseStore()
      expenseStore.deleteGroupExpenses(groupName)
    }
  }
})

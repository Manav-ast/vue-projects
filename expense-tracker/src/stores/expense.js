// store/expense.js
import { defineStore } from 'pinia' 

export const useExpenseStore = defineStore('expense', {
  state: () => ({
    expenses: [],
    selectedMonth: new Date().toISOString().slice(0, 7),
    searchQuery: ''
  }),

  getters: {
    filteredExpenses: (state) => {
      return state.expenses.filter(expense => {
        const matchesMonth = state.selectedMonth ? expense.date.startsWith(state.selectedMonth) : true
        const searchLower = state.searchQuery.toLowerCase()
        const matchesSearch = expense.group.toLowerCase().includes(searchLower) ||
          expense.name.toLowerCase().includes(searchLower)

        return matchesMonth && matchesSearch
      })
    },

    groupedExpenses: (state) => {
      const grouped = {}
      state.filteredExpenses.forEach(expense => {
        if (!grouped[expense.group]) {
          grouped[expense.group] = []
        }
        grouped[expense.group].push(expense)
      })
      return grouped
    },

    lifetimeTotal: (state) => {
      return state.expenses.reduce((total, expense) => total + expense.amount, 0)
    },

    monthlyTotal: (state) => {
      return state.filteredExpenses.reduce((total, expense) => total + expense.amount, 0)
    },

    highestMonthlyExpense: (state) => {
      if (state.filteredExpenses.length === 0) return 0
      return Math.max(...state.filteredExpenses.map(expense => expense.amount))
    }
  },

  actions: {
    loadExpenses() {
      const savedExpenses = localStorage.getItem('expenses')
      if (savedExpenses) {
        this.expenses = JSON.parse(savedExpenses)
      }
    },

    addExpense(expense) {
      const isDuplicate = this.expenses.some(
        e => e.group === expense.group &&
          e.name === expense.name &&
          e.amount === expense.amount &&
          e.date === expense.date
      )

      if (!isDuplicate) {
        this.expenses.push(expense)
        localStorage.setItem('expenses', JSON.stringify(this.expenses))
        return { success: true }
      }

      return { success: false, error: 'This expense is already added.' }
    },

    deleteExpense(expense) {
      this.expenses = this.expenses.filter(
        e => !(e.group === expense.group &&
          e.name === expense.name &&
          e.amount === expense.amount &&
          e.date === expense.date)
      )
      localStorage.setItem('expenses', JSON.stringify(this.expenses))
    },

    deleteGroupExpenses(groupName) {
      this.expenses = this.expenses.filter(expense => expense.group !== groupName)
      localStorage.setItem('expenses', JSON.stringify(this.expenses))
    },

    setSelectedMonth(month) {
      this.selectedMonth = month
    },

    setSearchQuery(query) {
      this.searchQuery = query
    },

    sortExpenses(criteria) {
      if (criteria === 'name') {
        this.expenses.sort((a, b) => a.name.localeCompare(b.name))
      } else if (criteria === 'amount') {
        this.expenses.sort((a, b) => a.amount - b.amount)
      }
      localStorage.setItem('expenses', JSON.stringify(this.expenses))
    }
  }
})

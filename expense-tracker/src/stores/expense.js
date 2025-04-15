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

    updateExpense(oldExpense, newExpense) {
      console.log('Updating expense:', { oldExpense, newExpense });

      // Find the index of the expense to update
      const index = this.expenses.findIndex(
        e => e.group === oldExpense.group &&
          e.name === oldExpense.name &&
          e.amount === oldExpense.amount &&
          e.date === oldExpense.date
      );

      console.log('Found expense at index:', index);

      if (index === -1) {
        console.error('Expense not found for update:', oldExpense);
        return { success: false, error: 'Original expense not found.' };
      }

      // Check if the updated expense would be a duplicate (excluding the current expense)
      const wouldBeDuplicate = this.expenses.some(
        (e, i) => i !== index &&
          e.group === newExpense.group &&
          e.name === newExpense.name &&
          e.amount === newExpense.amount &&
          e.date === newExpense.date
      );

      if (wouldBeDuplicate) {
        console.error('Update would create duplicate expense');
        return { success: false, error: 'This would create a duplicate expense.' };
      }

      try {
        // Create a new expense object to avoid reference issues
        const updatedExpense = {
          group: newExpense.group,
          name: newExpense.name,
          amount: parseFloat(newExpense.amount),
          date: newExpense.date
        };

        // Update the expense
        this.expenses[index] = updatedExpense;
        localStorage.setItem('expenses', JSON.stringify(this.expenses));

        console.log('Expense updated successfully:', updatedExpense);
        return { success: true };
      } catch (error) {
        console.error('Error updating expense:', error);
        return { success: false, error: 'Failed to update expense.' };
      }
    },

    updateExpenseGroup(oldGroupName, newGroupName) {
      // Update group name for all expenses in the old group
      this.expenses = this.expenses.map(expense => {
        if (expense.group === oldGroupName) {
          return { ...expense, group: newGroupName }
        }
        return expense
      })

      localStorage.setItem('expenses', JSON.stringify(this.expenses))
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

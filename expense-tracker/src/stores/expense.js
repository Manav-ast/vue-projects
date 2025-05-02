// store/expense.js
import { defineStore } from 'pinia'
import { fetchExpenses, createExpense, updateExpense as updateExpenseService, deleteExpense as deleteExpenseService } from '@/services/expenseService';
import { useGroupStore } from './group.js';

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
      console.log('[groupedExpenses] Starting grouping for filtered expenses:', state.filteredExpenses);
      const grouped = {}
      state.filteredExpenses.forEach(expense => {
        // Ensure the expense object has a 'group' property before trying to access it
        const groupKey = expense.group || 'Unknown Group'; // Use 'Unknown Group' if group is missing
        if (!grouped[groupKey]) {
          grouped[groupKey] = []
        }
        grouped[groupKey].push(expense)
      })
      console.log('[groupedExpenses] Resulting grouped object:', grouped);
      return grouped
    },

    groupTotals: (state) => {
      const totals = {};
      state.filteredExpenses.forEach(expense => {
        const groupKey = expense.group || 'Unknown Group';
        if (!totals[groupKey]) {
          totals[groupKey] = 0;
        }
        totals[groupKey] += parseFloat(expense.amount);
      });
      return totals;
    },

    groupTotalsForChart: (state) => {
      const totals = {};
      state.filteredExpenses.forEach(expense => {
        const groupKey = expense.group || 'Unknown Group';
        if (!totals[groupKey]) {
          totals[groupKey] = 0;
        }
        totals[groupKey] += parseFloat(expense.amount);
      });
      return {
        labels: Object.keys(totals),
        data: Object.values(totals),
      };
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
    // Add a helper method to ensure expenses have the group name
    _formatExpense(expense) {
      const groupStore = useGroupStore();
      console.log('[_formatExpense] Formatting expense:', expense);
      console.log('[_formatExpense] Current groups in groupStore:', groupStore.groups);
      const group = groupStore.groups.find(g => g.id === expense.group_id);
      console.log('[_formatExpense] Found group:', group);
      return {
        ...expense,
        amount: parseFloat(expense.amount), // Ensure amount is a number
        group: group ? group.name : 'Unknown Group', // Add the group name
        group_id: expense.group_id // Ensure group_id is included
      };
    },

    async loadExpenses() {
      try {
        console.log('Loading expenses...')
        const response = await fetchExpenses()
        if (response && response.data) {
          console.log('Received expenses:', response.data.expenses)
          // Use map to apply formatting and collect results
          const formattedExpenses = response.data.expenses.map(expense => {
            const formatted = this._formatExpense(expense)
            console.log('Formatted expense:', formatted)
            return formatted
          })
          this.expenses = formattedExpenses; // Assign the fully formatted array
          console.log('Final expenses state after load and format:', this.expenses)
        }
      } catch (error) {
        console.error('Error loading expenses:', error)
        // Fallback to local storage if API fails
        try {
          const savedExpenses = localStorage.getItem('expenses')
          if (savedExpenses) {
            this.expenses = JSON.parse(savedExpenses)
          }
        } catch (e) {
          console.error(e)
        }
      }
    },

    async addExpense(expense) {
      try {
        const isDuplicate = this.expenses.some(
          e => e.name === expense.name &&
            e.date === expense.date
        )

        if (!isDuplicate) {
          const response = await createExpense(expense)

          if (response && response.data) {
            // Add the group name to the returned expense before adding it to our store
            const formattedExpense = this._formatExpense(response.data.expense);
            this.expenses.push(formattedExpense);
            return { success: true }
          }
        }
        return { success: false, error: 'This expense is already added.' }
      } catch (error) {
        console.log('Error adding expense:', error)
        return { success: false, error: error.response?.data?.message || 'Failed to add expense' }
      }
    },

    async updateExpense(oldExpense, newExpensePayload) {
      // oldExpense contains the original expense object from the state (including id)
      // newExpensePayload contains the updated data from the form (including group_id)
      console.log('Updating expense:', { oldExpense, newExpensePayload })

      // Find the index of the expense to update using its unique ID
      const index = this.expenses.findIndex(e => e.id === oldExpense.id);

      console.log('Found expense at index:', index);

      if (index === -1) {
        console.error('Expense not found for update:', oldExpense)
        return { success: false, error: 'Original expense not found.' }
      }

      // Backend should handle duplicate validation. Removing frontend check.
      /*
      const wouldBeDuplicate = this.expenses.some(
        (e, i) => i !== index &&
          e.name === newExpensePayload.name &&
          e.amount === newExpensePayload.amount &&
          e.date === newExpensePayload.date &&
          e.group_id === newExpensePayload.group_id // Check group_id too if necessary
      );

      if (wouldBeDuplicate) {
        console.error('Update would create duplicate expense');
        return { success: false, error: 'This would create a duplicate expense.' };
      }
      */

      try {
        // Get the expense ID from the array
        const expenseId = oldExpense.id; // Use the ID from the original expense object

        // Update the expense via API, sending the payload from the form
        const response = await updateExpenseService(expenseId, newExpensePayload);

        if (response && response.data) {
          // Format the expense with group name before updating the local array
          const formattedExpense = this._formatExpense(response.data.expense);
          this.expenses[index] = formattedExpense;

          console.log('Expense updated successfully:', formattedExpense)
          return { success: true }
        }

        return { success: false, error: 'Failed to update expense on server' }
      } catch (error) {
        console.error('Error updating expense:', error)
        return { success: false, error: error.response?.data?.message || 'Failed to update expense' }
      }
    },

    updateExpenseGroup(oldGroupName, newGroupName) {
      // This action is called by the group store after a group name is successfully updated via API.
      // We only need to update the local state here.
      this.expenses = this.expenses.map(expense => {
        if (expense.group === oldGroupName) {
          // Find the corresponding group ID from the group store if needed, though group name might suffice
          // For simplicity, we just update the name locally as the backend handles the relation.
          return { ...expense, group: newGroupName };
        }
        return expense;
      });
      console.log(`Updated local expenses from group '${oldGroupName}' to '${newGroupName}'`);
    },

    async deleteExpense(expenseToDelete) {
      // expenseToDelete is the object from the list/state, including its id
      try {
        // Find the index using the expense ID
        const index = this.expenses.findIndex(e => e.id === expenseToDelete.id);

        if (index === -1) {
          console.error('Expense not found for deletion:', expenseToDelete);
          return { success: false, error: 'Expense not found locally' };
        }

        const expenseId = expenseToDelete.id; // Get ID from the passed expense object
        const response = await deleteExpenseService(expenseId);

        if (response) {
          this.expenses.splice(index, 1)
          return { success: true }
        }

        return { success: false, error: 'Failed to delete expense' }
      } catch (error) {
        console.log('Error deleting expense:', error)
        return { success: false, error: error.response?.data?.message || 'Failed to delete expense' }
      }
    },

    // Removed deleteGroupExpenses action.
    // Assuming backend handles cascading deletes when a group is deleted.
    // The group store's deleteGroup action should be the single point of truth for group deletion.

    setSelectedMonth(month) {
      this.selectedMonth = month
    },

    setSearchQuery(query) {
      this.searchQuery = query
    },

    async sortExpenses(criteria) {
      try {
        // Perform client-side sorting since we don't have a sort endpoint
        if (criteria === 'name') {
          this.expenses.sort((a, b) => a.name.localeCompare(b.name))
        } else if (criteria === 'amount') {
          this.expenses.sort((a, b) => parseFloat(b.amount) - parseFloat(a.amount))
        } else if (criteria === 'date') {
          this.expenses.sort((a, b) => new Date(b.date) - new Date(a.date))
        }
        return { success: true }
      } catch (error) {
        console.error('Error sorting expenses:', error)
        return { success: false, error: 'Failed to sort expenses' }
      }
    }
  }
})

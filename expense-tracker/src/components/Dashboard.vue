<!-- views/DashboardView.vue -->
<template>
  <div class="dashboard">
    <!-- Right Section -->
    <div class="main-content">
      <h2 class="dashboard-title">Dashboard Overview</h2>

      <Stats :stats="[
        { label: 'Lifetime Expenses', value: expenseStore.lifetimeTotal, prefix: '₹ ' },
        { label: 'Total this Month', value: expenseStore.monthlyTotal, prefix: '₹ ' },
        { label: 'Highest this Month', value: expenseStore.highestMonthlyExpense, prefix: '₹ ' }
      ]" />

      <ExpenseList
        @edit-expense="handleEditExpense"
        :show-header="false"
        :show-navigation="false"
        :use-form-edit="true"
        class="expense-list"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useExpenseStore } from '../stores/expense'
import ExpenseList from '../components/Expense/ExpenseList.vue'
import Stats from '../components/Shared/Stats.vue'

const expenseStore = useExpenseStore()
const expenseFormRef = ref(null)

const handleEditExpense = (expense) => {
  // Scroll the page to the expense form
  window.scrollTo({
    top: 0,
    behavior: 'smooth',
  })

  // After a short delay to allow scroll, focus the form and populate it
  setTimeout(() => {
    if (expenseFormRef.value && expenseFormRef.value.startEdit) {
      expenseFormRef.value.startEdit(expense)
    }
  }, 300) // Reduced delay to 300ms for better responsiveness
}

onMounted(() => {
  expenseStore.loadExpenses()
})
</script>

<style scoped>
.dashboard {
  flex: 1;
  min-height: 100vh;
  background-color: var(--background-color);
}

.main-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.dashboard-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 2rem;
  text-align: center;
}

.expense-list {
  background-color: var(--surface-color);
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

@media (max-width: 640px) {
  .main-content {
    padding: 1rem;
  }

  .dashboard-title {
    font-size: 1.25rem;
    margin-bottom: 1.5rem;
  }

  .expense-list {
    padding: 1rem;
    border-radius: 0.75rem;
    margin: 0 -0.5rem;
  }
}
</style>

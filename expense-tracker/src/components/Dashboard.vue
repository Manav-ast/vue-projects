<!-- views/DashboardView.vue -->
<template>
  <div class="dashboard">
    <!-- Right Section -->
    <div class="main-content">
      <h2 class="dashboard-title">Dashboard Overview</h2>

      <div class="stats-grid">
        <div class="stat-card">
          <p class="stat-label">Lifetime Expenses</p>
          <span class="stat-value">₹ {{ expenseStore.lifetimeTotal }}</span>
        </div>
        <div class="stat-card">
          <p class="stat-label">Total this Month</p>
          <span class="stat-value">₹ {{ expenseStore.monthlyTotal }}</span>
        </div>
        <div class="stat-card">
          <p class="stat-label">Highest this Month</p>
          <span class="stat-value">₹ {{ expenseStore.highestMonthlyExpense }}</span>
        </div>
      </div>

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

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2.5rem;
}

.stat-card {
  background-color: var(--surface-color);
  border-radius: 1rem;
  padding: 1.5rem;
  text-align: center;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: all 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.stat-label {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin-bottom: 0.5rem;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--primary-color);
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

  .stats-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .stat-card {
    padding: 1.25rem;
  }
}
</style>

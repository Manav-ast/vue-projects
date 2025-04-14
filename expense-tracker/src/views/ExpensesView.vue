<!-- views/Expenses.vue -->
<template>
  <div class="max-w-7xl mx-auto bg-white rounded-lg shadow-lg p-6">
    <h1 class="text-3xl font-semibold text-center mb-6">All Expenses</h1>

    <ExpenseList />
  </div>
</template>

<script setup>
import ExpenseList from '../components/Expense/ExpenseList.vue'
</script>

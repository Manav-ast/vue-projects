// router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '../views/DashboardView.vue'
import Expenses from '../views/ExpensesView.vue'

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard
  },
  {
    path: '/expenses',
    name: 'Expenses',
    component: Expenses
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router

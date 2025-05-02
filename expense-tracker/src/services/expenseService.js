import api from '@/config/api'

const fetchExpenses = () => {
  return api.get('/expenses')
}

const createExpense = (expenseData) => {
  return api.post('/expenses/store', expenseData)
}

const updateExpense = (expenseId, expenseData) => {
  return api.patch(`/expenses/update/${expenseId}`, expenseData)
}

const deleteExpense = (expenseId) => {
  return api.delete(`/expenses/delete/${expenseId}`)
}

const exportCsv = () => {
  return api.get('/expenses/export-csv')
}

const exportPdf = () => {
  return api.get('/expenses/export-pdf')
}


export {
  fetchExpenses,
  createExpense,
  updateExpense,
  deleteExpense,
  exportCsv,
  exportPdf
}

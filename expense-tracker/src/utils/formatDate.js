// utils/formatDate.js
export function formatDate(dateString) {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

export function getTodayDate() {
  return new Date().toISOString().split('T')[0]
}

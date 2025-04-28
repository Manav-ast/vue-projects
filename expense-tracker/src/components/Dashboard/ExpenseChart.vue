<template>
  <div class="expense-chart">
    <h3 class="text-lg font-semibold mb-4">Group Expenses Distribution</h3>
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useExpenseStore } from '../../stores/expense'
import Chart from 'chart.js/auto'

const expenseStore = useExpenseStore()
const chartCanvas = ref(null)
let chart = null

const createChart = () => {
  if (chart) {
    chart.destroy()
  }

  const { labels, data } = expenseStore.groupTotalsForChart

  chart = new Chart(chartCanvas.value, {
    type: 'doughnut',
    data: {
      labels: labels,
      datasets: [
        {
          data: data,
          backgroundColor: [
            '#FF6384',
            '#36A2EB',
            '#FFCE56',
            '#4BC0C0',
            '#9966FF',
            '#FF9F40',
            '#FF6384',
          ],
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom',
        },
        title: {
          display: false,
        },
      },
    },
  })
}

onMounted(() => {
  createChart()
})

// Watch for changes in expenses and recreate chart
watch(
  () => expenseStore.groupTotalsForChart,
  () => {
    createChart()
  },
  { deep: true },
)
</script>

<style scoped>
.expense-chart {
  background-color: var(--surface-color);
  border-radius: 0.5rem;
  padding: 1rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}
</style>

<template>
  <div class="stats-grid">
    <div v-for="(stat, index) in stats" :key="index" class="stat-card">
      <p class="stat-label">{{ stat.label }}</p>
      <span class="stat-value">{{ stat.prefix }}{{ stat.value }}</span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  stats: {
    type: Array,
    required: true,
    validator: (value) => {
      return value.every(stat =>
        typeof stat === 'object' &&
        'label' in stat &&
        'value' in stat &&
        'prefix' in stat
      )
    }
  }
})
</script>

<style scoped>
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

@media (max-width: 640px) {
  .stats-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .stat-card {
    padding: 1.25rem;
  }
}
</style>

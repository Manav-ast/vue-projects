<!-- components/Shared/TableComponent.vue -->
<template>
  <div class="overflow-x-auto">
    <table class="min-w-full bg-gray-100 border border-gray-300 rounded-lg overflow-hidden">
      <thead>
        <tr class="bg-blue-600 text-white rounded-t-lg">
          <th
            v-for="header in headers"
            :key="header.key"
            class="py-2 px-4"
            :class="header.align || 'text-left'"
          >
            {{ header.label }}
          </th>
        </tr>
      </thead>
      <tbody class="rounded-b-lg">
        <tr v-for="(row, index) in data" :key="getRowKey(row, index)">
          <td
            v-for="header in headers"
            :key="header.key"
            class="py-2 px-4"
            :class="header.align || 'text-left'"
          >
            <slot :name="header.key" :row="row">
              {{ row[header.key] }}
            </slot>
          </td>
        </tr>
      </tbody>
    </table>

    <div
      v-if="data.length === 0"
      class="text-center py-8 bg-gray-100 rounded"
    >
      <p class="text-gray-500">{{ emptyMessage }}</p>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
  headers: {
    type: Array,
    required: true,
    // Each header should have: { key: string, label: string, align?: string }
  },
  data: {
    type: Array,
    default: () => []
  },
  rowKey: {
    type: String,
    default: ''
  },
  emptyMessage: {
    type: String,
    default: 'No data available'
  }
})

const getRowKey = (row, index) => {
  if (props.rowKey && row[props.rowKey]) {
    return row[props.rowKey]
  }
  return index
}
</script>

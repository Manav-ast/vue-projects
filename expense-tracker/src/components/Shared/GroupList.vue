<!-- components/Shared/GroupList.vue -->
<template>
  <div class="overflow-y-auto h-48 mt-6 border border-gray-200 rounded">
    <ul class="space-y-2 p-2">
      <li
        v-for="group in groups"
        :key="group"
        class="flex justify-between items-center p-2 bg-gray-100 rounded">
        {{ group }}
        <div class="flex space-x-2">
          <button
            v-if="showEdit"
            class="text-blue-600"
            @click="$emit('edit', group)"
            title="Edit Group">
            <i class="fa-solid fa-pen"></i>
          </button>
          <button
            v-if="showDelete"
            class="text-red-600"
            @click="$emit('delete', group)"
            title="Delete Group">
            <font-awesome-icon icon="trash" />
          </button>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
defineProps({
  groups: {
    type: Array,
    required: true,
    default: () => []
  },
  showEdit: {
    type: Boolean,
    default: true
  },
  showDelete: {
    type: Boolean,
    default: true
  }
})

defineEmits(['edit', 'delete'])
</script>

<template>
  <div class="chat-view">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="bg-white rounded-lg shadow-lg overflow-hidden" style="height: calc(100vh - 8rem);">
        <ChatView />
      </div>
    </div>
  </div>
</template>

<script setup>
import ChatView from '../components/Chat/ChatView.vue'
</script>

<style scoped>
.chat-view {
  min-height: calc(100vh - 4rem);
  background-color: var(--background-color);
}
</style>
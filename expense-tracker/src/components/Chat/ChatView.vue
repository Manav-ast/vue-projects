<template>
  <div class="chat-container">
    <div class="chat-messages" ref="messagesContainer">
      <div v-for="(message, index) in messages" :key="index" class="message">
        <div class="message-content">
          {{ message.text }}
        </div>
      </div>
    </div>
    <form @submit.prevent="sendMessage" class="chat-input-form">
      <input
        v-model="newMessage"
        type="text"
        placeholder="Type your command..."
        class="chat-input"
      />
      <button type="submit" class="send-button">
        Send
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import api from '@/config/api'

const messages = ref([])
const newMessage = ref('')
const messagesContainer = ref(null)

const scrollToBottom = async () => {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const sendMessage = async () => {
  if (!newMessage.value.trim()) return

  const userMessage = newMessage.value
  messages.value.push({ text: userMessage })
  newMessage.value = ''

  try {
    const response = await api.post('/command', {
      command: userMessage
    })

    if (response.data.success) {
      const responseMessage = response.data.group
        ? `Created group: ${response.data.group.name}`
        : `Added expense: ${response.data.expense.name}`
      messages.value.push({ text: responseMessage })
    } else if (response.data.error) {
      messages.value.push({ text: `Error: ${response.data.error}` })
    }
  } catch (error) {
    messages.value.push({ text: 'Error: Could not process your command' })
  }

  await scrollToBottom()
}

onMounted(() => {
  messages.value.push({ text: 'Welcome! You can create groups or add expenses using natural language. Try saying something like "Create a group called Groceries" or "Add an expense of $50 for dinner in the Groceries group".' })
})
</script>

<style scoped>
.chat-container {
  display: flex;
  flex-direction: column;
  height: 100%;
  background-color: var(--surface-color);
  border-radius: 0.5rem;
  overflow: hidden;
}

.chat-messages {
  flex-grow: 1;
  overflow-y: auto;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.message {
  max-width: 80%;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  background-color: var(--hover-color);
  color: var(--text-primary);
}

.chat-input-form {
  display: flex;
  gap: 0.5rem;
  padding: 1rem;
  background-color: var(--surface-color);
  border-top: 1px solid var(--border-color);
}

.chat-input {
  flex-grow: 1;
  padding: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: 0.5rem;
  background-color: var(--surface-color);
  color: var(--text-primary);
  transition: all 0.2s ease;
}

.chat-input:focus {
  outline: none;
  border-color: var(--primary-light);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.send-button {
  padding: 0.75rem 1.5rem;
  background-color: var(--primary-color);
  color: white;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: all 0.2s ease;
}

.send-button:hover {
  background-color: var(--primary-dark);
}
</style>
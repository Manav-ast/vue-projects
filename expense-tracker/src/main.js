import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './assets/css/app.css'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faTrash, faPlus } from '@fortawesome/free-solid-svg-icons'
import api from './config/api'
import { useAuthStore } from './stores/auth'

// Add FontAwesome icons to the library
library.add(faTrash, faPlus)

const app = createApp(App)
const pinia = createPinia()

app.component('font-awesome-icon', FontAwesomeIcon)

// Make API instance available globally
app.config.globalProperties.$api = api

app.use(pinia)

// Initialize auth state before mounting the app
const authStore = useAuthStore()
authStore.initAuth()

app.use(router)
app.mount('#app')

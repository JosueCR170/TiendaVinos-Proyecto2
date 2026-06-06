import { createApp } from 'vue'

import App    from './App.vue'
import router from './router'
import { useCartStore } from './stores/cart'

import './assets/main.css'

const app = createApp(App)

app.use(router)

// Restaurar el carrito desde localStorage al iniciar la app
useCartStore().hydrate()

app.mount('#app')
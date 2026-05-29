<template>
  <div class="min-h-screen flex flex-col bg-background text-on-surface font-body">
    <!-- Navbar -->
    <nav class="flex justify-between items-center px-12 py-6 w-full sticky top-0 z-50 bg-[#d5d5b5]/90 backdrop-blur-3xl transition-all shadow-sm">
      <!-- Brand -->
      <div class="w-1/3 flex justify-start">
        <router-link to="/" class="font-['Noto_Serif'] text-2xl font-bold italic text-[#2a0002]">
          La Última Botella
        </router-link>
      </div>
      
      <!-- Nav Links (desktop) -->
      <div class="w-1/3 hidden md:flex justify-center space-x-8 font-['Noto_Serif'] text-lg tracking-tight">
        <router-link
          to="/"
          :class="isActive('/') ? 'text-[#2a0002] border-b-2 border-[#735c00] pb-1' : 'text-[#1b1d0e]/70 hover:text-[#2a0002] transition-colors duration-300'"
        >
          Inicio
        </router-link>
        <router-link
          to="/catalogo"
          :class="isActive('/catalogo') ? 'text-[#2a0002] border-b-2 border-[#735c00] pb-1' : 'text-[#1b1d0e]/70 hover:text-[#2a0002] transition-colors duration-300'"
        >
          Catálogo
        </router-link>
        <router-link
          to="/about"
          :class="isActive('/about') ? 'text-[#2a0002] border-b-2 border-[#735c00] pb-1' : 'text-[#1b1d0e]/70 hover:text-[#2a0002] transition-colors duration-300'"
        >
          Historia
        </router-link>
      </div>
      
      <!-- Right Icons -->
      <div class="w-1/3 flex justify-end items-center space-x-6">
        <router-link to="/carrito" class="relative group" title="Carrito">
          <span class="material-symbols-outlined text-[#2a0002] hover:opacity-80 active:scale-95 transition-all">shopping_cart</span>
          <span v-if="cartCount > 0" class="absolute -top-2 -right-2 bg-red-600 text-white text-[10px] w-4 h-4 flex items-center justify-center rounded-full font-bold shadow-sm group-hover:scale-110 transition-transform">
            {{ cartCount }}
          </span>
        </router-link>
        <router-link to="/admin" title="Panel de Administración" class="material-symbols-outlined text-[#2a0002] hover:opacity-80 active:scale-95 transition-all">
          settings
        </router-link>
        <button title="Mi cuenta" class="material-symbols-outlined text-[#2a0002] hover:opacity-80 active:scale-95 transition-all">
          person
        </button>
      </div>
    </nav>

    <!-- Notification Container -->
    <div id="notification-container" class="fixed bottom-10 right-10 z-[100] flex flex-col gap-4"></div>

    <!-- Main Content -->
    <main class="flex-grow">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="w-full py-20 px-12 bg-[#e4e4cc] border-t border-[#dac1bf]/30">
      <div class="max-w-4xl mx-auto flex flex-col items-center text-center gap-10">
        <!-- Brand -->
        <div class="space-y-4">
          <div class="font-['Noto_Serif'] text-3xl italic text-[#2a0002]">La Última Botella</div>
          <p class="font-body text-sm text-[#1b1d0e]/60 max-w-sm mx-auto">
            Curaduría exclusiva de vinos del viejo y nuevo mundo. Seleccionamos historias, descorchamos momentos.
          </p>
        </div>
        
        <!-- Social Media -->
        <div class="flex items-center space-x-8">
          <a href="#" class="text-[#2a0002]/70 hover:text-[#2a0002] transition-colors" title="Instagram">
            <i class="fa-brands fa-instagram text-xl"></i>
          </a>
          <a href="#" class="text-[#2a0002]/70 hover:text-[#2a0002] transition-colors" title="Facebook">
            <i class="fa-brands fa-facebook text-xl"></i>
          </a>
          <a href="#" class="text-[#2a0002]/70 hover:text-[#2a0002] transition-colors" title="Twitter">
            <i class="fa-brands fa-x-twitter text-xl"></i>
          </a>
        </div>

        <!-- Divider -->
        <div class="w-16 h-px bg-[#2a0002]/10"></div>

        <!-- Copyright -->
        <div class="font-['Manrope'] text-[10px] uppercase tracking-[0.2em] text-[#1b1d0e]/50">
          © {{ new Date().getFullYear() }} La Última Botella. Todos los derechos reservados.<br>
          <span class="mt-2 block opacity-70 italic text-stone-500">Diseñado y Desarrollado por Daniel y Josué</span>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const cartCount = ref(0)

const isActive = (path) => {
  if (path === '/') return route.path === '/'
  return route.path.startsWith(path)
}

const loadCartCount = () => {
  const cart = JSON.parse(localStorage.getItem('carrito') || '{}')
  cartCount.value = Object.keys(cart).length
}

onMounted(() => {
  loadCartCount()
  window.addEventListener('storage', loadCartCount)
})

const showNotification = (message, type = 'success') => {
  const container = document.getElementById('notification-container')
  const notification = document.createElement('div')
  notification.className = `p-4 rounded-lg shadow-2xl text-white font-label text-sm transform transition-all duration-500 translate-x-full ${type === 'success' ? 'bg-[#2a0002]' : 'bg-red-600'}`
  notification.innerHTML = `
    <div class="flex items-center gap-3">
      <span class="material-symbols-outlined text-sm">${type === 'success' ? 'check_circle' : 'error'}</span>
      <span>${message}</span>
    </div>
  `
  container.appendChild(notification)
  
  setTimeout(() => notification.classList.remove('translate-x-full'), 10)
  setTimeout(() => {
    notification.classList.add('opacity-0')
    setTimeout(() => notification.remove(), 500)
  }, 3000)
}

window.showNotification = showNotification
</script>

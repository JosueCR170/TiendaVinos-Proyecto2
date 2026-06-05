<template>
  <nav class="navbar">
    <!-- Marca -->
    <div class="navbar-brand">
      <RouterLink to="/" class="brand-link">La Última Botella</RouterLink>
    </div>

    <!-- Links centrales -->
    <div class="navbar-links">
      <RouterLink to="/"         class="nav-link" :class="{ active: $route.path === '/' }">Inicio</RouterLink>
      <RouterLink to="/catalogo" class="nav-link" :class="{ active: $route.path.startsWith('/catalogo') }">Catálogo</RouterLink>
      <RouterLink to="/about"    class="nav-link" :class="{ active: $route.path.startsWith('/about') }">Historia</RouterLink>
    </div>

    <!-- Íconos derecha -->
    <div class="navbar-actions">
      <!-- Carrito -->
      <RouterLink to="/carrito" class="cart-btn" title="Carrito">
        <span class="material-symbols-outlined">shopping_cart</span>
        <span v-if="cartCount > 0" class="cart-badge">{{ cartCount }}</span>
      </RouterLink>

      <!-- Admin -->
      <RouterLink to="/admin" class="icon-btn" title="Panel de Administración">
        <span class="material-symbols-outlined">settings</span>
      </RouterLink>


    </div>
  </nav>

  <!-- Notificaciones flotantes -->
  <div class="notification-container">
    <TransitionGroup name="notif">
      <div
        v-for="notif in notifications"
        :key="notif.id"
        class="notification"
        :class="notif.type"
      >
        <span class="material-symbols-outlined notif-icon">
          {{ notif.type === 'success' ? 'check_circle' : 'error' }}
        </span>
        <span>{{ notif.message }}</span>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useCartStore } from '@/stores/cart'
import { useNotificationStore } from '@/stores/notifications'

const cartStore         = useCartStore()
const notificationStore = useNotificationStore()

const cartCount     = computed(() => cartStore.count)
const notifications = computed(() => notificationStore.list)
</script>

<style scoped>
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 3rem;
  width: 100%;
  position: sticky;
  top: 0;
  z-index: 50;
  background-color: rgba(213, 213, 181, 0.90);
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
  box-shadow: 0 1px 3px rgba(0,0,0,0.06);
  transition: all 0.3s;
}

/* Marca */
.navbar-brand { width: 33%; display: flex; justify-content: flex-start; }

.brand-link {
  font-family: 'Noto Serif', serif;
  font-size: 1.5rem;
  font-weight: 700;
  font-style: italic;
  color: #2a0002;
  text-decoration: none;
  transition: opacity 0.2s;
}

.brand-link:hover { opacity: 0.8; }

/* Links */
.navbar-links {
  width: 33%;
  display: flex;
  justify-content: center;
  gap: 2rem;
  font-family: 'Noto Serif', serif;
  font-size: 1.125rem;
}

.nav-link {
  color: rgba(27, 29, 14, 0.7);
  text-decoration: none;
  padding-bottom: 4px;
  border-bottom: 2px solid transparent;
  transition: color 0.3s, border-color 0.3s;
}

.nav-link:hover,
.nav-link.active {
  color: #2a0002;
}

.nav-link.active {
  border-bottom-color: #735c00;
}

/* Acciones */
.navbar-actions {
  width: 33%;
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 1.5rem;
}

.cart-btn {
  position: relative;
  display: flex;
  align-items: center;
  color: #2a0002;
  text-decoration: none;
  transition: opacity 0.2s;
}

.cart-btn:hover { opacity: 0.8; }

.cart-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background-color: #dc2626;
  color: white;
  font-size: 10px;
  width: 16px;
  height: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 9999px;
  font-weight: 700;
  box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.icon-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  color: #2a0002;
  display: flex;
  align-items: center;
  transition: opacity 0.2s, transform 0.15s;
  text-decoration: none;
}

.icon-btn:hover  { opacity: 0.8; }
.icon-btn:active { transform: scale(0.95); }

/* Notificaciones */
.notification-container {
  position: fixed;
  bottom: 2.5rem;
  right: 2.5rem;
  z-index: 100;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  pointer-events: none;
}

.notification {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.25rem;
  border-radius: 0.5rem;
  box-shadow: 0 20px 40px rgba(0,0,0,0.15);
  color: white;
  font-size: 0.875rem;
  font-family: 'Manrope', sans-serif;
  pointer-events: all;
}

.notification.success { background-color: #2a0002; }
.notification.error   { background-color: #dc2626; }

.notif-icon { font-size: 18px !important; }

/* Animación de notificaciones */
.notif-enter-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.notif-leave-active { transition: all 0.3s ease; }
.notif-enter-from   { transform: translateX(100%); opacity: 0; }
.notif-leave-to     { transform: translateX(100%); opacity: 0; }
</style>
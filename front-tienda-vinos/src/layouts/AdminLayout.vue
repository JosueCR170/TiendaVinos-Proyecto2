<template>
  <div class="admin-wrapper">
    <!-- ── Sidebar ────────────────────────────────────────────────────────── -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <span class="brand-name">La Última Botella</span>
        <div class="admin-profile">
          <div class="profile-avatar">
            <span class="material-symbols-outlined">person</span>
          </div>
          <div class="profile-info">
            <span class="profile-name">Admin</span>
            <span class="profile-role">Panel de Control</span>
          </div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <RouterLink to="/admin" class="nav-item" :class="{ active: $route.path === '/admin' }">
          <span class="material-symbols-outlined">dashboard</span>
          <span>Dashboard</span>
        </RouterLink>

        <RouterLink to="/admin/productos" class="nav-item" :class="{ active: $route.path.startsWith('/admin/productos') }">
          <span class="material-symbols-outlined">wine_bar</span>
          <span>Productos</span>
        </RouterLink>

        <RouterLink to="/admin/categorias" class="nav-item" :class="{ active: $route.path.startsWith('/admin/categorias') }">
          <span class="material-symbols-outlined">category</span>
          <span>Categorías</span>
        </RouterLink>

        <RouterLink to="/admin/marcas" class="nav-item" :class="{ active: $route.path.startsWith('/admin/marcas') }">
          <span class="material-symbols-outlined">storefront</span>
          <span>Marcas</span>
        </RouterLink>

        <RouterLink to="/admin/variedades" class="nav-item" :class="{ active: $route.path.startsWith('/admin/variedades') }">
          <span class="material-symbols-outlined">local_florist</span>
          <span>Variedades</span>
        </RouterLink>
      </nav>

      <div class="sidebar-footer">
        <RouterLink to="/" class="btn-store-link">
          Volver a la Tienda
        </RouterLink>
      </div>
    </aside>

    <!-- ── Contenido principal ────────────────────────────────────────────── -->
    <main class="main-content">
      <div class="content-container">
        <RouterView />
      </div>
    </main>

    <!-- ── Notificaciones ─────────────────────────────────────────────────── -->
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
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useNotificationStore } from '@/stores/notifications'

const notificationStore = useNotificationStore()
const notifications = computed(() => notificationStore.list)
</script>

<style scoped>
/* Notificaciones */
.notification-container {
  position: fixed;
  bottom: 2.5rem;
  right: 2.5rem;
  z-index: 9999;
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
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
  color: white;
  font-size: 0.875rem;
  font-weight: 500;
  pointer-events: all;
}

.notification.success { background-color: #2a0002; }
.notification.error   { background-color: #dc2626; }

.notif-icon { font-size: 18px !important; }

.notif-enter-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.notif-leave-active { transition: all 0.3s ease; }
.notif-enter-from   { transform: translateX(100%); opacity: 0; }
.notif-leave-to     { transform: translateX(100%); opacity: 0; }
</style>

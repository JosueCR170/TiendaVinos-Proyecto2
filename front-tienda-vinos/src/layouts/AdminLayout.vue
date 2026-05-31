<template>
  <div class="admin-layout">
    <!-- ── Sidebar ────────────────────────────────────────────────────────── -->
    <aside class="admin-sidebar">
      <div class="sidebar-header">
        <RouterLink to="/admin" class="admin-brand">Panel Admin</RouterLink>
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
        <RouterLink to="/" class="back-link">
          <span class="material-symbols-outlined">arrow_back</span>
          <span>Volver a la Tienda</span>
        </RouterLink>
      </div>
    </aside>

    <!-- ── Contenido principal ────────────────────────────────────────────── -->
    <main class="admin-main">
      <header class="admin-topbar">
        <div class="topbar-left">
          <span class="material-symbols-outlined menu-toggle">menu</span>
        </div>
        <div class="topbar-right">
          <div class="user-profile">
            <span class="material-symbols-outlined">account_circle</span>
            <span>Administrador</span>
          </div>
        </div>
      </header>

      <div class="admin-content">
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
.admin-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f5f5f5;
  font-family: 'Manrope', sans-serif;
}

/* Sidebar */
.admin-sidebar {
  width: 260px;
  background-color: #2a0002;
  color: #e4e4cc;
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
}

.sidebar-header {
  padding: 1.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.admin-brand {
  font-family: 'Noto Serif', serif;
  font-size: 1.25rem;
  font-style: italic;
  font-weight: 700;
  color: #fff;
  text-decoration: none;
}

.sidebar-nav {
  padding: 1rem 0;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1.5rem;
  color: rgba(228, 228, 204, 0.7);
  text-decoration: none;
  transition: all 0.2s;
}

.nav-item:hover {
  background-color: rgba(255, 255, 255, 0.05);
  color: #fff;
}

.nav-item.active {
  background-color: #735c00;
  color: #fff;
  border-right: 4px solid #fbfbe2;
}

.nav-item .material-symbols-outlined {
  font-size: 1.25rem;
}

.sidebar-footer {
  padding: 1.5rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.back-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: rgba(228, 228, 204, 0.7);
  text-decoration: none;
  font-size: 0.875rem;
  transition: color 0.2s;
}

.back-link:hover {
  color: #fff;
}

/* Main Content */
.admin-main {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.admin-topbar {
  height: 64px;
  background-color: #fff;
  border-bottom: 1px solid #e5e5e5;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 2rem;
  flex-shrink: 0;
}

.menu-toggle {
  cursor: pointer;
  color: #666;
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #333;
  font-weight: 500;
  font-size: 0.875rem;
}

.admin-content {
  padding: 2rem;
  overflow-y: auto;
  flex-grow: 1;
}

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

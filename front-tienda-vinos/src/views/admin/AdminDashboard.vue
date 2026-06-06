<template>
  <div class="index-view">
    <header class="index-header mb-8">
      <div class="header-info">
        <h1>Dashboard General</h1>
        <p>Visión general del inventario, bodegas y estado de la cava.</p>
      </div>
    </header>

    <div v-if="loading" class="p-8 flex justify-center">
      <span class="material-symbols-outlined animate-spin text-4xl text-[#735c00]">progress_activity</span>
    </div>

    <div v-else-if="error" class="alert-premium error">
      <span class="material-symbols-outlined alert-icon">error</span>
      <div class="alert-content">
        <span class="alert-title">Error</span>
        <p class="alert-message">{{ error }}</p>
      </div>
      <button @click="fetchStats" class="btn-filter" style="margin-left: auto;">Reintentar</button>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      
      <!-- Total Productos -->
      <div class="stat-card">
        <div class="stat-icon bg-wine">
          <span class="material-symbols-outlined text-white">wine_bar</span>
        </div>
        <div class="stat-content">
          <p class="stat-label">Total Productos</p>
          <p class="stat-value">{{ stats.total_productos }}</p>
        </div>
      </div>

      <!-- Productos Activos -->
      <div class="stat-card">
        <div class="stat-icon bg-[#efefd7] text-primary">
          <span class="material-symbols-outlined">check_circle</span>
        </div>
        <div class="stat-content">
          <p class="stat-label">Productos Activos</p>
          <p class="stat-value">{{ stats.productos_activos }}</p>
        </div>
      </div>

      <!-- Sin Stock -->
      <div class="stat-card">
        <div class="stat-icon bg-[#ffdad6] text-[#ba1a1a]">
          <span class="material-symbols-outlined">warning</span>
        </div>
        <div class="stat-content">
          <p class="stat-label">Sin Stock (Agotados)</p>
          <p class="stat-value text-red-600">{{ stats.productos_sin_stock }}</p>
        </div>
      </div>

      <!-- Con Descuento -->
      <div class="stat-card">
        <div class="stat-icon bg-[#ffe088] text-[#735c00]">
          <span class="material-symbols-outlined">sell</span>
        </div>
        <div class="stat-content">
          <p class="stat-label">En Oferta</p>
          <p class="stat-value">{{ stats.con_descuento }}</p>
        </div>
      </div>

      <!-- Total Categorías -->
      <div class="stat-card">
        <div class="stat-icon bg-wine">
          <span class="material-symbols-outlined text-white">category</span>
        </div>
        <div class="stat-content">
          <p class="stat-label">Categorías</p>
          <p class="stat-value">{{ stats.total_categorias }}</p>
        </div>
      </div>

      <!-- Total Marcas -->
      <div class="stat-card">
        <div class="stat-icon bg-[#efefd7] text-primary">
          <span class="material-symbols-outlined">storefront</span>
        </div>
        <div class="stat-content">
          <p class="stat-label">Marcas / Bodegas</p>
          <p class="stat-value">{{ stats.total_marcas }}</p>
        </div>
      </div>

      <!-- Total Variedades -->
      <div class="stat-card">
        <div class="stat-icon bg-[#ffe088] text-[#735c00]">
          <span class="material-symbols-outlined">local_florist</span>
        </div>
        <div class="stat-content">
          <p class="stat-label">Variedades (Cepas)</p>
          <p class="stat-value">{{ stats.total_variedades }}</p>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const stats = ref({})
const loading = ref(true)
const error = ref(null)

async function fetchStats() {
  loading.value = true
  error.value = null
  try {
    const { data } = await api.get('/v1/admin/dashboard')
    stats.value = data.data
  } catch (err) {
    error.value = 'No se pudieron cargar las estadísticas.'
    console.error(err)
  } finally {
    loading.value = false
  }
}

onMounted(fetchStats)
</script>

<style scoped>
.stat-card {
  background-color: var(--surface-container-low);
  padding: 2rem;
  border-radius: 12px;
  border: 1px solid rgba(218, 193, 191, 0.15);
  display: flex;
  align-items: center;
  gap: 1.5rem;
  transition: transform 0.2s, box-shadow 0.2s;
}
.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 20px rgba(42, 0, 2, 0.05);
  background-color: var(--surface-container-high);
}

.stat-icon {
  width: 4rem;
  height: 4rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.bg-wine {
  background-color: var(--primary);
}

.stat-icon .material-symbols-outlined {
  font-size: 2rem;
}

.stat-content {
  display: flex;
  flex-direction: column;
}

.stat-label {
  font-size: 0.75rem;
  color: var(--secondary);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-bottom: 0.25rem;
}

.stat-value {
  font-family: var(--font-headline);
  font-size: 2.25rem;
  font-weight: 700;
  color: var(--primary);
  line-height: 1;
}
</style>

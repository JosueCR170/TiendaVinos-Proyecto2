<template>
  <div class="dashboard-view">
    <header class="mb-8">
      <h1 class="text-3xl font-bold font-serif text-[#2a0002]">Dashboard</h1>
      <p class="text-gray-600 mt-2">Visión general del inventario y bodega.</p>
    </header>

    <div v-if="loading" class="flex justify-center p-12">
      <span class="material-symbols-outlined animate-spin text-4xl text-[#735c00]">progress_activity</span>
    </div>

    <div v-else-if="error" class="bg-red-100 text-red-800 p-4 rounded-md flex items-center gap-3">
      <span class="material-symbols-outlined">error</span>
      {{ error }}
      <button @click="fetchStats" class="ml-auto underline font-medium">Reintentar</button>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      
      <!-- Total Productos -->
      <div class="stat-card">
        <div class="stat-icon bg-blue-100 text-blue-800">
          <span class="material-symbols-outlined">wine_bar</span>
        </div>
        <div class="stat-content">
          <p class="stat-label">Total Productos</p>
          <p class="stat-value">{{ stats.total_productos }}</p>
        </div>
      </div>

      <!-- Productos Activos -->
      <div class="stat-card">
        <div class="stat-icon bg-green-100 text-green-800">
          <span class="material-symbols-outlined">check_circle</span>
        </div>
        <div class="stat-content">
          <p class="stat-label">Productos Activos</p>
          <p class="stat-value">{{ stats.productos_activos }}</p>
        </div>
      </div>

      <!-- Sin Stock -->
      <div class="stat-card">
        <div class="stat-icon bg-red-100 text-red-800">
          <span class="material-symbols-outlined">warning</span>
        </div>
        <div class="stat-content">
          <p class="stat-label">Sin Stock</p>
          <p class="stat-value text-red-600">{{ stats.productos_sin_stock }}</p>
        </div>
      </div>

      <!-- Con Descuento -->
      <div class="stat-card">
        <div class="stat-icon bg-yellow-100 text-yellow-800">
          <span class="material-symbols-outlined">sell</span>
        </div>
        <div class="stat-content">
          <p class="stat-label">En Oferta</p>
          <p class="stat-value">{{ stats.con_descuento }}</p>
        </div>
      </div>

      <!-- Total Categorías -->
      <div class="stat-card">
        <div class="stat-icon bg-purple-100 text-purple-800">
          <span class="material-symbols-outlined">category</span>
        </div>
        <div class="stat-content">
          <p class="stat-label">Categorías</p>
          <p class="stat-value">{{ stats.total_categorias }}</p>
        </div>
      </div>

      <!-- Total Marcas -->
      <div class="stat-card">
        <div class="stat-icon bg-indigo-100 text-indigo-800">
          <span class="material-symbols-outlined">storefront</span>
        </div>
        <div class="stat-content">
          <p class="stat-label">Marcas / Bodegas</p>
          <p class="stat-value">{{ stats.total_marcas }}</p>
        </div>
      </div>

      <!-- Total Variedades -->
      <div class="stat-card">
        <div class="stat-icon bg-pink-100 text-pink-800">
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
  background-color: white;
  padding: 1.5rem;
  border-radius: 0.75rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  border: 1px solid #f3f4f6;
  display: flex;
  align-items: center;
  gap: 1.25rem;
  transition: transform 0.2s, box-shadow 0.2s;
}
.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.stat-icon {
  width: 3.5rem;
  height: 3.5rem;
  border-radius: 9999px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-icon .material-symbols-outlined {
  font-size: 1.875rem;
  line-height: 2.25rem;
}

.stat-content {
  display: flex;
  flex-direction: column;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 0.25rem;
}

.stat-value {
  font-size: 1.875rem;
  font-weight: 700;
  color: #111827;
}

/* Colores de íconos */
.bg-blue-100 { background-color: #dbeafe; }
.text-blue-800 { color: #1e40af; }
.bg-green-100 { background-color: #dcfce7; }
.text-green-800 { color: #166534; }
.bg-red-100 { background-color: #fee2e2; }
.text-red-800 { color: #991b1b; }
.text-red-600 { color: #dc2626; }
.bg-yellow-100 { background-color: #fef9c3; }
.text-yellow-800 { color: #854d0e; }
.bg-purple-100 { background-color: #f3e8ff; }
.text-purple-800 { color: #6b21a8; }
.bg-indigo-100 { background-color: #e0e7ff; }
.text-indigo-800 { color: #3730a3; }
.bg-pink-100 { background-color: #fce7f3; }
.text-pink-800 { color: #9d174d; }
</style>

<template>
  <div class="index-view">
    <header class="index-header mb-6 flex items-end justify-between">
      <div class="header-info">
        <h1 class="text-3xl font-bold font-serif text-[#2a0002]">Marcas</h1>
        <p class="text-gray-600 mt-2">Gestiona las bodegas y marcas productoras.</p>
      </div>
      <div class="header-actions">
        <router-link :to="{ name: 'admin.marcas.create' }" class="btn-primary">
          <span class="material-symbols-outlined">add</span>
          Nueva Marca
        </router-link>
      </div>
    </header>

    <!-- Barra de Filtros -->
    <div class="filter-bar bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex gap-4 mb-6">
      <div class="flex-grow flex items-center bg-gray-50 border border-gray-200 rounded-md px-3 py-2">
        <span class="material-symbols-outlined text-gray-400 mr-2">search</span>
        <input 
          v-model="searchQuery" 
          type="text" 
          class="bg-transparent border-none outline-none w-full text-sm" 
          placeholder="Buscar marcas..."
        >
      </div>
      <button @click="clearSearch" v-if="searchQuery" class="text-gray-500 hover:text-gray-800 text-sm font-medium">Limpiar</button>
    </div>

    <!-- Tabla -->
    <div class="table-wrapper bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
      <div v-if="loading" class="p-8 flex justify-center">
        <span class="material-symbols-outlined animate-spin text-3xl text-[#735c00]">progress_activity</span>
      </div>
      <div v-else-if="error" class="p-8 text-center text-red-600">
        {{ error }}
      </div>
      <table v-else class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-xs uppercase tracking-wider">
            <th class="p-4 font-medium w-16">ID</th>
            <th class="p-4 font-medium">Nombre de la Marca / Bodega</th>
            <th class="p-4 font-medium text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr 
            v-for="item in filteredList" 
            :key="item.id_marca"
            class="hover:bg-gray-50 transition-colors"
          >
            <td class="p-4 text-sm text-gray-500">#{{ item.id_marca }}</td>
            <td class="p-4 font-medium text-gray-900">{{ item.nombre }}</td>
            <td class="p-4 text-right">
              <div class="flex justify-end gap-2">
                <router-link 
                  :to="{ name: 'admin.marcas.edit', params: { id: item.id_marca } }"
                  class="p-2 text-blue-600 hover:bg-blue-50 rounded-full transition-colors"
                  title="Editar"
                >
                  <span class="material-symbols-outlined text-sm">edit</span>
                </router-link>
                <button 
                  @click="confirmDelete(item)"
                  class="p-2 text-red-600 hover:bg-red-50 rounded-full transition-colors"
                  title="Eliminar"
                >
                  <span class="material-symbols-outlined text-sm">delete</span>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="filteredList.length === 0">
            <td colspan="3" class="p-8 text-center text-gray-500">
              No se encontraron marcas.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { MarcaController } from '@/controllers'
import { useNotificationStore } from '@/stores/notifications'

const notif = useNotificationStore()
const items = ref([])
const loading = ref(true)
const error = ref(null)
const searchQuery = ref('')

const filteredList = computed(() => {
  if (!searchQuery.value) return items.value
  const q = searchQuery.value.toLowerCase()
  return items.value.filter(item => item.nombre.toLowerCase().includes(q))
})

async function fetchItems() {
  loading.value = true
  try {
    const result = await MarcaController.obtenerMarcas({ per_page: 100 })

    if (!result.success) {
      throw new Error(result.message)
    }

    items.value = result.marcas
  } catch (err) {
    error.value = 'Error al cargar las marcas.'
    console.error(err)
  } finally {
    loading.value = false
  }
}

async function confirmDelete(item) {
  if (!confirm(`¿Estás seguro de eliminar la marca "${item.nombre}"?`)) return
  try {
    const result = await MarcaController.eliminarMarca(item.id_marca)

    if (!result.success) {
      throw new Error(result.message)
    }

    notif.show('Marca eliminada con éxito.', 'success')
    fetchItems()
  } catch (err) {
    const msg = err.message || 'Error al eliminar la marca. Puede estar en uso.'
    notif.show(msg, 'error')
  }
}

function clearSearch() { searchQuery.value = '' }

onMounted(fetchItems)
</script>

<style scoped>
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background-color: #2a0002;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
  transition: background-color 0.2s;
}
.btn-primary:hover { background-color: #3d0003; }
</style>

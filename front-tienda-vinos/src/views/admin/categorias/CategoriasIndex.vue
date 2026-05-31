<template>
  <div class="index-view">
    <header class="index-header">
      <div class="header-info">
        <h1 class="text-3xl font-bold font-serif text-[#2a0002]">Categorías</h1>
        <p class="text-gray-600 mt-2">Gestiona las categorías editoriales que estructuran la cava digital.</p>
      </div>
      <div class="header-actions">
        <router-link :to="{ name: 'admin.categorias.create' }" class="btn-primary">
          <span class="material-symbols-outlined">add</span>
          Nueva Categoría Principal
        </router-link>
      </div>
    </header>

    <!-- Barra de Filtros -->
    <div class="filter-bar mt-6 bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex gap-4">
      <div class="flex-grow flex items-center bg-gray-50 border border-gray-200 rounded-md px-3 py-2">
        <span class="material-symbols-outlined text-gray-400 mr-2">search</span>
        <input 
          v-model="searchQuery" 
          type="text" 
          class="bg-transparent border-none outline-none w-full text-sm" 
          placeholder="Buscar categorías..."
        >
      </div>
      <button @click="clearSearch" v-if="searchQuery" class="text-gray-500 hover:text-gray-800 text-sm font-medium">Limpiar</button>
    </div>

    <!-- Tabla -->
    <div class="table-wrapper mt-6 bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
      <div v-if="loading" class="p-8 flex justify-center">
        <span class="material-symbols-outlined animate-spin text-3xl text-[#735c00]">progress_activity</span>
      </div>
      <div v-else-if="error" class="p-8 text-center text-red-600">
        {{ error }}
      </div>
      <table v-else class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-xs uppercase tracking-wider">
            <th class="p-4 font-medium">Estructura de Categoría</th>
            <th class="p-4 font-medium">Nivel</th>
            <th class="p-4 font-medium">Padre</th>
            <th class="p-4 font-medium text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr 
            v-for="cat in filteredCategorias" 
            :key="cat.id_categoria"
            class="hover:bg-gray-50 transition-colors"
          >
            <td class="p-4">
              <div class="flex items-center gap-3" :class="{ 'pl-8': cat.nivel > 1 }">
                <span v-if="cat.nivel === 1" class="material-symbols-outlined text-[#735c00]">folder_open</span>
                <span v-else class="text-gray-300">—</span>
                <div>
                  <p class="font-medium text-gray-900">{{ cat.nombre }}</p>
                  <p v-if="cat.descripcion" class="text-xs text-gray-500 truncate max-w-xs">{{ cat.descripcion }}</p>
                </div>
              </div>
            </td>
            <td class="p-4">
              <span class="px-2 py-1 text-xs rounded-full font-medium"
                :class="cat.nivel === 1 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
              >
                {{ cat.nivel === 1 ? 'Principal' : 'Subnivel' }}
              </span>
            </td>
            <td class="p-4 text-sm text-gray-600">
              {{ cat.padre ? cat.padre.nombre : 'Raíz' }}
            </td>
            <td class="p-4 text-right">
              <div class="flex justify-end gap-2">
                <router-link 
                  v-if="cat.nivel === 1"
                  :to="{ name: 'admin.categorias.create', query: { parent_id: cat.id_categoria } }"
                  class="p-2 text-[#735c00] hover:bg-yellow-50 rounded-full transition-colors"
                  title="Agregar Subcategoría"
                >
                  <span class="material-symbols-outlined text-sm">add_circle</span>
                </router-link>
                <router-link 
                  :to="{ name: 'admin.categorias.edit', params: { id: cat.id_categoria } }"
                  class="p-2 text-blue-600 hover:bg-blue-50 rounded-full transition-colors"
                  title="Editar"
                >
                  <span class="material-symbols-outlined text-sm">edit</span>
                </router-link>
                <button 
                  @click="confirmDelete(cat)"
                  class="p-2 text-red-600 hover:bg-red-50 rounded-full transition-colors"
                  title="Eliminar"
                >
                  <span class="material-symbols-outlined text-sm">delete</span>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="filteredCategorias.length === 0">
            <td colspan="4" class="p-8 text-center text-gray-500">
              No se encontraron categorías.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'
import { useNotificationStore } from '@/stores/notifications'

const notif = useNotificationStore()
const categorias = ref([])
const loading = ref(true)
const error = ref(null)
const searchQuery = ref('')

const filteredCategorias = computed(() => {
  if (!searchQuery.value) return categorias.value
  const q = searchQuery.value.toLowerCase()
  return categorias.value.filter(c => 
    c.nombre.toLowerCase().includes(q) || 
    (c.descripcion && c.descripcion.toLowerCase().includes(q))
  )
})

async function fetchCategorias() {
  loading.value = true
  try {
    const { data } = await api.get('/admin/categorias')
    categorias.value = data.data
  } catch (err) {
    error.value = 'Error al cargar las categorías.'
    console.error(err)
  } finally {
    loading.value = false
  }
}

async function confirmDelete(cat) {
  if (!confirm(`¿Estás seguro de eliminar la categoría "${cat.nombre}"?\nEsta acción no se puede deshacer.`)) return
  
  try {
    await api.delete(`/admin/categorias/${cat.id_categoria}`)
    notif.show('Categoría eliminada con éxito.', 'success')
    fetchCategorias()
  } catch (err) {
    const msg = err.response?.data?.message || 'Error al eliminar la categoría.'
    notif.show(msg, 'error')
  }
}

function clearSearch() {
  searchQuery.value = ''
}

onMounted(fetchCategorias)
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
.btn-primary:hover {
  background-color: #3d0003;
}
</style>

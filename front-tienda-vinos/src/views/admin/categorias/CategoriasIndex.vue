<template>
  <div class="index-view">
    <header class="index-header">
      <div class="header-info">
        <h1>Arquitectura de Colección</h1>
        <p>Gestiona las categorías editoriales que estructuran la cava digital.</p>
      </div>
      <div class="header-actions">
        <router-link :to="{ name: 'admin.categorias.create' }" class="btn-create">
          <span class="material-symbols-outlined">add</span>
          <span>Nueva Categoría Principal</span>
        </router-link>
      </div>
    </header>

    <!-- Barra de Filtros -->
    <div class="filter-bar">
      <div class="filter-group">
        <span class="material-symbols-outlined filter-icon">search</span>
        <input 
          v-model="searchQuery" 
          type="text" 
          class="filter-input" 
          placeholder="Buscar categorías..."
        >
      </div>
      <button @click="clearSearch" v-if="searchQuery" class="btn-filter">Limpiar Filtros</button>
    </div>

    <!-- Tabla -->
    <div class="table-wrapper">
      <div v-if="loading" class="p-8 flex justify-center">
        <span class="material-symbols-outlined animate-spin text-3xl text-[#735c00]">progress_activity</span>
      </div>
      <div v-else-if="error" class="p-8 text-center text-red-600">
        {{ error }}
      </div>
      <table v-else class="premium-table">
        <thead>
          <tr>
            <th>Estructura de Categoría</th>
            <th>Nivel</th>
            <th>Padre</th>
            <th class="actions-cell">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr 
            v-for="cat in filteredCategorias" 
            :key="cat.id_categoria"
          >
            <td>
              <div class="product-cell" :style="{ paddingLeft: cat.nivel > 1 ? '2rem' : '0' }">
                <span v-if="cat.nivel === 1" class="material-symbols-outlined text-secondary" style="font-size: 20px;">folder</span>
                <span v-else class="text-on-surface-variant/40">—</span>
                <div class="product-name-info">
                  <span class="product-name">{{ cat.nombre }}</span>
                  <span v-if="cat.descripcion" class="product-meta">{{ cat.descripcion }}</span>
                </div>
              </div>
            </td>
            <td>
              <span class="badge" :class="cat.nivel === 1 ? 'badge-success' : 'badge-neutral'">
                {{ cat.nivel === 1 ? 'Principal' : 'Subnivel' }}
              </span>
            </td>
            <td class="text-sm text-on-surface-variant">
              {{ cat.padre ? cat.padre.nombre : 'Raíz' }}
            </td>
            <td class="actions-cell">
              <div class="flex justify-end gap-2">
                <router-link 
                  v-if="cat.nivel === 1"
                  :to="{ name: 'admin.categorias.create', query: { parent_id: cat.id_categoria } }"
                  class="action-btn"
                  title="Agregar Subcategoría"
                >
                  <span class="material-symbols-outlined">add_circle</span>
                </router-link>
                <router-link 
                  :to="{ name: 'admin.categorias.edit', params: { id: cat.id_categoria } }"
                  class="action-btn"
                  title="Editar"
                >
                  <span class="material-symbols-outlined">edit</span>
                </router-link>
                <button 
                  @click="confirmDelete(cat)"
                  class="action-btn delete"
                  title="Eliminar"
                >
                  <span class="material-symbols-outlined">delete</span>
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
import { CategoriaController } from '@/controllers'
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
    const result = await CategoriaController.obtenerCategorias({ per_page: 100 })

    if (!result.success) {
      throw new Error(result.message)
    }

    categorias.value = result.categorias
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
    const result = await CategoriaController.eliminarCategoria(cat.id_categoria)

    if (!result.success) {
      throw new Error(result.message)
    }

    notif.show('Categoría eliminada con éxito.', 'success')
    fetchCategorias()
  } catch (err) {
    const msg = err.message || 'Error al eliminar la categoría.'
    notif.show(msg, 'error')
  }
}

function clearSearch() {
  searchQuery.value = ''
}

onMounted(fetchCategorias)
</script>

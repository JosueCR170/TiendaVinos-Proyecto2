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
            v-for="cat in displayCategorias" 
            :key="cat.id_categoria"
          >
            <td>
              <div class="product-cell" :style="{ paddingLeft: cat.nivel > 1 ? '2rem' : '0' }">
                <span v-if="cat.nivel === 1" class="material-symbols-outlined text-secondary" style="font-size: 20px;">folder</span>
                <span v-else class="text-on-surface-variant/40">—</span>
                <div class="product-name-info">
                  <span class="product-name">{{ cat.nombre }}</span>
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
                  @click="openDeleteModal(cat)"
                  class="action-btn delete"
                  title="Eliminar"
                >
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="displayCategorias.length === 0">
            <td colspan="4" class="p-8 text-center text-gray-500">
              No se encontraron categorías.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
      <Teleport to="body">
        <div v-if="deleteModal.visible" class="modal-overlay" @click.self="closeDeleteModal">
          <div class="modal-content">
            <div class="modal-header-icon">
              <span class="material-symbols-outlined">warning</span>
            </div>
            <h2>¿Eliminar Categoría?</h2>
            <p>Estás a punto de eliminar <strong>{{ deleteModal.categoria?.nombre }}</strong>. Esta acción es irreversible.</p>
            <div class="modal-warning">
              <span class="material-symbols-outlined" style="font-size: 16px;">info</span>
              <span>Si la categoría está en uso por productos, la eliminación fallará.</span>
            </div>
            <div class="modal-actions">
              <button class="btn-modal-cancel" @click="closeDeleteModal" :disabled="deleteModal.loading">Cancelar</button>
              <button class="btn-modal-confirm" :disabled="deleteModal.loading" @click="confirmDelete">{{ deleteModal.loading ? 'Eliminando...' : 'Eliminar Permanentemente' }}</button>
            </div>
          </div>
        </div>
      </Teleport>
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

const displayCategorias = computed(() => {
  const q = searchQuery.value.trim().toLowerCase()

  // helper to match search
  const matches = (c) => {
    if (!q) return true
    return c.nombre.toLowerCase().includes(q) || (c.descripcion && c.descripcion.toLowerCase().includes(q))
  }

  // if searching, just return filtered and sorted by nombre
  if (q) {
    return categorias.value
      .filter(matches)
      .sort((a, b) => a.nombre.localeCompare(b.nombre))
  }

  // No search: group by parent — show each principal category followed by its subcategories
  const parents = categorias.value.filter(c => c.nivel === 1).sort((a, b) => a.nombre.localeCompare(b.nombre))
  const children = categorias.value.filter(c => c.nivel !== 1)

  const mapChildren = {}
  children.forEach(ch => {
    const pid = ch.padre ? ch.padre.id_categoria : null
    if (!mapChildren[pid]) mapChildren[pid] = []
    mapChildren[pid].push(ch)
  })

  const result = []
  parents.forEach(p => {
    result.push(p)
    const chs = mapChildren[p.id_categoria] || []
    chs.sort((a, b) => a.nombre.localeCompare(b.nombre))
    chs.forEach(c => result.push(c))
  })

  // also include any orphan children or categories without parent at the end
  const parentIds = new Set(parents.map(p => p.id_categoria))
  categorias.value.forEach(c => {
    if (c.nivel !== 1 && (!c.padre || !parentIds.has(c.padre.id_categoria))) {
      result.push(c)
    }
  })

  return result
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
const deleteModal = ref({ visible: false, categoria: null, loading: false })

function openDeleteModal(cat) {
  deleteModal.value = { visible: true, categoria: cat, loading: false }
}

function closeDeleteModal() {
  if (deleteModal.value.loading) return
  deleteModal.value = { visible: false, categoria: null, loading: false }
}

async function confirmDelete() {
  if (!deleteModal.value || !deleteModal.value.categoria) return
  deleteModal.value.loading = true
  try {
    const result = await CategoriaController.eliminarCategoria(deleteModal.value.categoria.id_categoria)
    if (!result.success) throw new Error(result.message)
    notif.show('Categoría eliminada con éxito.', 'success')
    deleteModal.value.loading = false
    closeDeleteModal()
    await fetchCategorias()
  } catch (err) {
    deleteModal.value.loading = false
    const msg = err.message || 'Error al eliminar la categoría.'
    notif.show(msg, 'error')
  }
}

function clearSearch() {
  searchQuery.value = ''
}

onMounted(fetchCategorias)
</script>

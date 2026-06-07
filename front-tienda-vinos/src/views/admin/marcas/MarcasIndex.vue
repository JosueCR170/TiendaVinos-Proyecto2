<template>
  <div class="index-view">
    <header class="index-header">
      <div class="header-info">
        <h1>Casas y Bodegas</h1>
        <p>Gestiona los productores y casas vinícolas que dan vida a la colección.</p>
      </div>
      <div class="header-actions">
        <router-link :to="{ name: 'admin.marcas.create' }" class="btn-create">
          <span class="material-symbols-outlined">add</span>
          <span>Nueva Bodega</span>
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
          placeholder="Buscar por nombre o descripción..."
        >
      </div>
      <div class="filter-group">
        <span class="material-symbols-outlined filter-icon">public</span>
        <input 
          v-model="searchPais" 
          type="text" 
          class="filter-input" 
          placeholder="Buscar por país..."
        >
      </div>
      <button @click="clearFilters" v-if="searchQuery || searchPais" class="btn-filter">Limpiar Filtros</button>
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
            <th>Bodega y Origen</th>
            <th>Presencia Digital</th>
            <th class="actions-cell">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr 
            v-for="item in filteredList" 
            :key="item.id_marca"
          >
            <td>
              <div class="product-cell">
                <div class="product-name-info">
                  <span class="product-name">{{ item.nombre }}</span>
                  <span class="product-meta">{{ item.pais || 'N/A' }} — {{ item.descripcion || 'Sin descripción' }}</span>
                </div>
              </div>
            </td>
            <td>
              <a v-if="item.sitio_web" :href="item.sitio_web" target="_blank" class="text-secondary hover:text-primary transition-colors text-sm font-medium flex items-center gap-1">
                <span class="material-symbols-outlined text-base">language</span>
                Visitar Sitio
              </a>
              <span v-else class="text-on-surface-variant/40 text-sm">N/A</span>
            </td>
            <td class="actions-cell">
              <div class="flex justify-end gap-2">
                <router-link 
                  :to="{ name: 'admin.marcas.edit', params: { id: item.id_marca } }"
                  class="action-btn"
                  title="Editar"
                >
                  <span class="material-symbols-outlined">edit</span>
                </router-link>
                <button 
                  @click="openDeleteModal(item)"
                  class="action-btn delete"
                  title="Eliminar"
                >
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="filteredList.length === 0">
            <td colspan="3" class="p-8 text-center text-gray-500">
              No se encontraron marcas o bodegas.
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
            <h2>¿Eliminar Marca?</h2>
            <p>Estás a punto de eliminar <strong>{{ deleteModal.item?.nombre }}</strong>. Esta acción es irreversible.</p>
            <div class="modal-warning">
              <span class="material-symbols-outlined" style="font-size: 16px;">info</span>
              <span>Si la marca está asociada a productos, la eliminación fallará.</span>
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
import { MarcaController } from '@/controllers'
import { useNotificationStore } from '@/stores/notifications'

const notif = useNotificationStore()
const items = ref([])
const loading = ref(true)
const error = ref(null)
const searchQuery = ref('')
const searchPais = ref('')

const filteredList = computed(() => {
  return items.value.filter(item => {
    const matchName = !searchQuery.value || item.nombre.toLowerCase().includes(searchQuery.value.toLowerCase()) || (item.descripcion && item.descripcion.toLowerCase().includes(searchQuery.value.toLowerCase()))
    const matchPais = !searchPais.value || (item.pais && item.pais.toLowerCase().includes(searchPais.value.toLowerCase()))
    return matchName && matchPais
  })
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
const deleteModal = ref({ visible: false, item: null, loading: false })

function openDeleteModal(item) {
  deleteModal.value = { visible: true, item, loading: false }
}

function closeDeleteModal() {
  if (deleteModal.value.loading) return
  deleteModal.value = { visible: false, item: null, loading: false }
}

async function confirmDelete() {
  if (!deleteModal.value || !deleteModal.value.item) return
  deleteModal.value.loading = true
  try {
    const result = await MarcaController.eliminarMarca(deleteModal.value.item.id_marca)
    if (!result.success) throw new Error(result.message)
    notif.show('Marca eliminada con éxito.', 'success')
    deleteModal.value.loading = false
    closeDeleteModal()
    await fetchItems()
  } catch (err) {
    deleteModal.value.loading = false
    const msg = err.message || 'Error al eliminar la marca. Puede estar en uso.'
    notif.show(msg, 'error')
  }
}

function clearFilters() {
  searchQuery.value = ''
  searchPais.value = ''
}

onMounted(fetchItems)
</script>

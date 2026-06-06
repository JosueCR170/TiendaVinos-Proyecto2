<template>
  <div class="index-view">
    <header class="index-header">
      <div class="header-info">
        <h1>Variedades de Uva</h1>
        <p>Gestiona las cepas y variedades que definen el carácter de cada botella.</p>
      </div>
      <div class="header-actions">
        <router-link :to="{ name: 'admin.variedades.create' }" class="btn-create">
          <span class="material-symbols-outlined">add</span>
          <span>Nueva Variedad</span>
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
          placeholder="Buscar por nombre o tipo..."
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
            <th>Variedad</th>
            <th>Tipo</th>
            <th class="actions-cell">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr 
            v-for="item in filteredList" 
            :key="item.id_variedad"
          >
            <td>
              <div class="product-cell">
                <div class="product-name-info">
                  <span class="product-name">{{ item.nombre }}</span>
                  <span class="product-meta">Variedad de tipo {{ item.tipo || 'N/A' }}</span>
                </div>
              </div>
            </td>
            <td>
              <span 
                class="badge" 
                :class="{
                  'badge-tinta': item.tipo === 'Tinta',
                  'badge-blanca': item.tipo === 'Blanca',
                  'badge-aromatica': item.tipo === 'Aromatica'
                }"
              >
                {{ item.tipo || 'N/A' }}
              </span>
            </td>
            <td class="actions-cell">
              <div class="flex justify-end gap-2">
                <router-link 
                  :to="{ name: 'admin.variedades.edit', params: { id: item.id_variedad } }"
                  class="action-btn"
                  title="Editar"
                >
                  <span class="material-symbols-outlined">edit</span>
                </router-link>
                <button 
                  @click="confirmDelete(item)"
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
              No se encontraron variedades.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { VariedadController } from '@/controllers'
import { useNotificationStore } from '@/stores/notifications'

const notif = useNotificationStore()
const items = ref([])
const loading = ref(true)
const error = ref(null)
const searchQuery = ref('')

const filteredList = computed(() => {
  if (!searchQuery.value) return items.value
  const q = searchQuery.value.toLowerCase()
  return items.value.filter(item => 
    item.nombre.toLowerCase().includes(q) || 
    (item.tipo && item.tipo.toLowerCase().includes(q))
  )
})

async function fetchItems() {
  loading.value = true
  try {
    const result = await VariedadController.obtenerVariedades({ per_page: 100 })

    if (!result.success) {
      throw new Error(result.message)
    }

    items.value = result.variedades
  } catch (err) {
    error.value = 'Error al cargar las variedades.'
    console.error(err)
  } finally {
    loading.value = false
  }
}

async function confirmDelete(item) {
  if (!confirm(`¿Estás seguro de eliminar la variedad "${item.nombre}"?`)) return
  try {
    const result = await VariedadController.eliminarVariedad(item.id_variedad)

    if (!result.success) {
      throw new Error(result.message)
    }

    notif.show('Variedad eliminada con éxito.', 'success')
    fetchItems()
  } catch (err) {
    const msg = err.message || 'Error al eliminar la variedad. Puede estar en uso.'
    notif.show(msg, 'error')
  }
}

function clearSearch() { searchQuery.value = '' }

onMounted(fetchItems)
</script>

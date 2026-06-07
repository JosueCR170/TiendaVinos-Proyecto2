<!-- resources/js/views/admin/productos/ProductosIndex.vue -->

<template>
  <div class="index-view">
    <!-- ── Header ─────────────────────────────────────────────────────────── -->
    <header class="index-header">
      <div class="header-info">
        <h1>Inventario de Productos</h1>
        <p>Gestiona la selección editorial de licores finos y vinos de cosecha.</p>
      </div>
      <div class="header-actions">
        <router-link :to="{ name: 'admin.productos.create' }" class="btn-create">
          <span class="material-symbols-outlined">add</span>
          <span>Nuevo Producto</span>
        </router-link>
      </div>
    </header>

    <!-- ── Barra de filtros ───────────────────────────────────────────────── -->
    <div class="filter-bar">
      <div class="filter-group">
        <span class="material-symbols-outlined filter-icon">search</span>
        <input
          v-model="filters.search"
          type="text"
          class="filter-input"
          placeholder="Buscar por nombre o descripción..."
          @keyup.enter="applyFilters"
        />
      </div>

      <div class="filter-group">
        <select v-model="filters.categoria" class="premium-select">
          <option value="">Todas las Categorías</option>
          <option
            v-for="categoria in categorias"
            :key="categoria.id_categoria"
            :value="categoria.id_categoria"
          >
            {{ categoria.nombre }}
          </option>
        </select>
      </div>

      <div class="filter-group">
        <select v-model="filters.marca" class="premium-select">
          <option value="">Todas las Marcas</option>
          <option
            v-for="marca in marcas"
            :key="marca.id_marca"
            :value="marca.id_marca"
          >
            {{ marca.nombre }}
          </option>
        </select>
      </div>

      <div class="filter-group">
        <input
          v-model="filters.pais"
          list="paises-list"
          class="premium-datalist-input"
          placeholder="Buscar por país..."
        />
        <datalist id="paises-list">
          <option v-for="(nombre, code) in paises" :key="code" :value="nombre" />
        </datalist>
      </div>

      <button class="btn-filter" @click="applyFilters">Filtrar</button>

      <button
        v-if="hasActiveFilters"
        class="btn-reset"
        @click="clearFilters"
      >
        Limpiar Filtros
      </button>
    </div>

    <!-- ── Tabla ──────────────────────────────────────────────────────────── -->
    <div class="table-wrapper">

      <!-- Estado de carga -->
      <div v-if="loading" class="loading-state">
        <span class="material-symbols-outlined spinning">progress_activity</span>
        <span>Cargando productos...</span>
      </div>

      <!-- Error de carga -->
      <div v-else-if="error" class="error-state">
        <span class="material-symbols-outlined">error</span>
        <span>{{ error }}</span>
        <button class="btn-filter" @click="fetchProductos">Reintentar</button>
      </div>

      <table v-else class="premium-table">
        <thead>
          <tr>
            <th>
              <button class="sort-link" @click="toggleSort('nombre')">
                Producto
                <span class="material-symbols-outlined sort-icon" :class="{ active: sort.field === 'nombre' }">
                  {{ sortIcon('nombre') }}
                </span>
              </button>
            </th>
            <th>
              <div style="display: flex; gap: 0.5rem; align-items: center;">
                <button class="sort-link" @click="toggleSort('categoria')">
                  Categoría
                  <span class="material-symbols-outlined sort-icon" :class="{ active: sort.field === 'categoria' }">
                    {{ sortIcon('categoria') }}
                  </span>
                </button>
                <span style="opacity: 0.3;">/</span>
                <button class="sort-link" @click="toggleSort('marca')">
                  Marca
                  <span class="material-symbols-outlined sort-icon" :class="{ active: sort.field === 'marca' }">
                    {{ sortIcon('marca') }}
                  </span>
                </button>
              </div>
            </th>
            <th>
              <button class="sort-link" @click="toggleSort('cantidad')">
                Stock
                <span class="material-symbols-outlined sort-icon" :class="{ active: sort.field === 'cantidad' }">
                  {{ sortIcon('cantidad') }}
                </span>
              </button>
            </th>
            <th>
              <button class="sort-link" @click="toggleSort('precio')">
                Precio
                <span class="material-symbols-outlined sort-icon" :class="{ active: sort.field === 'precio' }">
                  {{ sortIcon('precio') }}
                </span>
              </button>
            </th>
            <th>
              <button class="sort-link" @click="toggleSort('estado')">
                Estado
                <span class="material-symbols-outlined sort-icon" :class="{ active: sort.field === 'estado' }">
                  {{ sortIcon('estado') }}
                </span>
              </button>
            </th>
            <th class="actions-cell">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <!-- Filas de productos -->
          <tr v-for="producto in productos" :key="producto.id_producto">
            <td>
              <div class="product-cell">
                <div class="product-img-wrapper">
                  <img
                    v-if="producto.imagen_url"
                    :src="producto.imagen_url"
                    :alt="producto.nombre"
                  />
                  <span v-else class="material-symbols-outlined" style="opacity: 0.3;">wine_bar</span>
                </div>
                <div class="product-name-info">
                  <span class="product-name">{{ producto.nombre }}</span>
                  <span class="product-meta">
                    {{ producto.pais ?? 'N/A' }} • {{ producto.contenido_ml ? producto.contenido_ml + 'ml' : 'N/A' }}
                  </span>
                </div>
              </div>
            </td>

            <td>
              <div class="category-cell">
                <span class="category-name">
                  <small
                    v-if="producto.categoria?.padre"
                    style="opacity: 0.5; font-size: 0.7rem; display: block;"
                  >
                    {{ producto.categoria.padre.nombre }}
                  </small>
                  {{ producto.categoria?.nombre ?? 'Sin Categoría' }}
                </span>
                <span class="brand-name-sm">
                  {{ producto.marca?.nombre ?? 'Sin Marca' }}
                </span>
              </div>
            </td>

            <td>
              <span
                class="stock-count"
                :class="producto.cantidad <= 10 ? 'stock-low' : 'stock-normal'"
              >
                {{ producto.cantidad }} Unidades
              </span>
            </td>

            <td>
              <span class="price-text">{{ formatPrice(producto.precio) }}</span>
            </td>

            <td>
              <span class="badge" :class="producto.estado ? 'badge-success' : 'badge-error'">
                {{ producto.estado ? 'Activo' : 'Inactivo' }}
              </span>
            </td>

            <td class="actions-cell">
              <div class="actions-wrapper">
                <router-link
                  :to="{ name: 'admin.productos.edit', params: { id: producto.id_producto } }"
                  class="action-btn"
                  title="Editar"
                >
                  <span class="material-symbols-outlined">edit</span>
                </router-link>

                <button
                  class="action-btn delete"
                  title="Eliminar"
                  @click="openDeleteModal(producto)"
                >
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </div>
            </td>
          </tr>

          <!-- Estado vacío -->
          <tr v-if="productos.length === 0">
            <td colspan="6" style="text-align: center; padding: 3rem; color: rgba(27, 29, 14, 0.4);">
              No se encontraron productos que coincidan con los criterios.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- ── Paginación ─────────────────────────────────────────────────────── -->
    <div v-if="pagination.total > 0" class="pagination-container">
      <div class="pagination-info">
        Mostrando
        <strong>{{ pagination.from ?? 0 }}</strong> a
        <strong>{{ pagination.to ?? 0 }}</strong> de
        <strong>{{ pagination.total }}</strong> productos
      </div>

      <div class="pagination-controls">
        <!-- Anterior -->
        <button
          class="page-link page-icon"
          :class="{ 'page-disabled': pagination.currentPage === 1 }"
          :disabled="pagination.currentPage === 1"
          @click="goToPage(pagination.currentPage - 1)"
        >
          <span class="material-symbols-outlined">chevron_left</span>
        </button>

        <!-- Páginas visibles -->
        <template v-for="page in visiblePages" :key="page">
          <span v-if="page === pagination.currentPage" class="page-current">
            {{ page }}
          </span>
          <button v-else class="page-link" @click="goToPage(page)">
            {{ page }}
          </button>
        </template>

        <!-- Siguiente -->
        <button
          class="page-link page-icon"
          :class="{ 'page-disabled': pagination.currentPage === pagination.lastPage }"
          :disabled="pagination.currentPage === pagination.lastPage"
          @click="goToPage(pagination.currentPage + 1)"
        >
          <span class="material-symbols-outlined">chevron_right</span>
        </button>
      </div>
    </div>

    <!-- ── Modal de eliminación ───────────────────────────────────────────── -->
    <Teleport to="body">
      <div
        v-if="deleteModal.visible"
        class="modal-overlay"
        @click.self="closeDeleteModal"
      >
        <div class="modal-content">
          <div class="modal-header-icon">
            <span class="material-symbols-outlined">warning</span>
          </div>
          <h2>¿Eliminar Producto?</h2>
          <p>
            Estás a punto de eliminar
            <strong>{{ deleteModal.producto?.nombre }}</strong>
            de la colección.
          </p>
          <div class="modal-warning">
            <span class="material-symbols-outlined" style="font-size: 16px;">info</span>
            <span>
              Esta acción es irreversible. Se eliminarán permanentemente todos los
              datos técnicos y notas de cata asociados.
            </span>
          </div>
          <div class="modal-actions">
            <button class="btn-modal-cancel" @click="closeDeleteModal">
              Mantener en Cava
            </button>
            <button
              class="btn-modal-confirm"
              :disabled="deleteModal.loading"
              @click="confirmDelete"
            >
              {{ deleteModal.loading ? 'Eliminando...' : 'Eliminar Permanentemente' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ProductoController } from '@/controllers'
import { useNotificationStore } from '@/stores/notifications'

const route  = useRoute()
const router = useRouter()
const notif  = useNotificationStore()

// ── Estado ────────────────────────────────────────────────────────────────────

const productos  = ref([])
const categorias = ref([])
const marcas     = ref([])
const paises     = ref({})
const loading    = ref(false)
const error      = ref(null)

const pagination = reactive({
  currentPage: 1,
  lastPage:    1,
  total:       0,
  from:        null,
  to:          null,
})

const sort = reactive({
  field:     'id_producto',
  direction: 'desc',
})

// Inicializar filtros desde la query string de la URL
const filters = reactive({
  search:    route.query.search    ?? '',
  categoria: route.query.categoria ?? '',
  marca:     route.query.marca     ?? '',
  pais:      route.query.pais      ?? '',
})

const deleteModal = reactive({
  visible:  false,
  producto: null,
  loading:  false,
})

// ── Computed ──────────────────────────────────────────────────────────────────

const hasActiveFilters = computed(() =>
  Object.values(filters).some(v => v !== '')
)

const visiblePages = computed(() => {
  const current = pagination.currentPage
  const last    = pagination.lastPage
  const start   = Math.max(1, current - 2)
  const end     = Math.min(last, current + 2)
  return Array.from({ length: end - start + 1 }, (_, i) => start + i)
})

// ── Helpers ───────────────────────────────────────────────────────────────────

function sortIcon(field) {
  if (sort.field !== field) return 'unfold_more'
  return sort.direction === 'asc' ? 'arrow_upward' : 'arrow_downward'
}

function formatPrice(value) {
  return new Intl.NumberFormat('en-US', {
    style:    'currency',
    currency: 'USD',
    minimumFractionDigits: 2,
  }).format(value)
}

// ── Sincronización URL ← → estado ─────────────────────────────────────────────
// Mantiene la URL actualizada para que los filtros sean compartibles / navegables

function buildQuery(page = 1) {
  const q = { page }
  if (filters.search)    q.search    = filters.search
  if (filters.categoria) q.categoria = filters.categoria
  if (filters.marca)     q.marca     = filters.marca
  if (filters.pais)      q.pais      = filters.pais
  if (sort.field !== 'id_producto') q.sort = sort.field
  if (sort.direction !== 'desc')    q.direction = sort.direction
  return q
}

function syncFromQuery() {
  filters.search    = route.query.search    ?? ''
  filters.categoria = route.query.categoria ?? ''
  filters.marca     = route.query.marca     ?? ''
  filters.pais      = route.query.pais      ?? ''
  sort.field        = route.query.sort      ?? 'id_producto'
  sort.direction    = route.query.direction ?? 'desc'
  pagination.currentPage = Number(route.query.page ?? 1)
}

// ── Fetch ─────────────────────────────────────────────────────────────────────

async function fetchFormData() {
  try {
    const result = await ProductoController.obtenerFormData()

    if (!result.success) {
      throw new Error(result.message)
    }

    categorias.value = result.categorias
    marcas.value     = result.marcas
    paises.value     = result.paises
  } catch {
    // No bloqueante: la tabla sigue siendo útil sin los filtros de selects
    console.warn('No se pudieron cargar los datos de formulario.')
  }
}

async function fetchProductos() {
  loading.value = true
  error.value   = null

  try {
    const result = await ProductoController.obtenerProductos({
      page:      pagination.currentPage,
      search:    filters.search    || undefined,
      categoria: filters.categoria || undefined,
      marca:     filters.marca     || undefined,
      pais:      filters.pais      || undefined,
      sort:      sort.field,
      direction: sort.direction,
    })

    if (!result.success) {
      throw new Error(result.message)
    }

    productos.value        = result.productos
    pagination.currentPage = result.pagination.currentPage
    pagination.lastPage    = result.pagination.lastPage
    pagination.total       = result.pagination.total
    pagination.from        = result.pagination.from
    pagination.to          = result.pagination.to
  } catch (e) {
    error.value = 'Error al cargar los productos. Intente nuevamente.'
    console.error(e)
  } finally {
    loading.value = false
  }
}

// ── Acciones de filtro / orden ─────────────────────────────────────────────────

function applyFilters() {
  router.push({ query: buildQuery(1) })
}

function clearFilters() {
  filters.search    = ''
  filters.categoria = ''
  filters.marca     = ''
  filters.pais      = ''
  router.push({ query: {} })
}

function toggleSort(field) {
  if (sort.field === field) {
    sort.direction = sort.direction === 'asc' ? 'desc' : 'asc'
  } else {
    sort.field     = field
    sort.direction = 'asc'
  }
  router.push({ query: buildQuery(pagination.currentPage) })
}

function goToPage(page) {
  if (page < 1 || page > pagination.lastPage) return
  router.push({ query: buildQuery(page) })
}

// ── Modal de eliminación ──────────────────────────────────────────────────────

function openDeleteModal(producto) {
  deleteModal.producto = producto
  deleteModal.visible  = true
}

function closeDeleteModal() {
  if (deleteModal.loading) return
  deleteModal.visible  = false
  deleteModal.producto = null
}

async function confirmDelete() {
  if (!deleteModal.producto) return
  deleteModal.loading = true

  try {
    const result = await ProductoController.eliminarProducto(deleteModal.producto.id_producto)

    if (!result.success) {
      throw new Error(result.message)
    }

    notif.show('Producto eliminado de la bodega', 'success')
      // quitar estado de carga antes de cerrar el modal para permitir el cierre
      deleteModal.loading = false
      closeDeleteModal()
    // Si la página actual queda vacía al borrar el último ítem, retroceder una página
    const isLastItemOnPage = productos.value.length === 1 && pagination.currentPage > 1
    await fetchProductos()
    if (isLastItemOnPage) goToPage(pagination.currentPage - 1)
  } catch (e) {
    const msg = e.message ?? 'Error al eliminar el producto.'
    notif.show(msg, 'error')
  } finally {
    deleteModal.loading = false
  }
}

// ── Ciclo de vida ─────────────────────────────────────────────────────────────

// Cuando la query string de la URL cambia (back/forward, applyFilters, etc.)
// sincronizamos el estado y recargamos
watch(
  () => route.query,
  () => {
    syncFromQuery()
    fetchProductos()
  }
)

onMounted(() => {
  syncFromQuery()
  fetchFormData()   // carga selects de filtros (no bloquea tabla)
  fetchProductos()  // carga la tabla
})
</script>

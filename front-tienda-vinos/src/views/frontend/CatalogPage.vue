<template>
  <main class="flex min-h-screen px-6 md:px-12 pt-12 pb-24 gap-16">

    <!-- ── Sidebar de filtros ─────────────────────────────────────────────── -->
    <aside class="w-72 flex-shrink-0 space-y-10 pt-4">
      <header>
        <h2 class="filter-heading">Filtrar Colección</h2>
        <p class="filter-sub">Refinando la bodega</p>
      </header>

      <!-- Búsqueda -->
      <section class="space-y-4">
        <h3 class="filter-section-title">
          <span class="material-symbols-outlined filter-icon">search</span>
          Buscar Vino
        </h3>
        <div class="relative">
          <input
            v-model="filters.buscar"
            @keyup.enter="applyFilters"
            type="text"
            placeholder="Nombre del vino..."
            class="filter-input"
          />
          <button @click="applyFilters" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#2a0002]/40 hover:text-[#2a0002] transition-colors">
            <span class="material-symbols-outlined text-xl">arrow_forward</span>
          </button>
        </div>
      </section>

      <!-- Categoría -->
      <section class="space-y-4">
        <h3 class="filter-section-title">
          <span class="material-symbols-outlined filter-icon">category</span>
          Categoría
        </h3>
        <div class="flex flex-col space-y-2">
          <label class="radio-label">
            <input type="radio" v-model="filters.categoria" value="" @change="applyFilters" class="accent-[#2a0002]"/>
            <span class="radio-text">Todas</span>
          </label>
          <label v-for="cat in meta.categorias" :key="cat.id_categoria" class="radio-label">
            <input type="radio" v-model="filters.categoria" :value="cat.id_categoria" @change="applyFilters" class="accent-[#2a0002]"/>
            <span class="radio-text">{{ cat.nombre }}</span>
          </label>
        </div>
      </section>

      <!-- País -->
      <section class="space-y-4">
        <h3 class="filter-section-title">
          <span class="material-symbols-outlined filter-icon">public</span>
          País de Origen
        </h3>
        <div class="flex flex-col space-y-2 max-h-48 overflow-y-auto pr-2">
          <label class="radio-label">
            <input type="radio" v-model="filters.pais" value="" @change="applyFilters" class="accent-[#2a0002]"/>
            <span class="radio-text">Todos</span>
          </label>
          <label v-for="pais in meta.paises" :key="pais" class="radio-label">
            <input type="radio" v-model="filters.pais" :value="pais" @change="applyFilters" class="accent-[#2a0002]"/>
            <span class="radio-text">{{ pais }}</span>
          </label>
        </div>
      </section>

      <!-- Bodega -->
      <section class="space-y-4">
        <h3 class="filter-section-title">
          <span class="material-symbols-outlined filter-icon">storefront</span>
          Bodega
        </h3>
        <div class="flex flex-col space-y-2 max-h-48 overflow-y-auto pr-2">
          <label class="radio-label">
            <input type="radio" v-model="filters.marca" value="" @change="applyFilters" class="accent-[#2a0002]"/>
            <span class="radio-text">Todas</span>
          </label>
          <label v-for="marca in meta.marcas" :key="marca.id_marca" class="radio-label">
            <input type="radio" v-model="filters.marca" :value="marca.id_marca" @change="applyFilters" class="accent-[#2a0002]"/>
            <span class="radio-text">{{ marca.nombre }}</span>
          </label>
        </div>
      </section>

      <!-- Variedad -->
      <section class="space-y-4">
        <h3 class="filter-section-title">
          <span class="material-symbols-outlined filter-icon">wine_bar</span>
          Cepa / Variedad
        </h3>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="v in meta.variedades" :key="v.id_variedad"
            @click="toggleVariedad(v.id_variedad)"
            class="variedad-btn"
            :class="filters.variedad == v.id_variedad ? 'variedad-active' : 'variedad-inactive'"
          >
            {{ v.nombre }}
          </button>
        </div>
      </section>

      <!-- Solo descuentos -->
      <section class="space-y-4">
        <h3 class="filter-section-title">
          <span class="material-symbols-outlined filter-icon">sell</span>
          Ofertas
        </h3>
        <label class="radio-label cursor-pointer">
          <input type="checkbox" v-model="filters.solo_descuentos" @change="applyFilters" class="accent-[#2a0002] rounded"/>
          <span class="text-sm font-medium text-red-600 font-bold italic group-hover:text-[#2a0002]">Solo con Descuento</span>
        </label>
      </section>

      <!-- Ordenar -->
      <section class="space-y-4">
        <h3 class="filter-section-title">
          <span class="material-symbols-outlined filter-icon">sort</span>
          Ordenar por
        </h3>
        <select v-model="filters.orden" @change="applyFilters" class="filter-select">
          <option value="newest">Más recientes</option>
          <option value="precio_asc">Precio: Menor a Mayor</option>
          <option value="precio_desc">Precio: Mayor a Menor</option>
          <option value="nombre">Nombre A-Z</option>
        </select>
      </section>

      <!-- Limpiar -->
      <button v-if="hasFilters" @click="clearFilters" class="inline-flex items-center gap-2 text-xs font-label uppercase tracking-widest text-[#745853] hover:text-[#2a0002] transition-colors">
        <span class="material-symbols-outlined text-sm">close</span> Limpiar filtros
      </button>
    </aside>

    <!-- ── Grilla de productos ─────────────────────────────────────────────── -->
    <section class="flex-grow">
      <div class="flex justify-between items-end mb-12">
        <div>
          <h1 class="catalog-title">Nuestra Colección</h1>
          <p v-if="pagination.total" class="catalog-sub">
            Mostrando {{ pagination.from }}–{{ pagination.to }} de {{ pagination.total }} vinos curados.
          </p>
        </div>
      </div>

      <!-- Skeletons -->
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-y-16 gap-x-8">
        <div v-for="n in 6" :key="n" class="product-skeleton"></div>
      </div>

      <!-- Productos -->
      <div v-else-if="productos.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-y-16 gap-x-8">
        <article
          v-for="producto in productos"
          :key="producto.id_producto"
          class="flex flex-col space-y-4 group"
        >
          <div class="relative">
            <RouterLink :to="`/catalogo/${producto.id_producto}`" class="block">
              <div class="aspect-[3/4] bg-[#f5f5dc] overflow-hidden rounded-lg relative p-6 flex items-center justify-center">
                <img v-if="producto.imagen_url"
                     :alt="producto.nombre"
                     :src="producto.imagen_url"
                     class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-700"/>
                <span v-else class="material-symbols-outlined text-7xl opacity-20">wine_bar</span>

                <div v-if="producto.descuento > 0"
                     class="absolute top-4 right-4 bg-red-600 text-white text-[10px] px-2 py-1
                            font-label uppercase tracking-widest rounded-full shadow-lg">
                  -{{ producto.descuento }}%
                </div>

                <div v-if="producto.cantidad <= 0" class="agotado-overlay">
                  <span class="agotado-badge">Agotado</span>
                </div>
              </div>
            </RouterLink>

            <!-- Botón rápido carrito -->
            <button
              v-if="producto.cantidad > 0"
              @click="addToCart(producto)"
              class="cart-quick-btn"
            >
              <span class="material-symbols-outlined">add_shopping_cart</span>
            </button>
          </div>

          <div class="space-y-1 mt-4">
            <RouterLink :to="`/catalogo/${producto.id_producto}`">
              <h3 class="product-name">{{ producto.nombre }}</h3>
            </RouterLink>
            <div class="flex justify-between items-center text-xs text-black/50 font-label uppercase tracking-wider">
              <span>
                {{ producto.marca?.nombre ?? '' }}
                {{ producto.pais ? '· ' + producto.pais : '' }}
                {{ producto.anio_cosecha ? ', ' + producto.anio_cosecha : '' }}
              </span>
              <div class="flex flex-col items-end">
                <template v-if="producto.descuento > 0">
                  <span class="text-[10px] line-through opacity-50">${{ fmt(producto.precio) }}</span>
                  <span class="text-red-600 font-bold">${{ fmt(producto.precio * (1 - producto.descuento / 100)) }}</span>
                </template>
                <span v-else class="text-[#735c00] font-bold">${{ fmt(producto.precio) }}</span>
              </div>
            </div>
            <p v-if="producto.variedades?.length" class="text-[10px] text-black/40 font-label uppercase tracking-widest">
              {{ producto.variedades.map(v => v.nombre).join(' / ') }}
            </p>
          </div>
        </article>
      </div>

      <!-- Vacío -->
      <div v-else class="col-span-3 py-24 text-center">
        <span class="material-symbols-outlined text-6xl opacity-20 block mb-4">wine_bar</span>
        <p class="font-['Noto_Serif'] text-2xl text-[#2a0002]/60 italic">
          No se encontraron vinos con los filtros seleccionados.
        </p>
        <button @click="clearFilters" class="mt-6 text-[#745853] font-label text-sm uppercase tracking-widest hover:text-[#2a0002] transition-colors">
          Ver todos los vinos
        </button>
      </div>

      <!-- ── Paginación ─────────────────────────────────────────────────────── -->
      <div v-if="pagination.last_page > 1" class="pagination-container">
        <div class="pagination-info">
          Mostrando <strong>{{ pagination.from ?? 0 }}</strong> a
          <strong>{{ pagination.to ?? 0 }}</strong> de
          <strong>{{ pagination.total }}</strong> vinos curados
        </div>
        <div class="pagination-controls">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="page-icon"
            :class="pagination.current_page <= 1 ? 'page-disabled' : 'page-link'"
          >
            <span class="material-symbols-outlined">chevron_left</span>
          </button>

          <button
            v-for="page in visiblePages"
            :key="page"
            @click="goToPage(page)"
            :class="page === pagination.current_page ? 'page-current' : 'page-link'"
          >
            {{ page }}
          </button>

          <button
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="page-icon"
            :class="pagination.current_page >= pagination.last_page ? 'page-disabled' : 'page-link'"
          >
            <span class="material-symbols-outlined">chevron_right</span>
          </button>
        </div>
      </div>

    </section>
  </main>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ProductoController } from '@/controllers'
import { useCartStore }         from '@/stores/cart'
import { useNotificationStore } from '@/stores/notifications'

const route      = useRoute()
const router     = useRouter()
const cartStore  = useCartStore()
const notifStore = useNotificationStore()

const productos = ref([])
const meta      = reactive({ categorias: [], marcas: [], variedades: [], paises: [] })
const loading   = ref(true)

const pagination = reactive({
  current_page: 1, last_page: 1, total: 0, from: 0, to: 0
})

const filters = reactive({
  buscar:          route.query.buscar         ?? '',
  categoria:       route.query.categoria      ?? '',
  pais:            route.query.pais           ?? '',
  marca:           route.query.marca          ?? '',
  variedad:        route.query.variedad       ?? '',
  solo_descuentos: route.query.solo_descuentos === '1',
  orden:           route.query.orden          ?? 'newest',
  page:            Number(route.query.page)   || 1,
})

const fmt = (n) => Number(n).toFixed(2)

const hasFilters = computed(() =>
  filters.buscar || filters.categoria || filters.pais ||
  filters.marca  || filters.variedad  || filters.solo_descuentos
)

const visiblePages = computed(() => {
  const cur  = pagination.current_page
  const last = pagination.last_page
  const start = Math.max(1, cur - 2)
  const end   = Math.min(last, cur + 2)
  return Array.from({ length: end - start + 1 }, (_, i) => start + i)
})

async function fetchMeta() {
  const result = await ProductoController.obtenerFormData()

  if (!result.success) {
    console.warn('No se pudieron cargar los filtros del catalogo.', result.errors)
    return
  }

  meta.categorias = result.categorias ?? []
  meta.marcas     = result.marcas ?? []
  meta.variedades = result.variedades ?? []
  meta.paises     = result.paises ?? []
}

async function fetchProductos() {
  loading.value = true
  try {
    const params = {
      buscar:          filters.buscar         || undefined,
      categoria:       filters.categoria      || undefined,
      pais:            filters.pais           || undefined,
      marca:           filters.marca          || undefined,
      variedad:        filters.variedad       || undefined,
      solo_descuentos: filters.solo_descuentos ? 1 : undefined,
      orden:           filters.orden          || undefined,
      page:            filters.page,
    }
    const result = await ProductoController.obtenerProductos(params)

    if (!result.success) {
      throw new Error(result.message)
    }

    productos.value = result.productos ?? []
    pagination.current_page = result.pagination.currentPage
    pagination.last_page    = result.pagination.lastPage
    pagination.total        = result.pagination.total
    pagination.from         = result.pagination.from
    pagination.to           = result.pagination.to
  } catch (e) {
    console.error('Error cargando catálogo', e)
  } finally {
    loading.value = false
  }
}

function applyFilters() {
  filters.page = 1
  syncUrl()
  fetchProductos()
}

function goToPage(page) {
  if (page < 1 || page > pagination.last_page) return
  filters.page = page
  syncUrl()
  fetchProductos()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function toggleVariedad(id) {
  filters.variedad = filters.variedad == id ? '' : id
  applyFilters()
}

function clearFilters() {
  Object.assign(filters, {
    buscar: '', categoria: '', pais: '', marca: '',
    variedad: '', solo_descuentos: false, orden: 'newest', page: 1
  })
  syncUrl()
  fetchProductos()
}

function syncUrl() {
  router.replace({
    query: {
      buscar:          filters.buscar         || undefined,
      categoria:       filters.categoria      || undefined,
      pais:            filters.pais           || undefined,
      marca:           filters.marca          || undefined,
      variedad:        filters.variedad       || undefined,
      solo_descuentos: filters.solo_descuentos ? '1' : undefined,
      orden:           filters.orden !== 'newest' ? filters.orden : undefined,
      page:            filters.page > 1 ? filters.page : undefined,
    }
  })
}

async function addToCart(producto) {
  try {
    const precioFinal = producto.descuento > 0 ? producto.precio * (1 - producto.descuento / 100) : producto.precio
    const data = await cartStore.addItem(producto.id_producto, {
      nombre: producto.nombre,
      precio: precioFinal,
      imagen: producto.imagen_url
    })
    notifStore.show(data.mensaje ?? 'Producto agregado al carrito')
  } catch {
    notifStore.show('Error al agregar al carrito', 'error')
  }
}

onMounted(async () => {
  await fetchMeta()
  await fetchProductos()
})
</script>

<style scoped>
/* Filtros */
.filter-heading {
  font-family: 'Noto Serif', serif;
  font-size: 1.5rem;
  color: #2a0002;
  font-weight: 700;
}

.filter-sub {
  font-size: 0.75rem;
  font-family: 'Manrope', sans-serif;
  text-transform: uppercase;
  letter-spacing: 0.15em;
  color: #745853;
  margin-top: 0.5rem;
  opacity: 0.7;
}

.filter-section-title {
  font-family: 'Noto Serif', serif;
  font-size: 1.125rem;
  font-style: italic;
  border-bottom: 1px solid rgba(218, 193, 191, 0.15);
  padding-bottom: 0.5rem;
  display: flex;
  align-items: center;
}

.filter-icon {
  font-size: 0.875rem !important;
  margin-right: 0.5rem;
  color: #735c00;
}

.filter-input {
  width: 100%;
  background-color: #efefd7;
  color: #1b1d0e;
  border: 1px solid rgba(218, 193, 191, 0.3);
  border-radius: 6px;
  padding: 0.75rem 2.5rem 0.75rem 1rem;
  font-family: 'Manrope', sans-serif;
  font-size: 0.875rem;
  transition: border-color 0.2s;
}

.filter-input:focus { outline: none; border-color: #2a0002; }

.filter-select {
  width: 100%;
  background-color: #efefd7;
  border: 1px solid rgba(218, 193, 191, 0.3);
  border-radius: 6px;
  padding: 0.5rem 0.75rem;
  font-family: 'Manrope', sans-serif;
  font-size: 0.875rem;
  color: #1b1d0e;
  cursor: pointer;
}

.radio-label {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  cursor: pointer;
}

.radio-text {
  font-size: 0.875rem;
  font-weight: 500;
  color: rgba(27, 29, 14, 0.8);
  transition: color 0.2s;
}

.radio-label:hover .radio-text { color: #2a0002; }

.variedad-btn {
  padding: 0.25rem 0.75rem;
  font-size: 10px;
  font-family: 'Manrope', sans-serif;
  border-radius: 9999px;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  cursor: pointer;
  border: none;
  transition: all 0.2s;
}

.variedad-active   { background-color: #735c00; color: white; box-shadow: 0 2px 8px rgba(115,92,0,0.3); }
.variedad-inactive { background-color: #eaead1; color: #1b1d0e; }
.variedad-inactive:hover { background-color: #e4e4cc; }

/* Catálogo */
.catalog-title {
  font-family: 'Noto Serif', serif;
  font-size: 2.25rem;
  color: #2a0002;
  font-weight: 700;
  letter-spacing: -0.02em;
}

.catalog-sub {
  color: rgba(27, 29, 14, 0.6);
  margin-top: 0.5rem;
  font-family: 'Noto Serif', serif;
  font-style: italic;
}

.product-name {
  font-family: 'Noto Serif', serif;
  font-size: 1.25rem;
  color: #2a0002;
  transition: color 0.2s;
}

.group:hover .product-name { color: #735c00; }

.agotado-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.2);
  backdrop-filter: blur(2px);
}

.agotado-badge {
  background: rgba(255, 255, 255, 0.9);
  color: #2a0002;
  padding: 0.5rem 1rem;
  border-radius: 9999px;
  font-family: 'Manrope', sans-serif;
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  font-weight: 700;
  border: 1px solid rgba(42, 0, 2, 0.1);
}

.cart-quick-btn {
  position: absolute;
  bottom: 1rem;
  right: 1rem;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
  padding: 0.75rem;
  border-radius: 9999px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  color: #2a0002;
  border: none;
  cursor: pointer;
  transform: translateY(1rem);
  opacity: 0;
  transition: all 0.3s;
}

.group:hover .cart-quick-btn { transform: translateY(0); opacity: 1; }
.cart-quick-btn:hover { background: #2a0002; color: white; }

/* Skeleton */
.product-skeleton {
  aspect-ratio: 3 / 5;
  background: linear-gradient(90deg, #eaead1 25%, #e4e4cc 50%, #eaead1 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 8px;
}

@keyframes shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>

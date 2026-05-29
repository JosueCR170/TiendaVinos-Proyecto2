<template>
  <AppLayout>
    <main class="flex min-h-screen px-6 md:px-12 pt-12 pb-24 gap-16">
      <!-- Barra lateral de filtros -->
      <aside class="w-72 flex-shrink-0 space-y-10 pt-4">
        <header>
          <h2 class="font-headline text-2xl text-primary font-bold">Filtrar Colección</h2>
          <p class="text-xs font-label uppercase tracking-widest text-secondary mt-2 opacity-70">Refinando la bodega</p>
        </header>

        <form @submit.prevent="aplicarFiltros" id="formFiltros">
          <!-- Búsqueda por nombre -->
          <section class="space-y-4 mb-10">
            <h3 class="font-headline text-lg italic border-b border-outline-variant/15 pb-2 flex items-center">
              <span class="material-symbols-outlined text-sm mr-2 text-tertiary">search</span>
              Buscar Vino
            </h3>
            <div class="relative">
              <input v-model="filtros.buscar" type="text" name="buscar"
                     placeholder="Nombre del vino..."
                     class="w-full bg-surface-container text-sm font-body text-on-surface border border-outline-variant/30 rounded-md px-4 py-3 focus:outline-none focus:border-primary transition-all pr-10">
              <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-primary/40 hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-xl">arrow_forward</span>
              </button>
            </div>
          </section>

          <!-- Categoría -->
          <section class="space-y-4 mb-8">
            <h3 class="font-headline text-lg italic border-b border-outline-variant/15 pb-2 flex items-center">
              <span class="material-symbols-outlined text-sm mr-2 text-tertiary">category</span>
              Categoría
            </h3>
            <div class="flex flex-col space-y-2">
              <label class="flex items-center space-x-3 cursor-pointer group">
                <input v-model="filtros.categoria" type="radio" name="categoria" value="" @change="aplicarFiltros"
                       class="accent-primary">
                <span class="text-sm font-medium text-on-surface/80 group-hover:text-primary">Todas</span>
              </label>
              <template v-for="cat in categorias" :key="cat.id_categoria">
                <label class="flex items-center space-x-3 cursor-pointer group">
                  <input v-model="filtros.categoria" type="radio" name="categoria" :value="cat.id_categoria" @change="aplicarFiltros"
                         class="accent-primary">
                  <span class="text-sm font-medium text-on-surface/80 group-hover:text-primary">{{ cat.nombre }}</span>
                </label>
              </template>
            </div>
          </section>

          <!-- País de origen -->
          <section class="space-y-4 mb-8">
            <h3 class="font-headline text-lg italic border-b border-outline-variant/15 pb-2 flex items-center">
              <span class="material-symbols-outlined text-sm mr-2 text-tertiary">public</span>
              País de Origen
            </h3>
            <div class="flex flex-col space-y-2 max-h-48 overflow-y-auto pr-2">
              <label class="flex items-center space-x-3 cursor-pointer group">
                <input v-model="filtros.pais" type="radio" name="pais" value="" @change="aplicarFiltros"
                       class="accent-primary">
                <span class="text-sm font-medium text-on-surface/80 group-hover:text-primary">Todos</span>
              </label>
              <template v-for="pais in paises" :key="pais">
                <label class="flex items-center space-x-3 cursor-pointer group">
                  <input v-model="filtros.pais" type="radio" name="pais" :value="pais" @change="aplicarFiltros"
                         class="accent-primary">
                  <span class="text-sm font-medium text-on-surface/80 group-hover:text-primary">{{ pais }}</span>
                </label>
              </template>
            </div>
          </section>

          <!-- Bodega / Marca -->
          <section class="space-y-4 mb-8">
            <h3 class="font-headline text-lg italic border-b border-outline-variant/15 pb-2 flex items-center">
              <span class="material-symbols-outlined text-sm mr-2 text-tertiary">storefront</span>
              Bodega
            </h3>
            <div class="flex flex-col space-y-2 max-h-48 overflow-y-auto pr-2">
              <label class="flex items-center space-x-3 cursor-pointer group">
                <input v-model="filtros.marca" type="radio" name="marca" value="" @change="aplicarFiltros"
                       class="accent-primary">
                <span class="text-sm font-medium text-on-surface/80 group-hover:text-primary">Todas</span>
              </label>
              <template v-for="marca in marcas" :key="marca.id_marca">
                <label class="flex items-center space-x-3 cursor-pointer group">
                  <input v-model="filtros.marca" type="radio" name="marca" :value="marca.id_marca" @change="aplicarFiltros"
                         class="accent-primary">
                  <span class="text-sm font-medium text-on-surface/80 group-hover:text-primary">{{ marca.nombre }}</span>
                </label>
              </template>
            </div>
          </section>

          <!-- Cepa / Variedad -->
          <section class="space-y-4 mb-8">
            <h3 class="font-headline text-lg italic border-b border-outline-variant/15 pb-2 flex items-center">
              <span class="material-symbols-outlined text-sm mr-2 text-tertiary">wine_bar</span>
              Cepa / Variedad
            </h3>
            <div class="flex flex-wrap gap-2">
              <template v-for="variedad in variedades" :key="variedad.id_variedad">
                <button @click="filtros.variedad = variedad.id_variedad; aplicarFiltros()" type="button"
                    :class="['px-3 py-1 text-[10px] font-label rounded-full uppercase tracking-widest cursor-pointer transition-colors',
                      filtros.variedad == variedad.id_variedad
                        ? 'bg-tertiary text-white shadow-md'
                        : 'bg-surface-container-high text-on-surface hover:bg-surface-container-highest']">
                    {{ variedad.nombre }}
                </button>
              </template>
            </div>
          </section>

          <!-- Ofertas -->
          <section class="space-y-4 mb-8">
            <h3 class="font-headline text-lg italic border-b border-outline-variant/15 pb-2 flex items-center">
              <span class="material-symbols-outlined text-sm mr-2 text-tertiary">sell</span>
              Ofertas
            </h3>
            <label class="flex items-center space-x-3 cursor-pointer group">
              <input v-model="filtros.solo_descuentos" type="checkbox" name="solo_descuentos" value="1" @change="aplicarFiltros"
                     class="accent-primary rounded">
              <span class="text-sm font-medium text-on-surface/80 group-hover:text-primary font-bold text-red-600 italic">Solo con Descuento</span>
            </label>
          </section>

          <!-- Ordenar -->
          <section class="space-y-4 mb-8">
            <h3 class="font-headline text-lg italic border-b border-outline-variant/15 pb-2 flex items-center">
              <span class="material-symbols-outlined text-sm mr-2 text-tertiary">sort</span>
              Ordenar por
            </h3>
            <select v-model="filtros.orden" @change="aplicarFiltros"
                    class="w-full bg-surface-container text-sm font-body text-on-surface border border-outline-variant/30 rounded-md px-3 py-2 focus:outline-none focus:border-tertiary">
              <option value="newest">Más recientes</option>
              <option value="precio_asc">Precio: Menor a Mayor</option>
              <option value="precio_desc">Precio: Mayor a Menor</option>
              <option value="nombre">Nombre A-Z</option>
            </select>
          </section>

          <!-- Limpiar filtros -->
          <template v-if="hasFilters">
            <button @click="limpiarFiltros" type="button" class="inline-flex items-center gap-2 text-xs font-label uppercase tracking-widest text-secondary hover:text-primary transition-colors">
              <span class="material-symbols-outlined text-sm">close</span> Limpiar filtros
            </button>
          </template>
        </form>
      </aside>

      <!-- Grilla de productos -->
      <section class="flex-grow">
        <div class="flex justify-between items-end mb-12">
          <div>
            <h1 class="font-headline text-4xl text-primary font-bold tracking-tight">Nuestra Colección</h1>
            <p class="text-on-surface/60 mt-2 font-headline italic">
              Mostrando {{ (currentPage - 1) * itemsPerPage + 1 }} - {{ Math.min(currentPage * itemsPerPage, totalProductos) }} de {{ totalProductos }} vinos curados.
            </p>
          </div>
        </div>

        <!-- Grilla -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-y-16 gap-x-8">
          <template v-if="productos.length > 0">
            <article v-for="producto in productos" :key="producto.id_producto" class="flex flex-col space-y-4 group">
              <div class="relative">
                <router-link :to="`/producto/${producto.id_producto}`" class="block">
                  <div class="aspect-[3/4] bg-surface-container-low overflow-hidden rounded-lg relative p-6 flex items-center justify-center">
                    <template v-if="producto.imagen_url">
                      <img :alt="producto.nombre"
                           class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-700"
                           :src="producto.imagen_url"/>
                    </template>
                    <template v-else>
                      <div class="w-full h-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-7xl text-outline-variant/30">wine_bar</span>
                      </div>
                    </template>

                    <template v-if="producto.descuento > 0">
                      <div class="absolute top-4 right-4 bg-red-600 text-white text-[10px] px-2 py-1 font-label uppercase tracking-widest rounded-full shadow-lg">
                        -{{ producto.descuento }}%
                      </div>
                    </template>

                    <template v-if="producto.cantidad <= 0">
                      <div class="absolute inset-0 flex items-center justify-center bg-black/20 backdrop-blur-[2px]">
                        <span class="bg-white/90 text-primary px-4 py-2 rounded-full font-label text-[10px] uppercase tracking-[0.2em] font-bold shadow-xl border border-primary/10">Agotado</span>
                      </div>
                    </template>
                  </div>
                </router-link>
                
                <!-- Botón rápido agregar carrito -->
                <template v-if="producto.cantidad > 0">
                  <button @click="agregarAlCarrito(producto.id_producto)"
                          class="absolute bottom-4 right-4 bg-white/90 backdrop-blur-md p-3 rounded-full shadow-lg text-primary hover:bg-primary hover:text-white transition-all duration-300 transform translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100">
                    <span class="material-symbols-outlined">add_shopping_cart</span>
                  </button>
                </template>
              </div>

              <div class="space-y-1 mt-4">
                <router-link :to="`/producto/${producto.id_producto}`" class="group">
                  <h3 class="font-headline text-xl text-primary group-hover:text-tertiary transition-colors">{{ producto.nombre }}</h3>
                </router-link>
                <div class="flex justify-between items-center text-xs text-on-surface/50 font-label uppercase tracking-wider">
                  <span>
                    {{ producto.marca?.nombre || '' }}
                    {{ producto.pais ? '· ' + producto.pais : '' }}
                    {{ producto.anio_cosecha ? ', ' + producto.anio_cosecha : '' }}
                  </span>
                  <div class="flex flex-col items-end">
                    <template v-if="producto.descuento > 0">
                      <span class="text-[10px] line-through opacity-50">${{ formatPrice(producto.precio) }}</span>
                      <span class="text-red-600 font-bold">${{ formatPrice(producto.precio * (1 - producto.descuento/100)) }}</span>
                    </template>
                    <template v-else>
                      <span class="text-tertiary font-bold">${{ formatPrice(producto.precio) }}</span>
                    </template>
                  </div>
                </div>
                <template v-if="producto.variedades && producto.variedades.length > 0">
                  <p class="text-[10px] text-on-surface/40 font-label uppercase tracking-widest">
                    {{ producto.variedades.map(v => v.nombre).join(' / ') }}
                  </p>
                </template>
              </div>
            </article>
          </template>
          <template v-else>
            <div class="col-span-3 py-24 text-center">
              <span class="material-symbols-outlined text-6xl text-outline-variant/30 block mb-4">wine_bar</span>
              <p class="font-headline text-2xl text-primary/60 italic">No se encontraron vinos con los filtros seleccionados.</p>
              <router-link to="/catalogo" class="mt-6 inline-block text-secondary font-label text-sm uppercase tracking-widest hover:text-primary transition-colors">
                Ver todos los vinos
              </router-link>
            </div>
          </template>
        </div>

        <!-- Paginación -->
        <template v-if="totalPages > 1">
          <div class="pagination-container mt-16">
            <div class="pagination-info">
              Mostrando <strong>{{ (currentPage - 1) * itemsPerPage + 1 }}</strong> a <strong>{{ Math.min(currentPage * itemsPerPage, totalProductos) }}</strong> de <strong>{{ totalProductos }}</strong> vinos curados
            </div>
            <div class="pagination-controls">
              <button @click="currentPage--" v-if="currentPage > 1" class="page-link page-icon">
                <span class="material-symbols-outlined">chevron_left</span>
              </button>
              <span v-else class="page-disabled page-icon">
                <span class="material-symbols-outlined">chevron_left</span>
              </span>

              <template v-for="page in pagesToShow" :key="page">
                <button v-if="page === currentPage" class="page-current">{{ page }}</button>
                <button v-else @click="currentPage = page" class="page-link">{{ page }}</button>
              </template>

              <button @click="currentPage++" v-if="currentPage < totalPages" class="page-link page-icon">
                <span class="material-symbols-outlined">chevron_right</span>
              </button>
              <span v-else class="page-disabled page-icon">
                <span class="material-symbols-outlined">chevron_right</span>
              </span>
            </div>
          </div>
        </template>
      </section>
    </main>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'

const route = useRoute()
const productos = ref([])
const categorias = ref([])
const paises = ref([])
const marcas = ref([])
const variedades = ref([])
const totalProductos = ref(0)
const currentPage = ref(1)
const itemsPerPage = 12

const filtros = ref({
  buscar: route.query.buscar || '',
  categoria: route.query.categoria || '',
  pais: route.query.pais || '',
  marca: route.query.marca || '',
  variedad: route.query.variedad || '',
  solo_descuentos: route.query.solo_descuentos ? '1' : '',
  orden: route.query.orden || 'newest'
})

const hasFilters = computed(() => {
  return Object.values(filtros.value).some(v => v !== '' && v !== '0')
})

const totalPages = computed(() => Math.ceil(totalProductos.value / itemsPerPage))

const pagesToShow = computed(() => {
  const pages = []
  const start = Math.max(1, currentPage.value - 2)
  const end = Math.min(totalPages.value, currentPage.value + 2)
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

const formatPrice = (price) => parseFloat(price).toFixed(2)

const cargarProductos = async () => {
  try {
    const params = new URLSearchParams()
    Object.entries(filtros.value).forEach(([key, value]) => {
      if (value) params.append(key, value)
    })
    params.append('page', currentPage.value)
    params.append('limit', itemsPerPage)

    const response = await fetch(`/api/productos?${params}`)
    const data = await response.json()
    productos.value = data.productos || []
    totalProductos.value = data.total || 0
  } catch (error) {
    console.error('Error loading products:', error)
  }
}

const cargarFiltros = async () => {
  try {
    const response = await fetch('/api/filtros')
    const data = await response.json()
    categorias.value = data.categorias || []
    paises.value = data.paises || []
    marcas.value = data.marcas || []
    variedades.value = data.variedades || []
  } catch (error) {
    console.error('Error loading filters:', error)
  }
}

const aplicarFiltros = () => {
  currentPage.value = 1
  cargarProductos()
}

const limpiarFiltros = () => {
  filtros.value = {
    buscar: '',
    categoria: '',
    pais: '',
    marca: '',
    variedad: '',
    solo_descuentos: '',
    orden: 'newest'
  }
  currentPage.value = 1
  cargarProductos()
}

const agregarAlCarrito = async (id) => {
  try {
    const response = await fetch(`/api/carrito/agregar/${id}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' }
    })
    const data = await response.json()
    if (data.success) {
      window.showNotification(data.mensaje || 'Producto agregado correctamente')
      window.dispatchEvent(new Event('cart-updated'))
    }
  } catch (error) {
    console.error('Error:', error)
    window.showNotification('Error al agregar al carrito', 'error')
  }
}

onMounted(() => {
  cargarFiltros()
  cargarProductos()
})
</script>

<style scoped>
.pagination-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 2rem 0;
  border-top: 1px solid rgba(218, 193, 191, 0.3);
}

.pagination-info {
  font-size: 0.875rem;
  color: #1b1d0e;
  font-family: Manrope;
}

.pagination-controls {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.page-link, .page-current, .page-disabled {
  padding: 0.5rem 0.75rem;
  border-radius: 0.375rem;
  font-family: Manrope;
  cursor: pointer;
  transition: all 0.3s;
}

.page-link {
  background-color: #f5f5dc;
  color: #1b1d0e;
  border: 1px solid #dac1bf;
}

.page-link:hover {
  background-color: #e4e4cc;
  color: #2a0002;
}

.page-current {
  background-color: #2a0002;
  color: #fbfbe2;
  font-weight: bold;
}

.page-disabled {
  color: #dac1bf;
  cursor: not-allowed;
}

.page-icon {
  padding: 0.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>

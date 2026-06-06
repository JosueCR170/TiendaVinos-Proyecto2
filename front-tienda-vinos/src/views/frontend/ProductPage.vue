<template>
    <main class="max-w-screen-2xl mx-auto px-6 md:px-12 pt-12 pb-24">
      <!-- Migas de pan -->
      <nav class="mb-8 text-xs font-label uppercase tracking-widest text-on-surface/50">
        <router-link to="/" class="hover:text-primary transition-colors">Inicio</router-link>
        <span class="mx-2">/</span>
        <router-link to="/catalogo" class="hover:text-primary transition-colors">Catálogo</router-link>
        <span class="mx-2">/</span>
        <span class="text-primary">{{ producto.nombre }}</span>
      </nav>

      <!-- Asymmetric Product Layout -->
      <template v-if="producto.id_producto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">
          <!-- Left: Image -->
          <div class="lg:col-span-5 relative">
            <div class="bg-surface-container-low rounded-lg p-12 lg:sticky lg:top-32 shadow-[0_20px_40px_-10px_rgba(27,29,14,0.06)] aspect-[3/4] flex flex-col items-center justify-between">
              <div class="w-full flex-grow flex items-center justify-center">
                <template v-if="producto.imagen_url">
                  <img :alt="producto.nombre" class="max-w-full max-h-full object-contain transform hover:scale-105 transition-transform duration-700" :src="producto.imagen_url"/>
                </template>
                <template v-else>
                  <div class="flex items-center justify-center">
                    <span class="material-symbols-outlined text-9xl text-outline-variant/30">wine_bar</span>
                  </div>
                </template>
              </div>
              <div class="w-full pt-6 border-t border-outline-variant/20 flex justify-center">
                <router-link
                  :to="{ name: 'admin.productos.index', query: { search: producto.nombre } }"
                  class="flex items-center gap-2 font-label text-xs uppercase tracking-widest text-[#735c00] hover:text-primary transition-colors font-bold"
                >
                  <span class="material-symbols-outlined text-base">edit</span>
                  Editar producto en Admin
                </router-link>
              </div>
            </div>
          </div>

          <!-- Right: Content -->
          <div class="lg:col-span-7 flex flex-col space-y-12">
            <!-- Header -->
            <section class="space-y-4">
              <div class="flex items-center flex-wrap gap-3">
                <template v-if="producto.categoria">
                  <span class="bg-tertiary text-on-tertiary px-4 py-1 rounded-full font-label text-[0.75rem] uppercase tracking-widest">
                    {{ producto.categoria.nombre }}
                  </span>
                </template>
                <template v-if="producto.pais">
                  <span class="text-on-surface-variant font-label text-[0.75rem] uppercase tracking-widest">
                    {{ producto.pais }}{{ producto.region ? ', ' + producto.region : '' }}
                  </span>
                </template>
              </div>
              <h1 class="font-headline text-5xl md:text-6xl font-bold text-primary tracking-tighter leading-tight italic">
                {{ producto.nombre }}
              </h1>
              <template v-if="producto.marca">
                <p class="font-headline text-2xl text-secondary italic opacity-80">{{ producto.marca.nombre }}</p>
              </template>
              <template v-if="producto.variedades && producto.variedades.length > 0">
                <p class="font-label text-sm text-tertiary uppercase tracking-widest">
                  {{ producto.variedades.map(v => v.nombre).join(' / ') }}
                </p>
              </template>
            </section>

            <!-- Technical Specs -->
            <section class="grid grid-cols-2 sm:grid-cols-4 gap-4">
              <template v-if="producto.anio_cosecha">
                <div class="bg-surface-container p-6 rounded-lg flex flex-col justify-center items-center">
                  <span class="font-label text-[0.65rem] uppercase tracking-widest text-on-surface-variant">Año de Cosecha</span>
                  <span class="font-headline text-xl font-bold text-primary">{{ producto.anio_cosecha }}</span>
                </div>
              </template>
              <template v-if="producto.alcohol_porcentaje">
                <div class="bg-surface-container p-6 rounded-lg flex flex-col justify-center items-center">
                  <span class="font-label text-[0.65rem] uppercase tracking-widest text-on-surface-variant">Alcohol</span>
                  <span class="font-headline text-xl font-bold text-primary">{{ producto.alcohol_porcentaje }}%</span>
                </div>
              </template>
              <template v-if="producto.contenido_ml">
                <div class="bg-surface-container p-6 rounded-lg flex flex-col justify-center items-center">
                  <span class="font-label text-[0.65rem] uppercase tracking-widest text-on-surface-variant">Contenido</span>
                  <span class="font-headline text-xl font-bold text-primary">{{ producto.contenido_ml }}ml</span>
                </div>
              </template>
              <div class="bg-surface-container p-6 rounded-lg flex flex-col justify-center items-center">
                <span class="font-label text-[0.65rem] uppercase tracking-widest text-on-surface-variant">Existencias</span>
                <span class="font-headline text-xl font-bold text-primary">{{ producto.cantidad }}</span>
              </div>
            </section>

            <!-- Pricing & CTA -->
            <section class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-8 bg-surface-container-highest rounded-xl border border-outline-variant/10">
              <div class="mb-6 sm:mb-0">
                <span class="font-label text-[0.75rem] uppercase text-on-surface-variant">Precio de Venta</span>
                <template v-if="producto.descuento > 0">
                  <p class="text-lg font-body line-through text-on-surface/40">${{ formatPrice(producto.precio) }}</p>
                  <p class="text-4xl font-headline font-bold text-primary">
                    ${{ formatPrice(producto.precio * (1 - producto.descuento / 100)) }}
                    <span class="text-base text-tertiary font-label ml-2">-{{ producto.descuento }}%</span>
                  </p>
                </template>
                <template v-else>
                  <p class="text-4xl font-headline font-bold text-primary">${{ formatPrice(producto.precio) }}</p>
                </template>
              </div>
              <template v-if="producto.cantidad > 0">
                <button @click="agregarAlCarrito(producto.id_producto)"
                        class="w-full sm:w-auto bg-[#2a0002] text-white px-8 py-4 rounded-md font-label text-sm font-bold uppercase tracking-widest hover:bg-[#3d0003] active:scale-95 transition-all duration-300 flex items-center justify-center space-x-3 shadow-lg">
                  <span class="material-symbols-outlined">add_shopping_cart</span>
                  <span>Agregar al Carrito</span>
                </button>
              </template>
              <template v-else>
                <div class="w-full sm:w-auto bg-stone-100 text-stone-400 px-8 py-4 rounded-md font-label text-sm font-bold uppercase tracking-widest flex items-center justify-center space-x-3 cursor-not-allowed border border-stone-200">
                  <span class="material-symbols-outlined">block</span>
                  <span>Producto Agotado</span>
                </div>
              </template>
            </section>

            <!-- Description -->
            <template v-if="producto.descripcion">
              <section class="space-y-4">
                <h3 class="font-headline text-2xl font-bold text-primary italic">Descripción del Vino</h3>
                <p class="font-body text-on-surface-variant leading-relaxed text-lg">{{ producto.descripcion }}</p>
              </section>
            </template>

            <!-- Details Block -->
            <section class="relative p-12 bg-surface-container-low rounded-lg overflow-hidden">
              <div class="absolute top-0 right-0 p-8 opacity-10">
                <span class="material-symbols-outlined text-9xl">wine_bar</span>
              </div>
              <div class="relative z-10">
                <h3 class="font-headline text-2xl font-bold text-primary mb-6">Datos Técnicos</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <template v-if="producto.pais">
                    <div class="flex items-start gap-3 border-b border-outline-variant/20 pb-3">
                      <span class="material-symbols-outlined text-tertiary text-sm mt-0.5">public</span>
                      <div>
                        <dt class="font-label text-[0.65rem] uppercase tracking-widest text-on-surface-variant">País</dt>
                        <dd class="font-body font-medium text-on-surface">{{ producto.pais }}</dd>
                      </div>
                    </div>
                  </template>
                  <template v-if="producto.region">
                    <div class="flex items-start gap-3 border-b border-outline-variant/20 pb-3">
                      <span class="material-symbols-outlined text-tertiary text-sm mt-0.5">location_on</span>
                      <div>
                        <dt class="font-label text-[0.65rem] uppercase tracking-widest text-on-surface-variant">Región</dt>
                        <dd class="font-body font-medium text-on-surface">{{ producto.region }}</dd>
                      </div>
                    </div>
                  </template>
                  <template v-if="producto.marca">
                    <div class="flex items-start gap-3 border-b border-outline-variant/20 pb-3">
                      <span class="material-symbols-outlined text-tertiary text-sm mt-0.5">storefront</span>
                      <div>
                        <dt class="font-label text-[0.65rem] uppercase tracking-widest text-on-surface-variant">Bodega</dt>
                        <dd class="font-body font-medium text-on-surface">{{ producto.marca.nombre }}</dd>
                      </div>
                    </div>
                  </template>
                  <template v-if="producto.categoria">
                    <div class="flex items-start gap-3 border-b border-outline-variant/20 pb-3">
                      <span class="material-symbols-outlined text-tertiary text-sm mt-0.5">category</span>
                      <div>
                        <dt class="font-label text-[0.65rem] uppercase tracking-widest text-on-surface-variant">Categoría</dt>
                        <dd class="font-body font-medium text-on-surface">{{ producto.categoria.nombre }}</dd>
                      </div>
                    </div>
                  </template>
                </dl>
              </div>
            </section>
          </div>
        </div>

        <!-- Related Products -->
        <template v-if="relacionados.length > 0">
          <section class="bg-surface-container-low py-24 px-6 md:px-12 mt-24">
            <div class="max-w-screen-2xl mx-auto">
              <h2 class="font-headline text-4xl text-primary font-bold mb-12 italic">Vinos del Mismo Estilo</h2>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <router-link v-for="rel in relacionados" :key="rel.id_producto" :to="`/producto/${rel.id_producto}`" class="group cursor-pointer block">
                  <div class="aspect-[3/4] bg-surface-container-low rounded-lg mb-6 overflow-hidden flex items-center justify-center p-6 transition-all duration-500 group-hover:-translate-y-2">
                    <template v-if="rel.imagen_url">
                      <img :alt="rel.nombre" class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-700" :src="rel.imagen_url"/>
                    </template>
                    <template v-else>
                      <span class="material-symbols-outlined text-7xl text-outline-variant/30">wine_bar</span>
                    </template>
                  </div>
                  <h4 class="font-headline text-xl font-bold text-primary group-hover:text-tertiary transition-colors">{{ rel.nombre }}</h4>
                  <p class="font-body text-on-surface-variant text-sm">
                    {{ rel.marca?.nombre || '' }}{{ rel.pais ? ' | ' + rel.pais : '' }}
                  </p>
                  <p class="font-label text-tertiary mt-2 font-bold">${{ formatPrice(rel.precio) }}</p>
                </router-link>
              </div>
            </div>
          </section>
        </template>
      </template>
      <template v-else>
        <div class="text-center py-16">
          <span class="material-symbols-outlined text-6xl text-outline-variant/30">wine_bar</span>
          <p class="text-2xl text-on-surface-variant mt-4">Producto no encontrado</p>
        </div>
      </template>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { ProductoController } from '@/controllers'
import { useCartStore } from '@/stores/cart'
import { useNotificationStore } from '@/stores/notifications'

const route = useRoute()
const cart = useCartStore()
const notif = useNotificationStore()
const producto = ref({})
const relacionados = ref([])

const formatPrice = (price) => parseFloat(price).toFixed(2)

function agregarAlCarrito(idProducto) {
  try {
    const p = producto.value
    const result = cart.addItem(idProducto, {
      nombre: p.nombre,
      precio: p.descuento > 0
        ? p.precio * (1 - p.descuento / 100)
        : p.precio,
      imagen: p.imagen_url,
    })
    notif.show(result.mensaje || 'Producto agregado al carrito', 'success')
    window.dispatchEvent(new Event('cart-updated'))
  } catch (err) {
    console.error('Error al agregar al carrito:', err)
    notif.show('Error al agregar al carrito', 'error')
  }
}

onMounted(async () => {
  try {
    const id = route.params.id
    const result = await ProductoController.obtenerProductoPorId(id)

    if (!result.success) {
      throw new Error(result.message)
    }

    producto.value = result.producto

    if (result.producto.id_categoria) {
      const relatedResult = await ProductoController.obtenerProductos({
        id_categoria: result.producto.id_categoria,
        per_page: 4,
      })

      relacionados.value = (relatedResult.productos ?? [])
        .filter((item) => item.id_producto !== result.producto.id_producto)
        .slice(0, 3)
    }
  } catch (error) {
    console.error('Error loading product:', error)
  }
})
</script>

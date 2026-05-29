<template>
  <AppLayout>
    <main class="pt-32">
      <!-- Hero Section -->
      <section class="relative px-12 py-24 max-w-screen-2xl mx-auto overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
          <div class="lg:col-span-6 z-10">
            <h1 class="text-display lg:text-[4rem] leading-tight font-serif text-primary tracking-tight mb-8">
              El Sommelier Digital: <span class="italic block">Excelencia Curada para el Paladar Paciente</span>
            </h1>
            <p class="text-body font-body text-on-surface-variant text-lg max-w-md mb-10 leading-relaxed">
              Un archivo curado de viticultura, donde el tiempo es el ingrediente principal y cada botella cuenta la historia de su terruño.
            </p>
            <router-link to="/catalogo" class="bg-[#2a0002] text-white px-8 py-4 rounded-md font-label uppercase tracking-widest text-sm hover:bg-[#3d0003] transition-all inline-block shadow-lg">
              Explorar la Bodega
            </router-link>
          </div>
          <div class="lg:col-span-6 relative">
            <div class="aspect-[3/4] bg-surface-container-low rounded-lg overflow-hidden shadow-xl transform lg:translate-x-12 lg:rotate-2 flex items-center justify-center p-6">
              <template v-if="productosDestacados.length > 0 && productosDestacados[0].imagen_url">
                <img :alt="productosDestacados[0].nombre" class="max-w-full max-h-full object-contain grayscale hover:grayscale-0 transition-all duration-1000" :src="productosDestacados[0].imagen_url"/>
              </template>
              <template v-else>
                <div class="w-full h-full flex items-center justify-center">
                  <span class="material-symbols-outlined text-7xl text-outline-variant/30">wine_bar</span>
                </div>
              </template>
            </div>
            <template v-if="productosDestacados.length > 1">
              <div class="absolute -bottom-10 -left-10 w-64 h-80 hidden lg:block bg-surface-container-low p-6 rounded-md shadow-lg transform -rotate-6 overflow-hidden flex items-center justify-center">
                <template v-if="productosDestacados[1].imagen_url">
                  <img :alt="productosDestacados[1].nombre" class="max-w-full max-h-full object-contain" :src="productosDestacados[1].imagen_url"/>
                </template>
                <template v-else>
                  <div class="w-full h-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-5xl text-outline-variant/30">wine_bar</span>
                  </div>
                </template>
              </div>
            </template>
          </div>
        </div>
      </section>

      <!-- Productos Destacados -->
      <section class="bg-surface-container-low py-24 px-12">
        <div class="max-w-screen-2xl mx-auto">
          <div class="mb-32">
            <div class="flex justify-between items-end mb-12">
              <div>
                <span class="font-label text-tertiary uppercase tracking-[0.2em] text-xs font-bold mb-4 block">Nuestra Selección</span>
                <h2 class="text-headline text-4xl text-primary">Vinos Destacados</h2>
              </div>
              <router-link class="text-secondary font-label border-b border-outline-variant/30 pb-1 hover:text-primary transition-colors" to="/catalogo">Ver Todos</router-link>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
              <template v-if="productosDestacados.length > 0">
                <article v-for="(producto, index) in productosDestacados" :key="producto.id_producto" class="group bg-surface p-8 rounded-md transition-all hover:shadow-2xl hover:-translate-y-2 block" :class="{ 'lg:translate-y-12': index === 1 }">
                  <router-link :to="`/producto/${producto.id_producto}`" class="block">
                    <div class="aspect-[3/4] mb-8 overflow-hidden rounded-lg bg-surface-container-low relative p-6 flex items-center justify-center">
                      <template v-if="producto.imagen_url">
                        <img :alt="producto.nombre" class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-700" :class="{ 'opacity-50 grayscale': producto.cantidad <= 0 }" :src="producto.imagen_url"/>
                      </template>
                      <template v-else>
                        <div class="w-full h-full flex items-center justify-center">
                          <span class="material-symbols-outlined text-7xl text-outline-variant/30">wine_bar</span>
                        </div>
                      </template>
                      
                      <template v-if="producto.cantidad <= 0">
                        <div class="absolute inset-0 flex items-center justify-center bg-black/20 backdrop-blur-[2px]">
                          <span class="bg-white/90 text-primary px-4 py-2 rounded-full font-label text-[10px] uppercase tracking-[0.2em] font-bold shadow-xl border border-primary/10">Agotado</span>
                        </div>
                      </template>
                    </div>
                    <span class="text-xs font-label text-stone-500 uppercase tracking-widest">
                      {{ producto.pais }}{{ producto.region ? ', ' + producto.region : '' }}{{ producto.anio_cosecha ? ', ' + producto.anio_cosecha : '' }}
                    </span>
                    <h3 class="text-xl serif text-primary mt-2 group-hover:text-tertiary transition-colors">{{ producto.nombre }}</h3>
                  </router-link>
                  <div class="mt-4 flex justify-between items-center">
                    <span class="text-lg font-body text-secondary">${{ formatPrice(producto.precio) }}</span>
                    <template v-if="producto.cantidad > 0">
                      <button @click="agregarAlCarrito(producto.id_producto)" class="p-2 rounded-full hover:bg-surface-container-highest transition-colors text-primary active:scale-90">
                        <span class="material-symbols-outlined">add_shopping_cart</span>
                      </button>
                    </template>
                  </div>
                </article>
              </template>
              <template v-else>
                <div class="col-span-3 text-center py-16 text-on-surface-variant font-body italic">
                  No hay productos disponibles en este momento.
                </div>
              </template>
            </div>
          </div>

          <!-- Ofertas Imperdibles -->
          <template v-if="productosDescuento.length > 0">
            <div class="mt-32 mb-32">
              <div class="flex justify-between items-end mb-12">
                <div>
                  <span class="font-label text-tertiary uppercase tracking-[0.2em] text-xs font-bold mb-4 block">Oportunidades Únicas</span>
                  <h2 class="text-headline text-4xl text-primary">Ofertas Imperdibles</h2>
                </div>
                <router-link class="text-secondary font-label border-b border-outline-variant/30 pb-1 hover:text-primary transition-colors" to="/catalogo?solo_descuentos=1">Ver Todas las Ofertas</router-link>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                <article v-for="producto in productosDescuento" :key="producto.id_producto" class="group bg-surface p-8 rounded-md transition-all hover:shadow-2xl hover:-translate-y-2 block">
                  <router-link :to="`/producto/${producto.id_producto}`" class="block">
                    <div class="aspect-[3/4] mb-8 overflow-hidden rounded-lg bg-surface-container-low relative p-6 flex items-center justify-center">
                      <template v-if="producto.imagen_url">
                        <img :alt="producto.nombre" class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-700" :class="{ 'opacity-50 grayscale': producto.cantidad <= 0 }" :src="producto.imagen_url"/>
                      </template>
                      <template v-else>
                        <div class="w-full h-full flex items-center justify-center">
                          <span class="material-symbols-outlined text-7xl text-outline-variant/30">wine_bar</span>
                        </div>
                      </template>
                      <div class="absolute top-4 right-4 bg-red-600 text-white text-[10px] px-3 py-1 font-label uppercase tracking-widest rounded-full shadow-lg">
                        -{{ producto.descuento }}%
                      </div>

                      <template v-if="producto.cantidad <= 0">
                        <div class="absolute inset-0 flex items-center justify-center bg-black/20 backdrop-blur-[2px]">
                          <span class="bg-white/90 text-primary px-4 py-2 rounded-full font-label text-[10px] uppercase tracking-[0.2em] font-bold shadow-xl border border-primary/10">Agotado</span>
                        </div>
                      </template>
                    </div>
                    <span class="text-xs font-label text-stone-500 uppercase tracking-widest">
                      {{ producto.pais }}{{ producto.region ? ', ' + producto.region : '' }}
                    </span>
                    <h3 class="text-xl serif text-primary mt-2 group-hover:text-tertiary transition-colors">{{ producto.nombre }}</h3>
                  </router-link>
                  <div class="mt-4 flex justify-between items-center">
                    <div>
                      <span class="text-sm line-through text-on-surface/40">${{ formatPrice(producto.precio) }}</span>
                      <span class="text-lg font-bold text-red-600 ml-2">${{ formatPrice(producto.precio * (1 - producto.descuento/100)) }}</span>
                    </div>
                    <template v-if="producto.cantidad > 0">
                      <button @click="agregarAlCarrito(producto.id_producto)" class="p-2 rounded-full hover:bg-surface-container-highest transition-colors text-primary active:scale-90">
                        <span class="material-symbols-outlined">add_shopping_cart</span>
                      </button>
                    </template>
                  </div>
                </article>
              </div>
            </div>
          </template>

          <!-- Ediciones Especiales -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="bg-[#2a0002] p-12 lg:p-24 rounded-lg text-white">
              <span class="font-label text-[#e4e4cc] uppercase tracking-[0.3em] text-xs font-bold mb-6 block opacity-80">Ediciones Especiales</span>
              <h2 class="text-display text-4xl mb-8 leading-tight text-white">Espumosos y Antigüedades</h2>
              <p class="font-body text-lg text-[#e4e4cc] leading-relaxed mb-10 opacity-90">
                Accede a nuestra reserva privada de champañas añejos y magnums de producción limitada, obtenidos directamente de las bóvedas más antiguas de la bodega.
              </p>
              <router-link to="/catalogo" class="mt-12 w-full py-4 border border-[#e4e4cc] text-[#e4e4cc] font-label uppercase tracking-widest hover:bg-[#e4e4cc] hover:text-[#2a0002] transition-all block text-center">
                Explorar el Catálogo
              </router-link>
            </div>
            <div class="grid grid-cols-2 gap-6">
              <template v-for="(p, idx) in productosDestacados.slice(0, 2)" :key="p.id_producto">
                <div class="rounded-md aspect-[3/4] bg-surface-container-low p-6 flex items-center justify-center" :class="{ 'translate-y-12': idx === 1 }">
                  <template v-if="p.imagen_url">
                    <img :alt="p.nombre" class="max-w-full max-h-full object-contain" :src="p.imagen_url"/>
                  </template>
                  <template v-else>
                    <span class="material-symbols-outlined text-7xl text-outline-variant/30">wine_bar</span>
                  </template>
                </div>
              </template>
            </div>
          </div>
        </div>
      </section>

      <!-- Nuestra Historia -->
      <section class="py-24 px-12 text-center bg-surface">
        <div class="max-w-2xl mx-auto border-t border-b border-outline-variant/30 py-16">
          <h2 class="text-headline text-3xl text-primary mb-6">Un Legado en Cada Copa</h2>
          <p class="font-body text-on-surface-variant leading-loose mb-10">
            Fundados en la creencia de que una gran bodega es una biblioteca viva del tiempo, hemos pasado décadas cultivando relaciones con productores artesanales que priorizan el terruño por encima de la escala.
          </p>
          <router-link class="inline-flex items-center gap-4 group" to="/about">
            <span class="font-label uppercase tracking-widest text-sm text-secondary group-hover:text-primary transition-colors">Conoce Nuestra Historia</span>
            <span class="material-symbols-outlined text-secondary group-hover:translate-x-2 transition-transform">arrow_forward</span>
          </router-link>
        </div>
      </section>
    </main>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { api } from '@/services/api'

const productosDestacados = ref([])
const productosDescuento = ref([])

const formatPrice = (price) => {
  return parseFloat(price).toFixed(2)
}

const agregarAlCarrito = async (id) => {
  try {
    const response = await fetch(`/api/carrito/agregar/${id}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
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

onMounted(async () => {
  try {
    const response = await fetch('/api/productos/destacados')
    const data = await response.json()
    productosDestacados.value = data.destacados || []
    productosDescuento.value = data.descuento || []
  } catch (error) {
    console.error('Error loading productos:', error)
  }
})
</script>

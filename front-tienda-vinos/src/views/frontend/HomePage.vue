<template>
  <main class="pt-32">

    <!-- ── Hero ─────────────────────────────────────────────────────────── -->
    <section class="relative px-12 py-24 max-w-screen-2xl mx-auto overflow-hidden">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">

        <!-- Texto -->
        <div class="lg:col-span-6 z-10">
          <h1 class="hero-title">
            El Sommelier Digital:
            <span class="italic block">Excelencia Curada para el Paladar Paciente</span>
          </h1>
          <p class="hero-subtitle">
            Un archivo curado de viticultura, donde el tiempo es el ingrediente
            principal y cada botella cuenta la historia de su terruño.
          </p>
          <RouterLink to="/catalogo" class="btn-primary-hero">
            Explorar la Bodega
          </RouterLink>
        </div>

        <!-- Imágenes hero -->
        <div class="lg:col-span-6 relative">
          <div class="aspect-[3/4] bg-[#f5f5dc] rounded-lg overflow-hidden shadow-xl
                      lg:translate-x-12 lg:rotate-2 flex items-center justify-center p-6">
            <template v-if="destacados[0]?.imagen_url">
              <img
                :alt="destacados[0].nombre"
                :src="destacados[0].imagen_url"
                class="max-w-full max-h-full object-contain grayscale hover:grayscale-0 transition-all duration-1000"
              />
            </template>
            <span v-else class="material-symbols-outlined hero-wine-icon">wine_bar</span>
          </div>

          <div v-if="destacados[1]"
               class="absolute -bottom-10 -left-10 w-64 h-80 hidden lg:flex bg-[#f5f5dc]
                      p-6 rounded-md shadow-lg -rotate-6 items-center justify-center overflow-hidden">
            <img v-if="destacados[1].imagen_url"
                 :alt="destacados[1].nombre"
                 :src="destacados[1].imagen_url"
                 class="max-w-full max-h-full object-contain"/>
            <span v-else class="material-symbols-outlined text-5xl opacity-20">wine_bar</span>
          </div>
        </div>
      </div>
    </section>

    <!-- ── Productos Destacados ──────────────────────────────────────────── -->
    <section class="bg-[#f5f5dc] py-24 px-12">
      <div class="max-w-screen-2xl mx-auto">

        <!-- Grid destacados -->
        <div class="mb-32">
          <div class="flex justify-between items-end mb-12">
            <div>
              <span class="section-eyebrow">Nuestra Selección</span>
              <h2 class="section-title">Vinos Destacados</h2>
            </div>
            <RouterLink to="/catalogo" class="link-secondary">Ver Todos</RouterLink>
          </div>

          <div v-if="loading" class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div v-for="n in 3" :key="n" class="product-skeleton"></div>
          </div>

          <div v-else-if="destacados.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            <article
              v-for="(producto, index) in destacados"
              :key="producto.id_producto"
              class="group transition-all hover:-translate-y-2"
              :class="index === 1 ? 'lg:translate-y-12' : ''"
            >
              <RouterLink :to="`/catalogo/${producto.id_producto}`" class="block">
                <div class="aspect-[3/4] mb-4 overflow-hidden rounded-lg bg-[#f5f5dc] relative p-6 flex items-center justify-center">
                  <img v-if="producto.imagen_url"
                       :alt="producto.nombre"
                       :src="producto.imagen_url"
                       class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-700"
                       :class="producto.cantidad <= 0 ? 'opacity-50 grayscale' : ''"/>
                  <span v-else class="material-symbols-outlined text-7xl opacity-20">wine_bar</span>

                  <div v-if="producto.cantidad <= 0" class="agotado-overlay">
                    <span class="agotado-badge">Agotado</span>
                  </div>
                </div>

                <span class="product-meta-text">
                  {{ producto.pais }}{{ producto.region ? ', ' + producto.region : '' }}{{ producto.anio_cosecha ? ', ' + producto.anio_cosecha : '' }}
                </span>
                <h3 class="product-name">{{ producto.nombre }}</h3>
              </RouterLink>

              <div class="mt-4 flex justify-between items-center">
                <span class="text-lg font-body text-[#745853]">${{ fmt(producto.precio) }}</span>
                <button v-if="producto.cantidad > 0"
                        @click="addToCart(producto)"
                        class="p-2 rounded-full hover:bg-[#eaead1] transition-colors text-[#2a0002] active:scale-90">
                  <span class="material-symbols-outlined">add_shopping_cart</span>
                </button>
              </div>
            </article>
          </div>

          <div v-else class="text-center py-16 text-[#544341] font-body italic">
            No hay productos disponibles en este momento.
          </div>
        </div>

        <!-- ── Ofertas Imperdibles ────────────────────────────────────────── -->
        <div v-if="descuentos.length" class="mt-32 mb-32">
          <div class="flex justify-between items-end mb-12">
            <div>
              <span class="section-eyebrow">Oportunidades Únicas</span>
              <h2 class="section-title">Ofertas Imperdibles</h2>
            </div>
            <RouterLink to="/catalogo?solo_descuentos=1" class="link-secondary">Ver Todas las Ofertas</RouterLink>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            <article
              v-for="producto in descuentos"
              :key="producto.id_producto"
              class="group transition-all hover:-translate-y-2"
            >
              <RouterLink :to="`/catalogo/${producto.id_producto}`" class="block">
                <div class="aspect-[3/4] mb-4 overflow-hidden rounded-lg bg-[#f5f5dc] relative p-6 flex items-center justify-center">
                  <img v-if="producto.imagen_url"
                       :alt="producto.nombre"
                       :src="producto.imagen_url"
                       class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-700"
                       :class="producto.cantidad <= 0 ? 'opacity-50 grayscale' : ''"/>
                  <span v-else class="material-symbols-outlined text-7xl opacity-20">wine_bar</span>

                  <div class="absolute top-4 right-4 bg-red-600 text-white text-[10px] px-3 py-1
                              font-label uppercase tracking-widest rounded-full shadow-lg">
                    -{{ producto.descuento }}%
                  </div>
                  <div v-if="producto.cantidad <= 0" class="agotado-overlay">
                    <span class="agotado-badge">Agotado</span>
                  </div>
                </div>

                <span class="product-meta-text">
                  {{ producto.pais }}{{ producto.region ? ', ' + producto.region : '' }}
                </span>
                <h3 class="product-name">{{ producto.nombre }}</h3>
              </RouterLink>

              <div class="mt-4 flex justify-between items-center">
                <div>
                  <span class="text-sm line-through text-black/40">${{ fmt(producto.precio) }}</span>
                  <span class="text-lg font-bold text-red-600 ml-2">
                    ${{ fmt(producto.precio * (1 - producto.descuento / 100)) }}
                  </span>
                </div>
                <button v-if="producto.cantidad > 0"
                        @click="addToCart(producto)"
                        class="p-2 rounded-full hover:bg-[#eaead1] transition-colors text-[#2a0002] active:scale-90">
                  <span class="material-symbols-outlined">add_shopping_cart</span>
                </button>
              </div>
            </article>
          </div>
        </div>

        <!-- ── Ediciones Especiales ──────────────────────────────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
          <div class="bg-[#2a0002] p-12 lg:p-24 rounded-lg text-white">
            <span class="font-label text-[#e4e4cc] uppercase tracking-[0.3em] text-xs font-bold mb-6 block opacity-80">
              Ediciones Especiales
            </span>
            <h2 class="text-4xl mb-8 leading-tight text-white" style="font-family: 'Noto Serif', serif;">
              Espumosos y Antigüedades
            </h2>
            <p class="font-body text-lg text-[#e4e4cc] leading-relaxed mb-10 opacity-90">
              Accede a nuestra reserva privada de champañas añejos y magnums de producción
              limitada, obtenidos directamente de las bóvedas más antiguas de la bodega.
            </p>
            <RouterLink to="/catalogo"
              class="mt-12 w-full py-4 border border-[#e4e4cc] text-[#e4e4cc] font-label
                     uppercase tracking-widest hover:bg-[#e4e4cc] hover:text-[#2a0002]
                     transition-all block text-center">
              Explorar el Catálogo
            </RouterLink>
          </div>

          <div class="grid grid-cols-2 gap-6">
            <div
              v-for="(p, i) in destacados.slice(0, 2)"
              :key="p.id_producto"
              class="rounded-md aspect-[3/4] bg-[#f5f5dc] p-6 flex items-center justify-center"
              :class="i === 1 ? 'translate-y-12' : ''"
            >
              <img v-if="p.imagen_url" :alt="p.nombre" :src="p.imagen_url"
                   class="max-w-full max-h-full object-contain"/>
              <span v-else class="material-symbols-outlined text-7xl opacity-20">wine_bar</span>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- ── Nuestra Historia ──────────────────────────────────────────────── -->
    <section class="py-24 px-12 text-center bg-[#fbfbe2]">
      <div class="max-w-2xl mx-auto border-t border-b border-[#dac1bf]/30 py-16">
        <h2 class="historia-title">Un Legado en Cada Copa</h2>
        <p class="historia-body">
          Fundados en la creencia de que una gran bodega es una biblioteca viva del tiempo,
          hemos pasado décadas cultivando relaciones con productores artesanales que priorizan
          el terruño por encima de la escala.
        </p>
        <RouterLink to="/about" class="inline-flex items-center gap-4 group">
          <span class="font-label uppercase tracking-widest text-sm text-[#745853]
                       group-hover:text-[#2a0002] transition-colors">
            Conoce Nuestra Historia
          </span>
          <span class="material-symbols-outlined text-[#745853] group-hover:translate-x-2 transition-transform">
            arrow_forward
          </span>
        </RouterLink>
      </div>
    </section>

  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import { useCartStore }         from '@/stores/cart'
import { useNotificationStore } from '@/stores/notifications'

const cartStore  = useCartStore()
const notifStore = useNotificationStore()

const destacados = ref([])
const descuentos = ref([])
const loading    = ref(true)

const fmt = (n) => Number(n).toFixed(2)

async function fetchHome() {
  try {
    const { data } = await api.get('/v1/home')
    destacados.value = data.destacados ?? []
    descuentos.value = data.descuentos ?? []
  } catch (e) {
    console.error('Error cargando home', e)
  } finally {
    loading.value = false
  }
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

onMounted(fetchHome)
</script>

<style scoped>
/* Hero */
.hero-title {
  font-family: 'Noto Serif', serif;
  font-size: clamp(2rem, 4vw, 4rem);
  line-height: 1.15;
  color: #2a0002;
  letter-spacing: -0.02em;
  margin-bottom: 2rem;
}

.hero-subtitle {
  font-family: 'Manrope', sans-serif;
  color: #544341;
  font-size: 1.125rem;
  max-width: 28rem;
  margin-bottom: 2.5rem;
  line-height: 1.7;
}

.btn-primary-hero {
  background-color: #2a0002;
  color: white;
  padding: 1rem 2rem;
  border-radius: 6px;
  font-family: 'Manrope', sans-serif;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.15em;
  font-weight: 700;
  text-decoration: none;
  display: inline-block;
  box-shadow: 0 10px 20px rgba(42, 0, 2, 0.15);
  transition: background-color 0.2s;
}

.btn-primary-hero:hover { background-color: #3d0003; }

.hero-wine-icon {
  font-size: 7rem;
  color: #dac1bf;
  opacity: 0.3;
}

/* Sections */
.section-eyebrow {
  font-family: 'Manrope', sans-serif;
  color: #735c00;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  font-size: 0.75rem;
  font-weight: 700;
  margin-bottom: 1rem;
  display: block;
}

.section-title {
  font-family: 'Noto Serif', serif;
  font-size: 2.25rem;
  color: #2a0002;
}

.link-secondary {
  color: #745853;
  font-family: 'Manrope', sans-serif;
  font-size: 0.875rem;
  border-bottom: 1px solid rgba(218, 193, 191, 0.3);
  padding-bottom: 4px;
  text-decoration: none;
  transition: color 0.2s;
}

.link-secondary:hover { color: #2a0002; }

/* Productos */
.product-meta-text {
  font-size: 0.75rem;
  font-family: 'Manrope', sans-serif;
  color: #78716c;
  text-transform: uppercase;
  letter-spacing: 0.15em;
}

.product-name {
  font-family: 'Noto Serif', serif;
  font-size: 1.25rem;
  color: #2a0002;
  margin-top: 0.5rem;
  transition: color 0.2s;
}

.group:hover .product-name { color: #735c00; }

/* Agotado */
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
  background: rgba(255,255,255,0.9);
  color: #2a0002;
  padding: 0.5rem 1rem;
  border-radius: 9999px;
  font-family: 'Manrope', sans-serif;
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.2em;
  font-weight: 700;
  box-shadow: 0 20px 40px rgba(0,0,0,0.1);
  border: 1px solid rgba(42, 0, 2, 0.1);
}

/* Skeleton loader */
.product-skeleton {
  aspect-ratio: 3/4;
  background: linear-gradient(90deg, #eaead1 25%, #e4e4cc 50%, #eaead1 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 8px;
}

@keyframes shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* Historia */
.historia-title {
  font-family: 'Noto Serif', serif;
  font-size: 1.875rem;
  color: #2a0002;
  margin-bottom: 1.5rem;
}

.historia-body {
  font-family: 'Manrope', sans-serif;
  color: #544341;
  line-height: 1.9;
  margin-bottom: 2.5rem;
}
</style>
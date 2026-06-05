<template>
  <div class="form-view max-w-4xl mx-auto pb-12">
    <header class="mb-8 flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold font-serif text-[#2a0002]">Nuevo Producto</h1>
        <p class="text-gray-600 mt-2">Registra un nuevo vino o licor en la bodega.</p>
      </div>
      <router-link :to="{ name: 'admin.productos.index' }" class="text-sm font-medium text-gray-500 hover:text-gray-900 flex items-center gap-1">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Volver
      </router-link>
    </header>

    <div v-if="loadingForm" class="flex justify-center p-12">
      <span class="material-symbols-outlined animate-spin text-3xl text-[#735c00]">progress_activity</span>
    </div>

    <form v-else @submit.prevent="submitForm" class="bg-white p-8 rounded-lg shadow-sm border border-gray-100 space-y-8">
      <div v-if="error" class="bg-red-50 text-red-700 p-4 rounded-md text-sm border border-red-200">
        {{ error }}
      </div>

      <!-- Sección 1: Información Principal -->
      <section>
        <h2 class="text-lg font-serif font-bold text-[#2a0002] border-b pb-2 mb-4">Información Principal</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Producto *</label>
            <input v-model="form.nombre" type="text" required class="input-field" placeholder="Ej: Casillero del Diablo Reserva">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
            <select v-model="form.id_categoria" required class="input-field">
              <option value="" disabled>Seleccionar...</option>
              <option v-for="cat in categorias" :key="cat.id_categoria" :value="cat.id_categoria">
                {{ cat.nombre }} {{ cat.nombre_padre ? `(de ${cat.nombre_padre})` : '' }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Marca / Bodega *</label>
            <select v-model="form.id_marca" required class="input-field">
              <option value="" disabled>Seleccionar...</option>
              <option v-for="marca in marcas" :key="marca.id_marca" :value="marca.id_marca">{{ marca.nombre }}</option>
            </select>
          </div>
          <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea v-model="form.descripcion" rows="3" class="input-field resize-y"></textarea>
          </div>
        </div>
      </section>

      <!-- Sección 2: Precios y Stock -->
      <section>
        <h2 class="text-lg font-serif font-bold text-[#2a0002] border-b pb-2 mb-4">Precios e Inventario</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Precio (₡) *</label>
            <input v-model.number="form.precio" type="number" min="0" step="0.01" required class="input-field">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Stock (Unidades) *</label>
            <input v-model.number="form.cantidad" type="number" min="0" required class="input-field">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descuento (%)</label>
            <input v-model.number="form.descuento" type="number" min="0" max="100" class="input-field">
          </div>
        </div>
      </section>

      <!-- Sección 3: Detalles Técnicos -->
      <section>
        <h2 class="text-lg font-serif font-bold text-[#2a0002] border-b pb-2 mb-4">Detalles Técnicos</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">País de Origen</label>
            <input v-model="form.pais" type="text" list="paises" class="input-field" placeholder="Ej: Argentina">
            <datalist id="paises">
              <option v-for="pais in paises" :key="pais" :value="pais"></option>
            </datalist>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Región</label>
            <input v-model="form.region" type="text" class="input-field" placeholder="Ej: Mendoza">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Año de Cosecha</label>
            <input v-model.number="form.anio_cosecha" type="number" min="1900" :max="new Date().getFullYear() + 1" class="input-field">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Alcohol (%)</label>
            <input v-model.number="form.alcohol_porcentaje" type="number" min="0" max="100" step="0.1" class="input-field">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Volumen (ml)</label>
            <input v-model.number="form.contenido_ml" type="number" min="0" class="input-field" placeholder="Ej: 750">
          </div>
        </div>
      </section>

      <!-- Sección 4: Media y Estado -->
      <section>
        <h2 class="text-lg font-serif font-bold text-[#2a0002] border-b pb-2 mb-4">Multimedia y Estado</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">URL de Imagen</label>
            <input v-model="form.imagen_url" type="url" class="input-field" placeholder="https://...">
            <div v-if="form.imagen_url" class="mt-2">
              <img :src="form.imagen_url" alt="Vista previa" class="h-32 object-contain rounded border border-gray-200">
            </div>
          </div>
          <div class="col-span-2">
            <label class="flex items-center gap-3 cursor-pointer p-4 border border-gray-200 rounded-md hover:bg-gray-50">
              <input type="checkbox" v-model="form.estado" class="w-5 h-5 accent-[#2a0002]">
              <div>
                <span class="block font-medium text-gray-900">Producto Activo</span>
                <span class="text-sm text-gray-500">Si está inactivo, no se mostrará en el catálogo público.</span>
              </div>
            </label>
          </div>
        </div>
      </section>

      <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
        <router-link :to="{ name: 'admin.productos.index' }" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-md font-medium hover:bg-gray-50 transition-colors">
          Cancelar
        </router-link>
        <button type="submit" :disabled="loadingSubmit" class="px-6 py-2.5 bg-[#2a0002] text-white rounded-md font-medium hover:bg-[#3d0003] transition-colors disabled:opacity-50">
          {{ loadingSubmit ? 'Guardando...' : 'Crear Producto' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ProductoController } from '@/controllers'
import { useNotificationStore } from '@/stores/notifications'

const router = useRouter()
const notif = useNotificationStore()

const loadingForm = ref(true)
const loadingSubmit = ref(false)
const error = ref(null)

const categorias = ref([])
const marcas = ref([])
const paises = ref([])

const form = reactive({
  nombre: '',
  id_categoria: '',
  id_marca: '',
  descripcion: '',
  precio: 0,
  cantidad: 0,
  descuento: 0,
  pais: '',
  region: '',
  anio_cosecha: null,
  alcohol_porcentaje: null,
  contenido_ml: 750,
  imagen_url: '',
  estado: true
})

async function fetchFormData() {
  try {
    const result = await ProductoController.obtenerFormData()

    if (!result.success) {
      throw new Error(result.message)
    }

    categorias.value = result.categorias
    marcas.value = result.marcas
    paises.value = result.paises
  } catch (err) {
    error.value = 'Error al cargar los datos auxiliares (categorías, marcas).'
    console.error(err)
  } finally {
    loadingForm.value = false
  }
}

async function submitForm() {
  loadingSubmit.value = true
  error.value = null
  try {
    const result = await ProductoController.crearProducto(form)

    if (!result.success) {
      throw result
    }

    notif.show('Producto creado exitosamente.', 'success')
    router.push({ name: 'admin.productos.index' })
  } catch (err) {
    if (err.status === 422) {
      error.value = err.message || 'Error de validación. Revisa los campos.'
    } else {
      error.value = 'Ocurrió un error inesperado al guardar el producto.'
    }
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } finally {
    loadingSubmit.value = false
  }
}

onMounted(fetchFormData)
</script>

<style scoped>
.input-field {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  transition: all 0.2s;
}
.input-field:focus {
  outline: none;
  border-color: #2a0002;
  box-shadow: 0 0 0 1px #2a0002;
}
</style>

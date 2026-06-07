<template>
  <div class="create-view-wrapper">
    <div v-if="loadingForm" class="flex justify-center" style="padding: 80px 0;">
      <span class="material-symbols-outlined" style="font-size:48px;color:var(--tertiary);animation:spin 1s linear infinite;">progress_activity</span>
    </div>

    <form v-else @submit.prevent="submitForm">

      <header class="header-section">
        <div class="header-text">
          <h1>Catalogar Nuevo Vino</h1>
          <p>Define la narrativa, especificaciones técnicas y el legado de una nueva adición a la cava digital.</p>
        </div>
        <div class="header-actions">
          <router-link :to="{ name: 'admin.productos.index' }" class="btn-discard">Descartar</router-link>
          <button type="submit" class="btn-save" :disabled="loadingSubmit">
            {{ loadingSubmit ? 'Guardando...' : 'Guardar Colección' }}
          </button>
        </div>
      </header>

      <div v-if="error" class="alert-premium error" style="margin-bottom:32px;">
        <span class="material-symbols-outlined alert-icon">error</span>
        <div class="alert-content">
          <span class="alert-title">Error</span>
          <p class="alert-message">{{ error }}</p>
        </div>
      </div>

      <div class="main-grid">
        <!-- ── FORM COLUMN ── -->
        <div class="form-column">

          <!-- Section 01: Información Básica -->
          <section>
            <div class="section-header">
              <span class="section-num">01</span>
              <h2>Información Básica</h2>
            </div>
            <div class="input-grid">
              <div class="form-group">
                <label for="nombre">Nombre del Vino</label>
                <input v-model="form.nombre" type="text" id="nombre" placeholder="ej. Château Margaux" required>
              </div>
              <div class="form-group">
                <label for="anio_cosecha">Cosecha (Vintage)</label>
                <input v-model.number="form.anio_cosecha" type="number" id="anio_cosecha" placeholder="2018">
              </div>
                          <div class="form-group">
                            <label for="pais">País de Origen</label>
                            <select v-model="form.pais" id="pais" class="premium-select" required>
                              <option value="" disabled>Seleccionar país...</option>
                              <option v-for="pais in paises" :key="pais" :value="pais">{{ pais }}</option>
                            </select>
                          </div>
              <div class="form-group">
                <label for="region">Región / Terroir</label>
                <input v-model="form.region" type="text" id="region" placeholder="Bordeaux">
              </div>
              <div class="form-group">
                <label for="id_categoria">Categoría Editorial</label>
                <select v-model="form.id_categoria" id="id_categoria" class="premium-select" required>
                  <option value="" disabled>Seleccionar...</option>
                  <option v-for="cat in categorias" :key="cat.id_categoria" :value="cat.id_categoria">
                    {{ cat.nombre }}
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label for="id_marca">Casa / Bodega</label>
                <select v-model="form.id_marca" id="id_marca" class="premium-select" required>
                  <option value="" disabled>Seleccionar...</option>
                  <option v-for="marca in marcas" :key="marca.id_marca" :value="marca.id_marca">{{ marca.nombre }}</option>
                </select>
              </div>
            </div>
          </section>

          <!-- Section 02: Detalles Técnicos -->
          <section>
            <div class="section-header">
              <span class="section-num">02</span>
              <h2>Detalles Técnicos</h2>
            </div>
            <div class="details-grid">
              <div class="detail-card">
                <label for="alcohol_porcentaje">Alcohol por Vol.</label>
                <div class="value-wrapper">
                  <input v-model.number="form.alcohol_porcentaje" type="number" step="0.1" id="alcohol_porcentaje" placeholder="13.5">
                  <span class="unit">%</span>
                </div>
              </div>
              <div class="detail-card">
                <label for="contenido_ml">Contenido</label>
                <div class="value-wrapper">
                  <input v-model.number="form.contenido_ml" type="number" id="contenido_ml" placeholder="750">
                  <span class="unit">ml</span>
                </div>
              </div>
              <div class="detail-card">
                <label>Estado Inicial</label>
                <div class="status-toggle">
                  <label class="switch">
                    <input type="checkbox" v-model="form.estado">
                    <span class="slider"></span>
                  </label>
                  <span class="stock-unit">{{ form.estado ? 'Activo' : 'Inactivo' }}</span>
                </div>
              </div>
            </div>
          </section>

          <!-- Section 03: Nota del Sommelier -->
          <section>
            <div class="section-header">
              <span class="section-num">03</span>
              <h2>Nota del Sommelier</h2>
            </div>
            <div class="note-area">
              <textarea v-model="form.descripcion" id="descripcion" rows="6" placeholder="Describe el carácter, bouquet y final de esta cosecha..."></textarea>
              <div class="note-badge">Voz Editorial</div>
            </div>
          </section>

          <!-- Section 04: Valoración y Stock -->
          <section>
            <div class="section-header">
              <span class="section-num">04</span>
              <h2>Valoración y Stock</h2>
            </div>
            <div class="storage-grid">
              <div class="valuation-item">
                <label for="precio">Precio Unitario</label>
                <div class="valuation-input-wrapper">
                  <span class="currency-symbol">$</span>
                  <input v-model.number="form.precio" type="number" step="0.01" id="precio" class="large-input" placeholder="0.00" required>
                </div>
              </div>
              <div class="valuation-item">
                <label for="cantidad">Cantidad en Cava</label>
                <div class="valuation-input-wrapper">
                  <input v-model.number="form.cantidad" type="number" id="cantidad" class="large-input" placeholder="0" required>
                  <span class="stock-unit">Botellas</span>
                </div>
              </div>
              <div class="valuation-item">
                <label for="descuento">Descuento (%)</label>
                <div class="valuation-input-wrapper">
                  <input v-model.number="form.descuento" type="number" step="1" id="descuento" class="large-input" placeholder="0">
                  <span class="stock-unit">% OFF</span>
                </div>
              </div>
            </div>
          </section>
        </div>

        <!-- ── VISUAL COLUMN ── -->
        <div class="visual-column">
          <div class="section-header">
            <span class="section-num">05</span>
            <h2>Identidad Visual</h2>
          </div>

          <div :class="['upload-box', form.imagen_url ? 'has-image' : '']" id="image-container" @click="$refs.fileInput.click()" style="cursor:pointer;">
            <img :src="form.imagen_url || ''" :class="['preview-img', form.imagen_url ? 'active' : '']" id="product-preview" alt="Preview">
            <div class="upload-content">
              <div class="upload-icon-circle">
                <span class="material-symbols-outlined">image_search</span>
              </div>
              <h3>Estética de la Botella</h3>
              <p>Sube una fotografía en alta resolución que capture la esencia de la etiqueta.</p>
            </div>
          </div>

          <input ref="fileInput" type="file" accept="image/*" style="display:none;" @change="handleImageUpload">

          <div v-if="uploadingImage" style="text-align:center;margin-top:12px;">
            <span class="material-symbols-outlined" style="font-size:24px;color:var(--tertiary);animation:spin 1s linear infinite;">progress_activity</span>
          </div>

          <div class="form-group" style="margin-top: 24px;">
            <label for="imagen_url">URL de la Imagen Editorial</label>
            <input v-model="form.imagen_url" type="text" id="imagen_url" placeholder="https://ejemplo.com/imagen.jpg" class="url-input">
          </div>

          <div class="curator-tip">
            <div class="tip-header">
              <span class="material-symbols-outlined" style="font-size: 14px;">auto_awesome</span>
              Tip del Curador
            </div>
            <p class="tip-text">
              "Al catalogar vinos de alta gama, asegúrese de resaltar la región y la cosecha, ya que son factores críticos para la valoración del coleccionista."
            </p>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ProductoController, ImageController } from '@/controllers'
import { useNotificationStore } from '@/stores/notifications'

const router = useRouter()
const notif = useNotificationStore()

const loadingForm = ref(true)
const loadingSubmit = ref(false)
const uploadingImage = ref(false)
const error = ref(null)

const categorias = ref([])
const marcas = ref([])
const paises = ref([])

const fileInput = ref(null)

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

async function handleImageUpload(event) {
  const file = event.target.files?.[0]
  if (!file) return

  uploadingImage.value = true
  try {
    const imageCtrl = new ImageController()
    const result = await imageCtrl.upload(file)

    if (!result.success) {
      throw new Error(result.message)
    }

    form.imagen_url = result.data.imagen_url
    notif.show('Imagen subida correctamente.', 'success')
  } catch (err) {
    error.value = err.message || 'Error al subir la imagen.'
  } finally {
    uploadingImage.value = false
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
@keyframes spin {
  from { transform: rotate(0deg); }
  to   { transform: rotate(360deg); }
}
</style>


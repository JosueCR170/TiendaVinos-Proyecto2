<template>
  <div class="create-view-wrapper">
    <div v-if="initialLoading" style="display:flex;justify-content:center;padding:80px 0;">
      <span class="material-symbols-outlined" style="font-size:48px;color:var(--tertiary);animation:spin 1s linear infinite;">progress_activity</span>
    </div>

    <form v-else @submit.prevent="submitForm">

      <header class="header-section">
        <div class="header-text">
          <h1>Editar Casa Vinícola</h1>
          <p>Actualiza la información y el legado de esta bodega en el catálogo.</p>
        </div>
        <div class="header-actions">
          <router-link :to="{ name: 'admin.marcas.index' }" class="btn-discard">Descartar</router-link>
          <button type="submit" class="btn-save" :disabled="loading">
            {{ loading ? 'Actualizando...' : 'Actualizar Bodega' }}
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
        <div class="form-column">

          <!-- Section 01 -->
          <section>
            <div class="section-header">
              <span class="section-num">01</span>
              <h2>Identidad de la Casa</h2>
            </div>
            <div class="input-grid">
              <div class="form-group">
                <label for="nombre">Nombre de la Bodega</label>
                <input v-model="form.nombre" type="text" id="nombre" placeholder="ej. Vega Sicilia" required>
              </div>
              <div class="form-group">
                <label for="pais">País de Origen</label>
                <input v-model="form.pais" list="paises-list" id="pais" class="premium-datalist-input" placeholder="Buscar país...">
                <datalist id="paises-list">
                  <option v-for="pais in paises" :key="pais" :value="pais"></option>
                </datalist>
              </div>
              <div class="form-group">
                <label for="sitio_web">Sitio Web Oficial</label>
                <input v-model="form.sitio_web" type="url" id="sitio_web" placeholder="https://www.bodega.com">
              </div>
            </div>
          </section>

          <!-- Section 02 -->
          <section>
            <div class="section-header">
              <span class="section-num">02</span>
              <h2>Historia y Legado</h2>
            </div>
            <div class="note-area">
              <textarea v-model="form.descripcion" id="descripcion" rows="6" placeholder="Cuéntanos la historia de esta bodega, sus métodos y filosofía..."></textarea>
              <div class="note-badge">Voz Editorial</div>
            </div>
          </section>

        </div>

        <div class="visual-column">
          <div class="curator-tip">
            <div class="tip-header">
              <span class="material-symbols-outlined" style="font-size:14px;">auto_awesome</span>
              Prestigio de Marca
            </div>
            <p class="tip-text">
              "Mantener la información actualizada, especialmente los sitios web oficiales, genera confianza en los coleccionistas más detallistas."
            </p>
          </div>
        </div>
      </div>

    </form>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { MarcaController } from '@/controllers'
import { useNotificationStore } from '@/stores/notifications'

const router = useRouter()
const route = useRoute()
const notif = useNotificationStore()
const id = route.params.id

const initialLoading = ref(true)
const loading = ref(false)
const error = ref(null)

const paises = [
  'Argentina', 'Australia', 'Austria', 'Chile', 'España', 'Estados Unidos',
  'Francia', 'Alemania', 'Italia', 'Nueva Zelanda', 'Portugal', 'Sudáfrica',
  'Uruguay', 'Grecia', 'Hungría', 'Costa Rica'
]

const form = reactive({
  nombre: '',
  pais: '',
  sitio_web: '',
  descripcion: ''
})

async function fetchData() {
  try {
    const result = await MarcaController.obtenerMarcaPorId(id)
    if (!result.success) throw new Error(result.message)
    form.nombre = result.marca.nombre
    form.pais = result.marca.pais || ''
    form.sitio_web = result.marca.sitio_web || ''
    form.descripcion = result.marca.descripcion || ''
  } catch (err) {
    error.value = 'No se encontró la marca.'
    console.error(err)
  } finally {
    initialLoading.value = false
  }
}

async function submitForm() {
  loading.value = true
  error.value = null
  try {
    const result = await MarcaController.actualizarMarca(id, form)
    if (!result.success) throw result
    notif.show('Marca actualizada exitosamente.')
    router.push({ name: 'admin.marcas.index' })
  } catch (err) {
    error.value = err.status === 422
      ? err.message || 'Datos inválidos.'
      : 'Ocurrió un error inesperado.'
  } finally {
    loading.value = false
  }
}

onMounted(fetchData)
</script>

<style scoped>
@keyframes spin {
  from { transform: rotate(0deg); }
  to   { transform: rotate(360deg); }
}
</style>

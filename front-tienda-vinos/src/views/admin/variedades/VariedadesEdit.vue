<template>
  <div class="create-view-wrapper">
    <div v-if="initialLoading" style="display:flex;justify-content:center;padding:80px 0;">
      <span class="material-symbols-outlined" style="font-size:48px;color:var(--tertiary);animation:spin 1s linear infinite;">progress_activity</span>
    </div>

    <form v-else @submit.prevent="submitForm">

      <header class="header-section">
        <div class="header-text">
          <h1>Editar Variedad de Uva</h1>
          <p>Actualiza la definición y perfil de esta cepa en la cava.</p>
        </div>
        <div class="header-actions">
          <router-link :to="{ name: 'admin.variedades.index' }" class="btn-discard">Descartar</router-link>
          <button type="submit" class="btn-save" :disabled="loading">
            {{ loading ? 'Actualizando...' : 'Actualizar Variedad' }}
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
              <h2>Perfil de la Variedad</h2>
            </div>
            <div class="input-grid">
              <div class="form-group">
                <label for="nombre">Nombre de la Variedad</label>
                <input v-model="form.nombre" type="text" id="nombre" placeholder="ej. Cabernet Sauvignon" required>
              </div>
              <div class="form-group">
                <label for="tipo">Tipo de Uva</label>
                <select v-model="form.tipo" id="tipo" class="premium-select" required>
                  <option value="" disabled>Seleccionar...</option>
                  <option value="Tinta">Tinta</option>
                  <option value="Blanca">Blanca</option>
                  <option value="Aromatica">Aromatica</option>
                </select>
              </div>
            </div>
          </section>

          <!-- Section 02 -->
          <section>
            <div class="section-header">
              <span class="section-num">02</span>
              <h2>Notas de Cepa</h2>
            </div>
            <div class="note-area">
              <textarea v-model="form.descripcion" id="descripcion" rows="6" placeholder="Describe las características típicas, aromas y sabores de esta variedad..."></textarea>
              <div class="note-badge">Voz de Sommelier</div>
            </div>
          </section>

        </div>

        <div class="visual-column">
          <div class="curator-tip">
            <div class="tip-header">
              <span class="material-symbols-outlined" style="font-size:14px;">auto_awesome</span>
              Identidad del Terroir
            </div>
            <p class="tip-text">
              "Al editar, asegúrese de que la descripción sea fiel a las características de la cepa para guiar correctamente al consumidor."
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
import { VariedadController } from '@/controllers'
import { useNotificationStore } from '@/stores/notifications'

const router = useRouter()
const route = useRoute()
const notif = useNotificationStore()
const id = route.params.id

const initialLoading = ref(true)
const loading = ref(false)
const error = ref(null)

const form = reactive({
  nombre: '',
  tipo: '',
  descripcion: ''
})

async function fetchData() {
  try {
    const result = await VariedadController.obtenerVariedadPorId(id)
    if (!result.success) throw new Error(result.message)
    form.nombre = result.variedad.nombre
    form.tipo = result.variedad.tipo
    form.descripcion = result.variedad.descripcion || ''
  } catch (err) {
    error.value = 'No se encontró la variedad.'
    console.error(err)
  } finally {
    initialLoading.value = false
  }
}

async function submitForm() {
  loading.value = true
  error.value = null
  try {
    const result = await VariedadController.actualizarVariedad(id, form)
    if (!result.success) throw result
    notif.show('Variedad actualizada exitosamente.')
    router.push({ name: 'admin.variedades.index' })
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

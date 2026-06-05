<template>
  <div class="create-view-wrapper">
    <div v-if="initialLoading" style="display:flex;justify-content:center;padding:80px 0;">
      <span class="material-symbols-outlined" style="font-size:48px;color:var(--tertiary);animation:spin 1s linear infinite;">progress_activity</span>
    </div>

    <form v-else @submit.prevent="submitForm">

      <header class="header-section">
        <div class="header-text">
          <h1>Editar Categoría Editorial</h1>
          <p>Ajusta la definición y posición jerárquica de esta categoría.</p>
        </div>
        <div class="header-actions">
          <router-link :to="{ name: 'admin.categorias.index' }" class="btn-discard">Descartar</router-link>
          <button type="submit" class="btn-save" :disabled="loading">
            {{ loading ? 'Actualizando...' : 'Actualizar Categoría' }}
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
              <h2>Identidad de la Categoría</h2>
            </div>
            <div class="input-grid">
              <div class="form-group">
                <label for="nombre">Nombre de la Categoría</label>
                <input v-model="form.nombre" type="text" id="nombre" placeholder="ej. Tintos de Guarda" required>
              </div>
              <div class="form-group">
                <label for="nivel_display">Nivel Jerárquico</label>
                <select id="nivel_display" class="premium-select" disabled>
                  <option :value="1" :selected="form.nivel === 1">Nivel 1 (Principal)</option>
                  <option :value="2" :selected="form.nivel === 2">Nivel 2 (Subcategoría)</option>
                </select>
              </div>
              <div class="form-group">
                <label for="nombre_padre">Categoría Padre (Si aplica)</label>
                <template v-if="form.nivel === 1">
                  <select class="premium-select" disabled>
                    <option value="">Ninguna (Raíz)</option>
                  </select>
                </template>
                <template v-else>
                  <select v-model="form.nombre_padre" id="nombre_padre" class="premium-select" required>
                    <option value="" disabled>Seleccione una categoría superior</option>
                    <option v-for="cat in categoriasPrincipales" :key="cat.id_categoria" :value="cat.id_categoria">
                      {{ cat.nombre }}
                    </option>
                  </select>
                </template>
              </div>
            </div>
          </section>

          <!-- Section 02 -->
          <section>
            <div class="section-header">
              <span class="section-num">02</span>
              <h2>Descripción Editorial</h2>
            </div>
            <div class="note-area">
              <textarea v-model="form.descripcion" id="descripcion" rows="6" placeholder="Define el propósito y carácter de esta categoría..."></textarea>
              <div class="note-badge">Voz de Cava</div>
            </div>
          </section>

        </div>

        <div class="visual-column">
          <div class="curator-tip">
            <div class="tip-header">
              <span class="material-symbols-outlined" style="font-size:14px;">auto_awesome</span>
              Estructura de Bodega
            </div>
            <p class="tip-text">
              "Al editar, considere que los cambios en el nombre pueden afectar la navegación del usuario si ya está familiarizado con la estructura actual."
            </p>
          </div>
        </div>
      </div>

    </form>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { CategoriaController } from '@/controllers'
import { useNotificationStore } from '@/stores/notifications'

const router = useRouter()
const route = useRoute()
const notif = useNotificationStore()

const id = route.params.id
const loading = ref(false)
const initialLoading = ref(true)
const error = ref(null)
const todasCategorias = ref([])

const form = reactive({
  nombre: '',
  nivel: 1,
  nombre_padre: '',
  descripcion: ''
})

const categoriasPrincipales = computed(() =>
  todasCategorias.value.filter(c => c.nivel === 1 && c.id_categoria != id)
)

async function fetchData() {
  initialLoading.value = true
  try {
    const result = await CategoriaController.obtenerCategorias({ per_page: 100 })
    if (!result.success) throw new Error(result.message)
    todasCategorias.value = result.categorias
    const cat = result.categorias.find(c => c.id_categoria == id)
    if (cat) {
      form.nombre = cat.nombre
      form.nivel = cat.nivel
      form.nombre_padre = cat.nombre_padre || ''
      form.descripcion = cat.descripcion || ''
    } else {
      error.value = 'No se encontró la categoría solicitada.'
    }
  } catch (err) {
    error.value = 'Error al cargar los datos de la categoría.'
    console.error(err)
  } finally {
    initialLoading.value = false
  }
}

async function submitForm() {
  loading.value = true
  error.value = null
  try {
    const payload = { ...form }
    if (payload.nivel === 1) payload.nombre_padre = null
    const result = await CategoriaController.actualizarCategoria(id, payload)
    if (!result.success) throw result
    notif.show('Categoría actualizada exitosamente.')
    router.push({ name: 'admin.categorias.index' })
  } catch (err) {
    error.value = err.status === 422
      ? err.message || 'Datos inválidos. Verifica el formulario.'
      : 'Ocurrió un error inesperado al actualizar la categoría.'
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

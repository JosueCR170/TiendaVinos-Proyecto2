<template>
  <div class="form-view max-w-2xl mx-auto">
    <header class="mb-8 flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold font-serif text-[#2a0002]">Nueva Categoría</h1>
        <p class="text-gray-600 mt-2">Crea una nueva clasificación para el catálogo.</p>
      </div>
      <router-link :to="{ name: 'admin.categorias.index' }" class="text-sm font-medium text-gray-500 hover:text-gray-900 flex items-center gap-1">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Volver
      </router-link>
    </header>

    <form @submit.prevent="submitForm" class="bg-white p-8 rounded-lg shadow-sm border border-gray-100">
      <div v-if="error" class="mb-6 bg-red-50 text-red-700 p-4 rounded-md text-sm border border-red-200">
        {{ error }}
      </div>

      <div class="space-y-6">
        <!-- Nombre -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Categoría *</label>
          <input 
            v-model="form.nombre" 
            type="text" 
            required 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-[#2a0002] focus:border-[#2a0002]"
            placeholder="Ej: Tintos, Blancos, Reserva..."
          >
        </div>

        <!-- Nivel -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nivel Jerárquico *</label>
          <div class="flex gap-4 mt-2">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" v-model="form.nivel" :value="1" class="accent-[#2a0002]">
              <span class="text-sm">Categoría Principal (Raíz)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" v-model="form.nivel" :value="2" class="accent-[#2a0002]">
              <span class="text-sm">Subcategoría</span>
            </label>
          </div>
        </div>

        <!-- Categoría Padre (solo si nivel 2) -->
        <div v-if="form.nivel === 2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Categoría Padre *</label>
          <select 
            v-model="form.nombre_padre" 
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-[#2a0002]"
          >
            <option value="" disabled>Seleccione una categoría principal...</option>
            <option v-for="cat in categoriasPrincipales" :key="cat.id_categoria" :value="cat.id_categoria">
              {{ cat.nombre }}
            </option>
          </select>
        </div>

        <!-- Descripción -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
          <textarea 
            v-model="form.descripcion" 
            rows="4" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-[#2a0002] resize-y"
            placeholder="Breve descripción opcional..."
          ></textarea>
        </div>
      </div>

      <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
        <router-link :to="{ name: 'admin.categorias.index' }" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-50 transition-colors">
          Cancelar
        </router-link>
        <button 
          type="submit" 
          :disabled="loading"
          class="px-4 py-2 bg-[#2a0002] text-white rounded-md text-sm font-medium hover:bg-[#3d0003] transition-colors disabled:opacity-50"
        >
          {{ loading ? 'Guardando...' : 'Guardar Categoría' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/services/api'
import { useNotificationStore } from '@/stores/notifications'

const router = useRouter()
const route = useRoute()
const notif = useNotificationStore()

const loading = ref(false)
const error = ref(null)
const todasCategorias = ref([])

const form = reactive({
  nombre: '',
  nivel: route.query.parent_id ? 2 : 1,
  nombre_padre: route.query.parent_id ? Number(route.query.parent_id) : '',
  descripcion: ''
})

const categoriasPrincipales = computed(() => {
  return todasCategorias.value.filter(c => c.nivel === 1)
})

async function fetchCategorias() {
  try {
    const { data } = await api.get('/admin/categorias')
    todasCategorias.value = data.data
  } catch (err) {
    console.error('No se pudieron cargar las categorías para el select.', err)
  }
}

async function submitForm() {
  loading.value = true
  error.value = null
  
  try {
    const payload = { ...form }
    if (payload.nivel === 1) payload.nombre_padre = null

    await api.post('/admin/categorias', payload)
    notif.show('Categoría creada exitosamente.')
    router.push({ name: 'admin.categorias.index' })
  } catch (err) {
    if (err.response?.status === 422) {
      error.value = err.response.data.message || 'Datos inválidos. Verifica el formulario.'
    } else {
      error.value = 'Ocurrió un error inesperado al guardar la categoría.'
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchCategorias()
})
</script>

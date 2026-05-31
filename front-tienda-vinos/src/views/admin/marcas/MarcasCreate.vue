<template>
  <div class="form-view max-w-2xl mx-auto">
    <header class="mb-8 flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold font-serif text-[#2a0002]">Nueva Marca</h1>
        <p class="text-gray-600 mt-2">Agrega una nueva bodega productora.</p>
      </div>
      <router-link :to="{ name: 'admin.marcas.index' }" class="text-sm font-medium text-gray-500 hover:text-gray-900 flex items-center gap-1">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Volver
      </router-link>
    </header>

    <form @submit.prevent="submitForm" class="bg-white p-8 rounded-lg shadow-sm border border-gray-100">
      <div v-if="error" class="mb-6 bg-red-50 text-red-700 p-4 rounded-md text-sm border border-red-200">
        {{ error }}
      </div>

      <div class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Marca *</label>
          <input 
            v-model="form.nombre" 
            type="text" 
            required 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-[#2a0002]"
            placeholder="Ej: Concha y Toro, Rutini..."
          >
        </div>
      </div>

      <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
        <router-link :to="{ name: 'admin.marcas.index' }" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-50 transition-colors">
          Cancelar
        </router-link>
        <button 
          type="submit" 
          :disabled="loading"
          class="px-4 py-2 bg-[#2a0002] text-white rounded-md text-sm font-medium hover:bg-[#3d0003] transition-colors disabled:opacity-50"
        >
          {{ loading ? 'Guardando...' : 'Guardar Marca' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import { useNotificationStore } from '@/stores/notifications'

const router = useRouter()
const notif = useNotificationStore()

const loading = ref(false)
const error = ref(null)

const form = reactive({ nombre: '' })

async function submitForm() {
  loading.value = true
  error.value = null
  try {
    await api.post('/admin/marcas', form)
    notif.show('Marca creada exitosamente.')
    router.push({ name: 'admin.marcas.index' })
  } catch (err) {
    if (err.response?.status === 422) {
      error.value = err.response.data.message || 'Datos inválidos.'
    } else {
      error.value = 'Ocurrió un error inesperado al guardar.'
    }
  } finally {
    loading.value = false
  }
}
</script>

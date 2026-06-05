import axios from 'axios'

/**
 * Instancia axios configurada para el backend Laravel.
 * Usa el proxy de Vite en desarrollo (/api → localhost:8000/api).
 */
const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true,
})

// Interceptor de respuesta: maneja errores globalmente
api.interceptors.response.use(
  (response) => response,
  (error) => {
    const mensaje = error.response?.data?.message ?? error.message ?? 'Error desconocido'
    console.error('[API Error]', error.response?.status, mensaje)
    console.error('Error completo:', error.response?.data ?? error)
    return Promise.reject(error)
  }
)

// Obtener token CSRF de la cookie
const getCsrfToken = () => {
  const name = 'XSRF-TOKEN'
  let cookieValue = null
  if (document.cookie && document.cookie !== '') {
    const cookies = document.cookie.split(';')
    for (let i = 0; i < cookies.length; i++) {
      const cookie = cookies[i].trim()
      if (cookie.substring(0, name.length + 1) === (name + '=')) {
        cookieValue = decodeURIComponent(cookie.substring(name.length + 1))
        break
      }
    }
  }
  return cookieValue
}

// Agregar token CSRF a cada petición y manejar FormData
api.interceptors.request.use(
  (config) => {
    const token = getCsrfToken()
    if (token) {
      config.headers['X-CSRF-TOKEN'] = token
    }

    // Si es FormData, remover Content-Type para que axios lo maneje automáticamente
    if (config.data instanceof FormData) {
      delete config.headers['Content-Type']
    }

    return config
  },
  (error) => Promise.reject(error)
)

export { api }
export default api

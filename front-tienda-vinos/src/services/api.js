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
  withCredentials: false,
})

// Interceptor de respuesta: maneja errores globalmente
api.interceptors.response.use(
  (response) => response,
  (error) => {
    const mensaje = error.response?.data?.message ?? error.message ?? 'Error desconocido'
    console.error('[API Error]', error.response?.status, mensaje)
    return Promise.reject(error)
  }
)

export { api }
export default api

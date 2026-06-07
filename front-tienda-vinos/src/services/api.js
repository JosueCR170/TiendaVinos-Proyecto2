import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true,
})

const STORAGE_BASE = import.meta.env.VITE_STORAGE_URL ?? 'http://127.0.0.1:8000'

// Función para convertir URLs relativas a absolutas
const resolverUrls = (obj) => {
  if (!obj || typeof obj !== 'object') return obj
  for (const key in obj) {
    if (typeof obj[key] === 'string' && obj[key].startsWith('/storage/')) {
      obj[key] = `${STORAGE_BASE}${obj[key]}`
    } else if (typeof obj[key] === 'object') {
      resolverUrls(obj[key])
    }
  }
  return obj
}

// Interceptor de respuesta: maneja errores y convierte URLs de storage
api.interceptors.response.use(
  (response) => {
    if (response.data) {
      resolverUrls(response.data)
    }
    return response
  },
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

    if (config.data instanceof FormData) {
      delete config.headers['Content-Type']
    }

    return config
  },
  (error) => Promise.reject(error)
)

export { api }
export default api
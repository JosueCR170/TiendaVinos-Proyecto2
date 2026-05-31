import api from '@/services/api'

export default class BaseController {
  constructor(resource) {
    this.resource = resource
  }

  async request(action) {
    try {
      const response = await action()
      return this.success(response.data)
    } catch (error) {
      return this.fail(error)
    }
  }

  success(payload = {}) {
    return {
      success: payload.success ?? true,
      message: payload.message ?? '',
      data: payload.data ?? payload,
      raw: payload,
    }
  }

  fail(error) {
    const payload = error.response?.data ?? {}

    return {
      success: false,
      message: payload.message ?? error.message ?? 'Error al procesar la solicitud.',
      errors: payload.errors ?? null,
      error: payload.error ?? error,
      status: error.response?.status ?? null,
      raw: payload,
    }
  }

  listar(params = {}) {
    return this.request(() => api.get(this.resource, { params }))
  }

  obtener(id) {
    return this.request(() => {
      this.requireId(id)
      return api.get(`${this.resource}/${id}`)
    })
  }

  crear(data) {
    return this.request(() => api.post(this.resource, data))
  }

  actualizar(id, data) {
    return this.request(() => {
      this.requireId(id)
      return api.put(`${this.resource}/${id}`, data)
    })
  }

  eliminar(id) {
    return this.request(() => {
      this.requireId(id)
      return api.delete(`${this.resource}/${id}`)
    })
  }

  requireId(id) {
    if (!id) {
      throw new Error('El ID es requerido.')
    }
  }

  paginator(payload) {
    const data = payload.data ?? {}

    return {
      items: data.data ?? [],
      currentPage: data.current_page ?? 1,
      lastPage: data.last_page ?? 1,
      total: data.total ?? 0,
      from: data.from ?? 0,
      to: data.to ?? 0,
      perPage: data.per_page ?? null,
      links: data.links ?? [],
    }
  }
}

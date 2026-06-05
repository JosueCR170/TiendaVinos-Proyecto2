import BaseController from './BaseController'
import { Variedad } from '@/models'

class VariedadController extends BaseController {
  constructor() {
    super('/v1/variedades')
  }

  async obtenerVariedades(params = {}) {
    const response = await this.listar(params)
    const pagination = this.paginator(response)

    return {
      ...response,
      variedades: pagination.items.map((variedad) => new Variedad(variedad)),
      pagination,
    }
  }

  async obtenerVariedadPorId(idVariedad) {
    const response = await this.obtener(idVariedad)

    return {
      ...response,
      variedad: new Variedad(response.data),
    }
  }

  crearVariedad(data) {
    return this.crear(data)
  }

  actualizarVariedad(idVariedad, data) {
    return this.actualizar(idVariedad, data)
  }

  eliminarVariedad(idVariedad) {
    return this.eliminar(idVariedad)
  }

  filtrarVariedadesPorNombre(variedades = [], termino = '') {
    if (!termino.trim()) {
      return variedades
    }

    const search = termino.toLowerCase()

    return variedades.filter((variedad) =>
      variedad.nombre?.toLowerCase().includes(search)
    )
  }

  ordenarVariedadesAlfabeticamente(variedades = []) {
    return [...variedades].sort((a, b) =>
      a.nombre.localeCompare(b.nombre)
    )
  }
}

export default new VariedadController()

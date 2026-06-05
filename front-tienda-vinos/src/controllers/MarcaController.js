import BaseController from './BaseController'
import { Marca } from '@/models'

class MarcaController extends BaseController {
  constructor() {
    super('/v1/marcas')
  }

  async obtenerMarcas(params = {}) {
    const response = await this.listar(params)
    const pagination = this.paginator(response)

    return {
      ...response,
      marcas: pagination.items.map((marca) => new Marca(marca)),
      pagination,
    }
  }

  async obtenerMarcaPorId(idMarca) {
    const response = await this.obtener(idMarca)

    return {
      ...response,
      marca: new Marca(response.data),
    }
  }

  crearMarca(data) {
    return this.crear(data)
  }

  actualizarMarca(idMarca, data) {
    return this.actualizar(idMarca, data)
  }

  eliminarMarca(idMarca) {
    return this.eliminar(idMarca)
  }

  filtrarMarcasPorNombre(marcas = [], termino = '') {
    if (!termino.trim()) {
      return marcas
    }

    const search = termino.toLowerCase()

    return marcas.filter((marca) =>
      marca.nombre?.toLowerCase().includes(search)
    )
  }

  ordenarMarcasAlfabeticamente(marcas = []) {
    return [...marcas].sort((a, b) =>
      a.nombre.localeCompare(b.nombre)
    )
  }
}

export default new MarcaController()

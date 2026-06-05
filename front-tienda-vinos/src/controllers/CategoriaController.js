import BaseController from './BaseController'
import { Categoria } from '@/models'

class CategoriaController extends BaseController {
  constructor() {
    super('/v1/categorias')
  }

  async obtenerCategorias(params = {}) {
    const response = await this.listar(params)
    const pagination = this.paginator(response)

    return {
      ...response,
      categorias: pagination.items.map((categoria) => new Categoria(categoria)),
      pagination,
    }
  }

  async obtenerCategoriaPorId(idCategoria) {
    const response = await this.obtener(idCategoria)

    return {
      ...response,
      categoria: new Categoria(response.data),
    }
  }

  crearCategoria(data) {
    return this.crear(data)
  }

  actualizarCategoria(idCategoria, data) {
    return this.actualizar(idCategoria, data)
  }

  eliminarCategoria(idCategoria) {
    return this.eliminar(idCategoria)
  }

  filtrarCategoriasPorNombre(categorias = [], termino = '') {
    if (!termino.trim()) {
      return categorias
    }

    const search = termino.toLowerCase()

    return categorias.filter((categoria) =>
      categoria.nombre?.toLowerCase().includes(search)
    )
  }

  agruparCategoriasPorNivel(categorias = []) {
    return categorias.reduce((agrupadas, categoria) => {
      const nivel = categoria.nivel ?? 'sin_nivel'

      if (!agrupadas[nivel]) {
        agrupadas[nivel] = []
      }

      agrupadas[nivel].push(categoria)
      return agrupadas
    }, {})
  }
}

export default new CategoriaController()

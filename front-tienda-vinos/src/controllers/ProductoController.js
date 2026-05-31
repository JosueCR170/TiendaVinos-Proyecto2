import BaseController from './BaseController'

class ProductoController extends BaseController {
  constructor() {
    super('/v1/productos')
  }

  async obtenerProductos(params = {}) {
    const response = await this.listar(this.normalizeParams(params))
    const pagination = this.paginator(response)

    return {
      ...response,
      productos: pagination.items,
      pagination,
    }
  }

  async obtenerCatalogo(filtros = {}, page = 1, perPage = 12) {
    return this.obtenerProductos({
      ...filtros,
      page,
      per_page: perPage,
    })
  }

  async obtenerProductoPorId(idProducto) {
    const response = await this.obtener(idProducto)

    return {
      ...response,
      producto: response.data,
    }
  }

  crearProducto(data) {
    return this.crear(data)
  }

  actualizarProducto(idProducto, data) {
    return this.actualizar(idProducto, data)
  }

  eliminarProducto(idProducto) {
    return this.eliminar(idProducto)
  }

  buscarProductos(termino, page = 1, perPage = 12) {
    if (!termino?.trim()) {
      return Promise.resolve({
        success: false,
        message: 'Termino de busqueda es requerido.',
      })
    }

    return this.obtenerCatalogo({ search: termino }, page, perPage)
  }

  filtrarPorCategoria(idCategoria, page = 1, perPage = 12) {
    return this.obtenerCatalogo({ id_categoria: idCategoria }, page, perPage)
  }

  filtrarPorMarca(idMarca, page = 1, perPage = 12) {
    return this.obtenerCatalogo({ id_marca: idMarca }, page, perPage)
  }

  filtrarPorPais(pais, page = 1, perPage = 12) {
    return this.obtenerCatalogo({ pais }, page, perPage)
  }

  ordenarProductos(sortBy = 'id_producto', direction = 'asc', page = 1, perPage = 12) {
    return this.obtenerCatalogo({
      sort_by: sortBy,
      direction,
    }, page, perPage)
  }

  normalizeParams(params = {}) {
    const normalized = { ...params }

    if (normalized.buscar && !normalized.search) {
      normalized.search = normalized.buscar
      delete normalized.buscar
    }

    if (normalized.categoria && !normalized.id_categoria) {
      normalized.id_categoria = normalized.categoria
      delete normalized.categoria
    }

    if (normalized.marca && !normalized.id_marca) {
      normalized.id_marca = normalized.marca
      delete normalized.marca
    }

    if (normalized.sort && !normalized.sort_by) {
      normalized.sort_by = normalized.sort
      delete normalized.sort
    }

    Object.keys(normalized).forEach((key) => {
      if (
        normalized[key] === '' ||
        normalized[key] === null ||
        normalized[key] === undefined
      ) {
        delete normalized[key]
      }
    })

    return normalized
  }
}

export default new ProductoController()

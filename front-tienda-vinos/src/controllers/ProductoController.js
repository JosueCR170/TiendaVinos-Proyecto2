import BaseController from './BaseController'
import CategoriaController from './CategoriaController'
import MarcaController from './MarcaController'
import VariedadController from './VariedadController'
import api from '@/services/api'
import { Producto } from '@/models'

class ProductoController extends BaseController {
  constructor() {
    super('/v1/productos')
  }

  async obtenerProductos(params = {}) {
    const response = await this.listar(this.normalizeParams(params))
    const pagination = this.paginator(response)

    return {
      ...response,
      productos: pagination.items.map((producto) => new Producto(producto)),
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
      producto: new Producto(response.data),
    }
  }

  async obtenerFormData() {
    // Prefer backend endpoint that returns form data (categorias, marcas, variedades, paises)
    const response = await this.request(() => api.get(`${this.resource}/form-data`))

    const data = response.data ?? {}

    return {
      success: response.success,
      message: response.message || 'Datos del formulario obtenidos correctamente.',
      categorias: data.categorias ?? [],
      marcas: data.marcas ?? [],
      variedades: data.variedades ?? [],
      paises: data.paises ?? [],
      errors: response.success ? [] : [response],
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

    if (normalized.orden && !normalized.sort_by) {
      const sortMap = {
        newest: { sort_by: 'id_producto', direction: 'desc' },
        precio_asc: { sort_by: 'precio', direction: 'asc' },
        precio_desc: { sort_by: 'precio', direction: 'desc' },
        nombre: { sort_by: 'nombre', direction: 'asc' },
      }

      Object.assign(normalized, sortMap[normalized.orden] ?? {})
      delete normalized.orden
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

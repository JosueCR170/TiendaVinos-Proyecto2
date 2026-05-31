import { api } from '@/services/api'

class ProductoController {
  /**
   * Obtiene todos los productos del catálogo con filtros opcionales
   * @param {Object} filtros - Objeto con filtros (buscar, categoria, marca, etc)
   * @param {Number} page - Número de página para paginación
   * @param {Number} perPage - Cantidad de productos por página
   * @returns {Promise<Object>}
   */
  async obtenerCatalogo(filtros = {}, page = 1, perPage = 12) {
    try {
      const params = new URLSearchParams({
        page,
        per_page: perPage,
        ...filtros
      })

      const response = await api.get(`/api/catalogo?${params.toString()}`)
      
      if (!response.success) {
        throw new Error(response.message || 'Error al obtener catálogo')
      }

      return {
        success: true,
        productos: response.data.productos,
        pagination: response.data.pagination,
        filtros: response.data.filtros
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al obtener catálogo',
        error
      }
    }
  }

  /**
   * Obtiene los productos destacados de la portada
   * @returns {Promise<Object>}
   */
  async obtenerProductosDestacados() {
    try {
      const response = await api.get('/api')
      
      if (!response.success) {
        throw new Error(response.message || 'Error al obtener productos destacados')
      }

      return {
        success: true,
        destacados: response.data.destacados,
        descuentos: response.data.descuentos
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al obtener productos',
        error
      }
    }
  }

  /**
   * Obtiene los detalles de un producto específico
   * @param {Number} id - ID del producto
   * @returns {Promise<Object>}
   */
  async obtenerProductoPorId(id) {
    try {
      if (!id) {
        throw new Error('ID de producto es requerido')
      }

      const response = await api.get(`/api/productos/${id}`)
      
      if (!response.success) {
        throw new Error(response.message || 'Producto no encontrado')
      }

      return {
        success: true,
        producto: response.data.producto,
        relacionados: response.data.relacionados
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al obtener producto',
        error
      }
    }
  }

  /**
   * Busca productos por término de búsqueda
   * @param {String} termino - Término de búsqueda
   * @param {Number} page - Número de página
   * @returns {Promise<Object>}
   */
  async buscarProductos(termino, page = 1) {
    try {
      if (!termino || termino.trim().length === 0) {
        throw new Error('Término de búsqueda es requerido')
      }

      return this.obtenerCatalogo({ buscar: termino }, page)
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error en la búsqueda',
        error
      }
    }
  }

  /**
   * Filtra productos por categoría
   * @param {Number} idCategoria - ID de la categoría
   * @param {Number} page - Número de página
   * @returns {Promise<Object>}
   */
  async filtrarPorCategoria(idCategoria, page = 1) {
    try {
      if (!idCategoria) {
        throw new Error('ID de categoría es requerido')
      }

      return this.obtenerCatalogo({ categoria: idCategoria }, page)
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al filtrar por categoría',
        error
      }
    }
  }

  /**
   * Filtra productos por marca
   * @param {Number} idMarca - ID de la marca
   * @param {Number} page - Número de página
   * @returns {Promise<Object>}
   */
  async filtrarPorMarca(idMarca, page = 1) {
    try {
      if (!idMarca) {
        throw new Error('ID de marca es requerido')
      }

      return this.obtenerCatalogo({ marca: idMarca }, page)
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al filtrar por marca',
        error
      }
    }
  }

  /**
   * Filtra productos por rango de precio
   * @param {Number} precioMin - Precio mínimo
   * @param {Number} precioMax - Precio máximo
   * @param {Number} page - Número de página
   * @returns {Promise<Object>}
   */
  async filtrarPorPrecio(precioMin, precioMax, page = 1) {
    try {
      const filtros = {}
      
      if (precioMin !== undefined && precioMin !== null) {
        filtros.precio_min = precioMin
      }
      if (precioMax !== undefined && precioMax !== null) {
        filtros.precio_max = precioMax
      }

      return this.obtenerCatalogo(filtros, page)
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al filtrar por precio',
        error
      }
    }
  }

  /**
   * Obtiene productos en descuento
   * @param {Number} page - Número de página
   * @returns {Promise<Object>}
   */
  async obtenerProductosEnDescuento(page = 1) {
    try {
      return this.obtenerCatalogo({ solo_descuentos: true }, page)
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al obtener productos en descuento',
        error
      }
    }
  }

  /**
   * Ordena productos por criterio especificado
   * @param {String} orden - Criterio de orden (newest, precio_asc, precio_desc, nombre)
   * @param {Number} page - Número de página
   * @returns {Promise<Object>}
   */
  async ordenarProductos(orden = 'newest', page = 1) {
    try {
      const ordenesValidas = ['newest', 'precio_asc', 'precio_desc', 'nombre']
      
      if (!ordenesValidas.includes(orden)) {
        throw new Error('Criterio de orden inválido')
      }

      return this.obtenerCatalogo({ orden }, page)
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al ordenar productos',
        error
      }
    }
  }
}

export default new ProductoController()

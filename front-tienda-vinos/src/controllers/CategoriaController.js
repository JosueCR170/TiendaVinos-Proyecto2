import { api } from '@/services/api'

class CategoriaController {
  /**
   * Obtiene todas las categorías
   * @returns {Promise<Object>}
   */
  async obtenerCategorias() {
    try {
      // Las categorías vienen incluidas en la respuesta del catálogo
      const response = await api.get('/api/catalogo?per_page=1')

      if (!response.success) {
        throw new Error(response.message || 'Error al obtener categorías')
      }

      return {
        success: true,
        categorias: response.data.filtros.categorias
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al obtener categorías',
        categorias: [],
        error
      }
    }
  }

  /**
   * Obtiene una categoría específica
   * @param {Number} idCategoria - ID de la categoría
   * @returns {Promise<Object>}
   */
  async obtenerCategoriaPorId(idCategoria) {
    try {
      if (!idCategoria) {
        throw new Error('ID de categoría es requerido')
      }

      const response = await api.get(`/api/categorias/${idCategoria}`)

      if (!response.success) {
        throw new Error(response.message || 'Categoría no encontrada')
      }

      return {
        success: true,
        categoria: response.data
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al obtener categoría',
        error
      }
    }
  }

  /**
   * Obtiene todos las marcas
   * @returns {Promise<Object>}
   */
  async obtenerMarcas() {
    try {
      // Las marcas vienen incluidas en la respuesta del catálogo
      const response = await api.get('/api/catalogo?per_page=1')

      if (!response.success) {
        throw new Error(response.message || 'Error al obtener marcas')
      }

      return {
        success: true,
        marcas: response.data.filtros.marcas
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al obtener marcas',
        marcas: [],
        error
      }
    }
  }

  /**
   * Obtiene una marca específica
   * @param {Number} idMarca - ID de la marca
   * @returns {Promise<Object>}
   */
  async obtenerMarcaPorId(idMarca) {
    try {
      if (!idMarca) {
        throw new Error('ID de marca es requerido')
      }

      const response = await api.get(`/api/marcas/${idMarca}`)

      if (!response.success) {
        throw new Error(response.message || 'Marca no encontrada')
      }

      return {
        success: true,
        marca: response.data
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al obtener marca',
        error
      }
    }
  }

  /**
   * Obtiene todas las variedades
   * @returns {Promise<Object>}
   */
  async obtenerVariedades() {
    try {
      // Las variedades vienen incluidas en la respuesta del catálogo
      const response = await api.get('/api/catalogo?per_page=1')

      if (!response.success) {
        throw new Error(response.message || 'Error al obtener variedades')
      }

      return {
        success: true,
        variedades: response.data.filtros.variedades
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al obtener variedades',
        variedades: [],
        error
      }
    }
  }

  /**
   * Obtiene una variedad específica
   * @param {Number} idVariedad - ID de la variedad
   * @returns {Promise<Object>}
   */
  async obtenerVariedadPorId(idVariedad) {
    try {
      if (!idVariedad) {
        throw new Error('ID de variedad es requerido')
      }

      const response = await api.get(`/api/variedades/${idVariedad}`)

      if (!response.success) {
        throw new Error(response.message || 'Variedad no encontrada')
      }

      return {
        success: true,
        variedad: response.data
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al obtener variedad',
        error
      }
    }
  }

  /**
   * Obtiene los filtros disponibles (categorías, marcas, variedades, países)
   * @returns {Promise<Object>}
   */
  async obtenerFiltrosDisponibles() {
    try {
      const response = await api.get('/api/catalogo?per_page=1')

      if (!response.success) {
        throw new Error(response.message || 'Error al obtener filtros')
      }

      return {
        success: true,
        categorias: response.data.filtros.categorias,
        marcas: response.data.filtros.marcas,
        variedades: response.data.filtros.variedades,
        paises: response.data.filtros.paises
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al obtener filtros',
        categorias: [],
        marcas: [],
        variedades: [],
        paises: [],
        error
      }
    }
  }

  /**
   * Obtiene los países disponibles
   * @returns {Promise<Object>}
   */
  async obtenerPaises() {
    try {
      const response = await api.get('/api/catalogo?per_page=1')

      if (!response.success) {
        throw new Error(response.message || 'Error al obtener países')
      }

      return {
        success: true,
        paises: response.data.filtros.paises
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al obtener países',
        paises: [],
        error
      }
    }
  }

  /**
   * Filtra categorías por nombre
   * @param {Array} categorias - Array de categorías
   * @param {String} termino - Término de búsqueda
   * @returns {Array}
   */
  filtrarCategoriasPorNombre(categorias, termino) {
    if (!termino || termino.trim().length === 0) {
      return categorias
    }

    const terminoLower = termino.toLowerCase()
    return categorias.filter(cat => 
      cat.nombre.toLowerCase().includes(terminoLower)
    )
  }

  /**
   * Filtra marcas por nombre
   * @param {Array} marcas - Array de marcas
   * @param {String} termino - Término de búsqueda
   * @returns {Array}
   */
  filtrarMarcasPorNombre(marcas, termino) {
    if (!termino || termino.trim().length === 0) {
      return marcas
    }

    const terminoLower = termino.toLowerCase()
    return marcas.filter(marca => 
      marca.nombre.toLowerCase().includes(terminoLower)
    )
  }

  /**
   * Filtra variedades por nombre
   * @param {Array} variedades - Array de variedades
   * @param {String} termino - Término de búsqueda
   * @returns {Array}
   */
  filtrarVariedadesPorNombre(variedades, termino) {
    if (!termino || termino.trim().length === 0) {
      return variedades
    }

    const terminoLower = termino.toLowerCase()
    return variedades.filter(var_item => 
      var_item.nombre.toLowerCase().includes(terminoLower)
    )
  }

  /**
   * Agrupa categorías por tipo
   * @param {Array} categorias - Array de categorías
   * @returns {Object}
   */
  agruparCategoriasPorTipo(categorias) {
    const agrupadas = {}
    
    categorias.forEach(categoria => {
      const tipo = categoria.tipo || 'general'
      if (!agrupadas[tipo]) {
        agrupadas[tipo] = []
      }
      agrupadas[tipo].push(categoria)
    })

    return agrupadas
  }

  /**
   * Ordena marcas alfabéticamente
   * @param {Array} marcas - Array de marcas
   * @returns {Array}
   */
  ordenarMarcasAlfabeticamente(marcas) {
    return [...marcas].sort((a, b) => 
      a.nombre.localeCompare(b.nombre)
    )
  }

  /**
   * Ordena variedades alfabéticamente
   * @param {Array} variedades - Array de variedades
   * @returns {Array}
   */
  ordenarVariedadesAlfabeticamente(variedades) {
    return [...variedades].sort((a, b) => 
      a.nombre.localeCompare(b.nombre)
    )
  }
}

export default new CategoriaController()

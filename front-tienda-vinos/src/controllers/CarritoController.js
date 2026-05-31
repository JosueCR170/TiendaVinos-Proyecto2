import { api } from '@/services/api'

class CarritoController {
  /**
   * Agregar un producto al carrito
   * @param {Number} idProducto - ID del producto
   * @param {Number} cantidad - Cantidad a agregar (default: 1)
   * @returns {Promise<Object>}
   */
  async agregarProducto(idProducto, cantidad = 1) {
    try {
      if (!idProducto) {
        throw new Error('ID de producto es requerido')
      }

      if (cantidad < 1) {
        throw new Error('La cantidad debe ser mayor a 0')
      }

      const response = await api.post(`/api/carrito/agregar/${idProducto}`, {
        cantidad
      })

      if (!response.success) {
        throw new Error(response.message || 'Error al agregar producto')
      }

      return {
        success: true,
        message: response.message,
        carritoCount: response.carrito_count,
        carritoTotal: response.carrito_total
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al agregar producto al carrito',
        error
      }
    }
  }

  /**
   * Obtiene el carrito actual
   * @returns {Promise<Object>}
   */
  async obtenerCarrito() {
    try {
      const response = await api.get('/api/carrito')

      if (!response.success) {
        throw new Error(response.message || 'Error al obtener carrito')
      }

      return {
        success: true,
        items: response.data.items,
        cantidadItems: response.data.cantidad_items,
        total: response.data.total
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al obtener carrito',
        items: [],
        cantidadItems: 0,
        total: 0,
        error
      }
    }
  }

  /**
   * Actualiza la cantidad de un producto en el carrito
   * @param {Number} idProducto - ID del producto
   * @param {Number} cantidad - Nueva cantidad (o usar accion)
   * @param {String} accion - 'incrementar' o 'decrementar'
   * @returns {Promise<Object>}
   */
  async actualizarProducto(idProducto, cantidad = null, accion = null) {
    try {
      if (!idProducto) {
        throw new Error('ID de producto es requerido')
      }

      const data = {}
      
      if (cantidad !== null && cantidad !== undefined) {
        data.cantidad = cantidad
      } else if (accion) {
        data.accion = accion
      } else {
        throw new Error('Debe proporcionar cantidad o acción')
      }

      const response = await api.post(`/api/carrito/actualizar/${idProducto}`, data)

      if (!response.success) {
        throw new Error(response.message || 'Error al actualizar producto')
      }

      return {
        success: true,
        message: response.message,
        item: response.item,
        carritoTotal: response.carrito_total
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al actualizar producto en carrito',
        error
      }
    }
  }

  /**
   * Incrementa la cantidad de un producto
   * @param {Number} idProducto - ID del producto
   * @returns {Promise<Object>}
   */
  async incrementarProducto(idProducto) {
    return this.actualizarProducto(idProducto, null, 'incrementar')
  }

  /**
   * Decrementa la cantidad de un producto
   * @param {Number} idProducto - ID del producto
   * @returns {Promise<Object>}
   */
  async decrementarProducto(idProducto) {
    return this.actualizarProducto(idProducto, null, 'decrementar')
  }

  /**
   * Establece la cantidad exacta de un producto
   * @param {Number} idProducto - ID del producto
   * @param {Number} cantidad - Cantidad exacta
   * @returns {Promise<Object>}
   */
  async establecerCantidad(idProducto, cantidad) {
    return this.actualizarProducto(idProducto, cantidad)
  }

  /**
   * Elimina un producto del carrito
   * @param {Number} idProducto - ID del producto
   * @returns {Promise<Object>}
   */
  async eliminarProducto(idProducto) {
    try {
      if (!idProducto) {
        throw new Error('ID de producto es requerido')
      }

      const response = await api.delete(`/api/carrito/eliminar/${idProducto}`)

      if (!response.success) {
        throw new Error(response.message || 'Error al eliminar producto')
      }

      return {
        success: true,
        message: response.message,
        carritoTotal: response.carrito_total
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al eliminar producto del carrito',
        error
      }
    }
  }

  /**
   * Limpia completamente el carrito
   * @returns {Promise<Object>}
   */
  async limpiarCarrito() {
    try {
      const response = await api.post('/api/carrito/limpiar')

      if (!response.success) {
        throw new Error(response.message || 'Error al limpiar carrito')
      }

      return {
        success: true,
        message: response.message
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al limpiar carrito',
        error
      }
    }
  }

  /**
   * Calcula el total del carrito con descuentos
   * @param {Array} items - Array de items del carrito
   * @returns {Number}
   */
  calcularTotal(items) {
    return items.reduce((total, item) => {
      return total + (item.precio * item.cantidad)
    }, 0)
  }

  /**
   * Calcula el descuento total
   * @param {Array} items - Array de items del carrito
   * @returns {Number}
   */
  calcularDescuentoTotal(items) {
    return items.reduce((total, item) => {
      const descuentoItem = (item.precio_original - item.precio) * item.cantidad
      return total + descuentoItem
    }, 0)
  }

  /**
   * Obtiene el subtotal sin descuentos
   * @param {Array} items - Array de items del carrito
   * @returns {Number}
   */
  calcularSubtotal(items) {
    return items.reduce((total, item) => {
      return total + (item.precio_original * item.cantidad)
    }, 0)
  }
}

export default new CarritoController()

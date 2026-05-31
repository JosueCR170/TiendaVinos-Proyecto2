import { api } from '@/services/api'
import CarritoController from './CarritoController'

class PedidoController {
  /**
   * Obtiene los datos necesarios para el checkout
   * @returns {Promise<Object>}
   */
  async obtenerCheckout() {
    try {
      const response = await api.get('/api/checkout')

      if (!response.success) {
        throw new Error(response.message || 'Error al obtener datos del checkout')
      }

      return {
        success: true,
        carrito: response.data.carrito,
        total: response.data.total
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al obtener checkout',
        error
      }
    }
  }

  /**
   * Procesa el pago del pedido
   * @param {Object} datosCliente - Datos del cliente (nombre, email, dirección, etc)
   * @returns {Promise<Object>}
   */
  async procesarPago(datosCliente = {}) {
    try {
      const carrito = await CarritoController.obtenerCarrito()

      if (!carrito.success || carrito.cantidadItems === 0) {
        throw new Error('El carrito está vacío')
      }

      const response = await api.post('/api/procesar-pago', {
        cliente: datosCliente,
        carrito: carrito.items
      })

      if (!response.success) {
        throw new Error(response.message || 'Error al procesar el pago')
      }

      return {
        success: true,
        message: response.message,
        total: response.data.total,
        itemsComprados: response.data.items_comprados,
        ordenId: response.data.orden_id
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al procesar el pago',
        error
      }
    }
  }

  /**
   * Valida los datos del cliente
   * @param {Object} datos - Objeto con datos del cliente
   * @returns {Object} - Objeto con validación y errores
   */
  validarDatosCliente(datos) {
    const errores = {}

    if (!datos.nombre || datos.nombre.trim().length === 0) {
      errores.nombre = 'El nombre es requerido'
    }

    if (!datos.email || datos.email.trim().length === 0) {
      errores.email = 'El email es requerido'
    } else if (!this.validarEmail(datos.email)) {
      errores.email = 'El email no es válido'
    }

    if (!datos.telefono || datos.telefono.trim().length === 0) {
      errores.telefono = 'El teléfono es requerido'
    }

    if (!datos.direccion || datos.direccion.trim().length === 0) {
      errores.direccion = 'La dirección es requerida'
    }

    if (!datos.ciudad || datos.ciudad.trim().length === 0) {
      errores.ciudad = 'La ciudad es requerida'
    }

    if (!datos.codigoPostal || datos.codigoPostal.toString().trim().length === 0) {
      errores.codigoPostal = 'El código postal es requerido'
    }

    return {
      valido: Object.keys(errores).length === 0,
      errores
    }
  }

  /**
   * Valida formato de email
   * @param {String} email - Email a validar
   * @returns {Boolean}
   */
  validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return regex.test(email)
  }

  /**
   * Obtiene un resumen del pedido
   * @param {Array} items - Items del carrito
   * @returns {Object}
   */
  obtenerResumenPedido(items) {
    const subtotal = CarritoController.calcularSubtotal(items)
    const descuento = CarritoController.calcularDescuentoTotal(items)
    const total = CarritoController.calcularTotal(items)

    return {
      cantidad_items: items.length,
      subtotal: subtotal,
      descuento: descuento,
      total: total,
      items: items
    }
  }

  /**
   * Calcula costos de envío
   * @param {String} pais - País de envío
   * @param {Number} monto - Monto del pedido
   * @returns {Number}
   */
  calcularCostoEnvio(pais, monto) {
    // Ejemplo simple de cálculo de envío
    const costosBase = {
      'Costa Rica': 5,
      'Panama': 10,
      'Colombia': 15,
      'Mexico': 20
    }

    const costoBase = costosBase[pais] || 25

    // Envío gratis si el monto es mayor a $100
    if (monto > 100) {
      return 0
    }

    return costoBase
  }

  /**
   * Aplica un código de descuento (para futura implementación)
   * @param {String} codigo - Código de descuento
   * @param {Number} monto - Monto base
   * @returns {Promise<Object>}
   */
  async aplicarCodigoDescuento(codigo, monto) {
    try {
      if (!codigo || codigo.trim().length === 0) {
        throw new Error('Código de descuento es requerido')
      }

      // Aquí iría la validación con el backend
      const response = await api.post('/api/aplicar-descuento', {
        codigo,
        monto
      })

      if (!response.success) {
        throw new Error(response.message || 'Código de descuento inválido')
      }

      return {
        success: true,
        porcentaje: response.porcentaje,
        monto_descuento: response.monto_descuento,
        monto_final: response.monto_final
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al aplicar código de descuento',
        error
      }
    }
  }

  /**
   * Obtiene métodos de pago disponibles
   * @returns {Promise<Object>}
   */
  async obtenerMetodosPago() {
    try {
      const metodos = [
        {
          id: 'tarjeta_credito',
          nombre: 'Tarjeta de Crédito',
          icono: 'credit-card',
          disponible: true
        },
        {
          id: 'tarjeta_debito',
          nombre: 'Tarjeta de Débito',
          icono: 'debit-card',
          disponible: true
        },
        {
          id: 'paypal',
          nombre: 'PayPal',
          icono: 'paypal',
          disponible: true
        },
        {
          id: 'transferencia',
          nombre: 'Transferencia Bancaria',
          icono: 'bank',
          disponible: false
        }
      ]

      return {
        success: true,
        metodos
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al obtener métodos de pago',
        error
      }
    }
  }

  /**
   * Obtiene el estado de un pedido
   * @param {String} ordenId - ID del pedido
   * @returns {Promise<Object>}
   */
  async obtenerEstadoPedido(ordenId) {
    try {
      if (!ordenId) {
        throw new Error('ID de orden es requerido')
      }

      const response = await api.get(`/api/pedidos/${ordenId}`)

      if (!response.success) {
        throw new Error(response.message || 'Pedido no encontrado')
      }

      return {
        success: true,
        pedido: response.data
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al obtener estado del pedido',
        error
      }
    }
  }

  /**
   * Cancela un pedido (si es posible)
   * @param {String} ordenId - ID del pedido
   * @returns {Promise<Object>}
   */
  async cancelarPedido(ordenId) {
    try {
      if (!ordenId) {
        throw new Error('ID de orden es requerido')
      }

      const response = await api.post(`/api/pedidos/${ordenId}/cancelar`)

      if (!response.success) {
        throw new Error(response.message || 'Error al cancelar pedido')
      }

      return {
        success: true,
        message: response.message
      }
    } catch (error) {
      return {
        success: false,
        message: error.message || 'Error al cancelar pedido',
        error
      }
    }
  }
}

export default new PedidoController()

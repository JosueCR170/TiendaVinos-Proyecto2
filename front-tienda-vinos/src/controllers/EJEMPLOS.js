/**
 * EJEMPLOS DE USO DE CONTROLLERS
 * 
 * Este archivo contiene ejemplos de cómo usar los controllers en componentes Vue
 */

// ============================================================================
// EJEMPLO 1: Componente para listar productos
// ============================================================================

/*
<template>
  <div class="productos-container">
    <h1>Catálogo de Productos</h1>
    
    <div v-if="cargando" class="loading">Cargando productos...</div>
    <div v-if="error" class="error">{{ error }}</div>
    
    <div v-if="!cargando && productos.length > 0" class="productos-grid">
      <div v-for="producto in productos" :key="producto.id" class="producto-card">
        <img :src="producto.imagen" :alt="producto.nombre">
        <h3>{{ producto.nombre }}</h3>
        <p class="precio">${{ producto.precio }}</p>
        <button @click="agregarAlCarrito(producto.id)">Agregar</button>
      </div>
    </div>
  </div>
</template>

<script>
import { ProductoController, CarritoController } from '@/controllers'

export default {
  name: 'ProductosView',
  data() {
    return {
      productos: [],
      cargando: false,
      error: null
    }
  },
  
  async mounted() {
    await this.cargarProductos()
  },
  
  methods: {
    async cargarProductos() {
      this.cargando = true
      const resultado = await ProductoController.obtenerCatalogo()
      
      if (resultado.success) {
        this.productos = resultado.productos
      } else {
        this.error = resultado.message
      }
      
      this.cargando = false
    },
    
    async agregarAlCarrito(idProducto) {
      const resultado = await CarritoController.agregarProducto(idProducto, 1)
      
      if (resultado.success) {
        alert(resultado.message)
      } else {
        alert(resultado.message)
      }
    }
  }
}
</script>
*/

// ============================================================================
// EJEMPLO 2: Búsqueda y filtros de productos
// ============================================================================

/*
<template>
  <div class="busqueda-container">
    <input 
      v-model="terminoBusqueda"
      @keyup.enter="buscar"
      placeholder="Buscar productos..."
    >
    
    <select v-model="categoriaSeleccionada" @change="filtrarPorCategoria">
      <option value="">Todas las categorías</option>
      <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
        {{ cat.nombre }}
      </option>
    </select>
    
    <select v-model="ordenSeleccionado" @change="aplicarOrden">
      <option value="newest">Más recientes</option>
      <option value="precio_asc">Precio menor a mayor</option>
      <option value="precio_desc">Precio mayor a menor</option>
      <option value="nombre">Nombre A-Z</option>
    </select>
    
    <div class="productos-lista">
      <div v-for="producto in productos" :key="producto.id">
        {{ producto.nombre }} - ${{ producto.precio }}
      </div>
    </div>
  </div>
</template>

<script>
import { ProductoController, CategoriaController } from '@/controllers'

export default {
  data() {
    return {
      productos: [],
      categorias: [],
      terminoBusqueda: '',
      categoriaSeleccionada: '',
      ordenSeleccionado: 'newest'
    }
  },
  
  async mounted() {
    await this.cargarCategorias()
    await this.cargarProductos()
  },
  
  methods: {
    async cargarCategorias() {
      const resultado = await CategoriaController.obtenerCategorias()
      if (resultado.success) {
        this.categorias = resultado.categorias
      }
    },
    
    async cargarProductos() {
      const resultado = await ProductoController.obtenerCatalogo()
      if (resultado.success) {
        this.productos = resultado.productos
      }
    },
    
    async buscar() {
      if (this.terminoBusqueda.trim().length === 0) return
      
      const resultado = await ProductoController.buscarProductos(
        this.terminoBusqueda
      )
      if (resultado.success) {
        this.productos = resultado.productos
      }
    },
    
    async filtrarPorCategoria() {
      if (!this.categoriaSeleccionada) {
        await this.cargarProductos()
        return
      }
      
      const resultado = await ProductoController.filtrarPorCategoria(
        this.categoriaSeleccionada
      )
      if (resultado.success) {
        this.productos = resultado.productos
      }
    },
    
    async aplicarOrden() {
      const resultado = await ProductoController.ordenarProductos(
        this.ordenSeleccionado
      )
      if (resultado.success) {
        this.productos = resultado.productos
      }
    }
  }
}
</script>
*/

// ============================================================================
// EJEMPLO 3: Carrito de compras
// ============================================================================

/*
<template>
  <div class="carrito-container">
    <h2>Mi Carrito</h2>
    
    <div v-if="items.length === 0" class="carrito-vacio">
      El carrito está vacío
    </div>
    
    <div v-else>
      <table class="carrito-tabla">
        <thead>
          <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, idProducto) in items" :key="idProducto">
            <td>{{ item.nombre }}</td>
            <td>${{ item.precio }}</td>
            <td>
              <button @click="decrementar(idProducto)">-</button>
              {{ item.cantidad }}
              <button @click="incrementar(idProducto)">+</button>
            </td>
            <td>${{ (item.precio * item.cantidad).toFixed(2) }}</td>
            <td>
              <button @click="eliminar(idProducto)">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div class="carrito-resumen">
        <h3>Total: ${{ total.toFixed(2) }}</h3>
        <button @click="irAlCheckout">Proceder al Pago</button>
        <button @click="seguirComprando">Seguir Comprando</button>
      </div>
    </div>
  </div>
</template>

<script>
import { CarritoController } from '@/controllers'

export default {
  data() {
    return {
      items: {},
      total: 0
    }
  },
  
  async mounted() {
    await this.cargarCarrito()
  },
  
  methods: {
    async cargarCarrito() {
      const resultado = await CarritoController.obtenerCarrito()
      if (resultado.success) {
        this.items = resultado.items
        this.total = resultado.total
      }
    },
    
    async incrementar(idProducto) {
      const resultado = await CarritoController.incrementarProducto(idProducto)
      if (resultado.success) {
        await this.cargarCarrito()
      }
    },
    
    async decrementar(idProducto) {
      const resultado = await CarritoController.decrementarProducto(idProducto)
      if (resultado.success) {
        await this.cargarCarrito()
      }
    },
    
    async eliminar(idProducto) {
      if (confirm('¿Desea eliminar este producto?')) {
        const resultado = await CarritoController.eliminarProducto(idProducto)
        if (resultado.success) {
          await this.cargarCarrito()
        }
      }
    },
    
    irAlCheckout() {
      this.$router.push('/checkout')
    },
    
    seguirComprando() {
      this.$router.push('/catalogo')
    }
  }
}
</script>
*/

// ============================================================================
// EJEMPLO 4: Página de checkout
// ============================================================================

/*
<template>
  <div class="checkout-container">
    <h2>Checkout</h2>
    
    <form @submit.prevent="procesarPago">
      <div class="seccion-datos">
        <h3>Datos del Cliente</h3>
        
        <input 
          v-model="cliente.nombre"
          placeholder="Nombre completo"
          type="text"
          required
        >
        <span v-if="erroresValidacion.nombre" class="error">
          {{ erroresValidacion.nombre }}
        </span>
        
        <input 
          v-model="cliente.email"
          placeholder="Email"
          type="email"
          required
        >
        <span v-if="erroresValidacion.email" class="error">
          {{ erroresValidacion.email }}
        </span>
        
        <input 
          v-model="cliente.telefono"
          placeholder="Teléfono"
          type="tel"
          required
        >
        <input 
          v-model="cliente.direccion"
          placeholder="Dirección"
          type="text"
          required
        >
        <input 
          v-model="cliente.ciudad"
          placeholder="Ciudad"
          type="text"
          required
        >
        <input 
          v-model="cliente.codigoPostal"
          placeholder="Código postal"
          type="text"
          required
        >
      </div>
      
      <div class="seccion-metodo">
        <h3>Método de Pago</h3>
        <div v-for="metodo in metodosPago" :key="metodo.id">
          <input 
            v-model="metodoPagoSeleccionado"
            :value="metodo.id"
            type="radio"
            :disabled="!metodo.disponible"
          >
          <label>{{ metodo.nombre }}</label>
        </div>
      </div>
      
      <div class="resumen-pedido">
        <h3>Resumen del Pedido</h3>
        <p>Subtotal: ${{ resumen.subtotal.toFixed(2) }}</p>
        <p v-if="resumen.descuento > 0">
          Descuento: -${{ resumen.descuento.toFixed(2) }}
        </p>
        <p>Envío: ${{ costoEnvio.toFixed(2) }}</p>
        <h4>Total: ${{ (resumen.total + costoEnvio).toFixed(2) }}</h4>
      </div>
      
      <button type="submit" :disabled="cargando">
        {{ cargando ? 'Procesando...' : 'Completar Compra' }}
      </button>
    </form>
  </div>
</template>

<script>
import { PedidoController, CarritoController } from '@/controllers'

export default {
  data() {
    return {
      cliente: {
        nombre: '',
        email: '',
        telefono: '',
        direccion: '',
        ciudad: '',
        codigoPostal: ''
      },
      resumen: {},
      metodosPago: [],
      metodoPagoSeleccionado: 'tarjeta_credito',
      erroresValidacion: {},
      cargando: false,
      costoEnvio: 0
    }
  },
  
  async mounted() {
    await this.cargarDatos()
  },
  
  methods: {
    async cargarDatos() {
      // Obtener carrito
      const carrito = await CarritoController.obtenerCarrito()
      if (carrito.success) {
        this.resumen = PedidoController.obtenerResumenPedido(
          Object.values(carrito.items)
        )
        this.costoEnvio = PedidoController.calcularCostoEnvio(
          'Costa Rica',
          this.resumen.total
        )
      }
      
      // Obtener métodos de pago
      const metodos = await PedidoController.obtenerMetodosPago()
      if (metodos.success) {
        this.metodosPago = metodos.metodos
      }
    },
    
    async procesarPago() {
      // Validar datos
      const validacion = PedidoController.validarDatosCliente(this.cliente)
      
      if (!validacion.valido) {
        this.erroresValidacion = validacion.errores
        return
      }
      
      this.cargando = true
      
      // Procesar pago
      const resultado = await PedidoController.procesarPago(this.cliente)
      
      if (resultado.success) {
        alert('¡Compra realizada exitosamente!')
        this.$router.push('/gracias')
      } else {
        alert('Error al procesar el pago: ' + resultado.message)
      }
      
      this.cargando = false
    }
  }
}
</script>
*/

// ============================================================================
// EJEMPLO 5: Página de detalles de producto
// ============================================================================

/*
<template>
  <div class="producto-detalle">
    <div v-if="cargando">Cargando...</div>
    <div v-else>
      <img :src="producto.imagen" :alt="producto.nombre">
      <h1>{{ producto.nombre }}</h1>
      
      <div class="informacion">
        <p><strong>Marca:</strong> {{ producto.marca.nombre }}</p>
        <p><strong>País:</strong> {{ producto.pais }}</p>
        <p><strong>Región:</strong> {{ producto.region }}</p>
        <p><strong>Año Cosecha:</strong> {{ producto.anio_cosecha }}</p>
        <p><strong>Alcohol:</strong> {{ producto.alcohol }}%</p>
        <p><strong>Contenido:</strong> {{ producto.contenido }} ml</p>
      </div>
      
      <p class="descripcion">{{ producto.descripcion }}</p>
      
      <div class="compra">
        <div class="precio">
          <span v-if="producto.descuento > 0" class="descuento">
            ${{ producto.precio }}
          </span>
          <span class="precio-final">
            ${{ precioFinal }}
          </span>
          <span v-if="producto.descuento > 0" class="etiqueta-descuento">
            -{{ producto.descuento }}%
          </span>
        </div>
        
        <button @click="agregarAlCarrito">
          Agregar al Carrito
        </button>
      </div>
      
      <div class="productos-relacionados">
        <h3>Productos Relacionados</h3>
        <div v-for="related in relacionados" :key="related.id">
          <router-link :to="`/producto/${related.id}`">
            {{ related.nombre }}
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ProductoController, CarritoController } from '@/controllers'

export default {
  data() {
    return {
      producto: {},
      relacionados: [],
      cargando: false
    }
  },
  
  computed: {
    precioFinal() {
      return (
        this.producto.precio * (1 - this.producto.descuento / 100)
      ).toFixed(2)
    }
  },
  
  async mounted() {
    const idProducto = this.$route.params.id
    await this.cargarProducto(idProducto)
  },
  
  methods: {
    async cargarProducto(id) {
      this.cargando = true
      const resultado = await ProductoController.obtenerProductoPorId(id)
      
      if (resultado.success) {
        this.producto = resultado.producto
        this.relacionados = resultado.relacionados
      }
      
      this.cargando = false
    },
    
    async agregarAlCarrito() {
      const resultado = await CarritoController.agregarProducto(
        this.producto.id,
        1
      )
      
      if (resultado.success) {
        alert('Producto agregado al carrito')
        this.$router.push('/carrito')
      }
    }
  }
}
</script>
*/

export default {}

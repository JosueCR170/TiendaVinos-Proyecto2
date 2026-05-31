# Controllers - Documentación

Este directorio contiene los controllers que manejan la comunicación con la API del backend. Cada controller encapsula la lógica de negocio para una sección específica de la aplicación.

## Estructura

```
controllers/
├── ProductoController.js      # Manejo de productos y catálogo
├── CarritoController.js       # Manejo del carrito de compras
├── PedidoController.js        # Manejo de pedidos y checkout
├── CategoriaController.js     # Manejo de categorías, marcas y variedades
├── index.js                   # Exportaciones centralizadas
└── README.md                  # Este archivo
```

## Controllers Disponibles

### 1. ProductoController
Gestiona la obtención y filtrado de productos.

**Métodos principales:**
- `obtenerCatalogo(filtros, page, perPage)` - Obtiene todos los productos con paginación
- `obtenerProductosDestacados()` - Obtiene productos destacados de la portada
- `obtenerProductoPorId(id)` - Obtiene detalles de un producto específico
- `buscarProductos(termino, page)` - Busca productos por término
- `filtrarPorCategoria(idCategoria, page)` - Filtra por categoría
- `filtrarPorMarca(idMarca, page)` - Filtra por marca
- `filtrarPorPrecio(precioMin, precioMax, page)` - Filtra por rango de precio
- `obtenerProductosEnDescuento(page)` - Obtiene productos en descuento
- `ordenarProductos(orden, page)` - Ordena productos (newest, precio_asc, precio_desc, nombre)

**Ejemplo de uso:**
```javascript
import { ProductoController } from '@/controllers'

// Obtener catálogo
const resultado = await ProductoController.obtenerCatalogo({}, 1, 12)
if (resultado.success) {
  console.log(resultado.productos)
  console.log(resultado.pagination)
}

// Buscar productos
const busqueda = await ProductoController.buscarProductos('Malbec')

// Filtrar por precio
const filtrado = await ProductoController.filtrarPorPrecio(10, 50)
```

### 2. CarritoController
Gestiona las operaciones del carrito de compras.

**Métodos principales:**
- `agregarProducto(idProducto, cantidad)` - Agregar producto al carrito
- `obtenerCarrito()` - Obtener el carrito actual
- `actualizarProducto(idProducto, cantidad, accion)` - Actualizar cantidad de un producto
- `incrementarProducto(idProducto)` - Incrementar cantidad
- `decrementarProducto(idProducto)` - Decrementar cantidad
- `establecerCantidad(idProducto, cantidad)` - Establecer cantidad exacta
- `eliminarProducto(idProducto)` - Eliminar producto del carrito
- `limpiarCarrito()` - Vaciar completamente el carrito
- `calcularTotal(items)` - Calcular total con descuentos
- `calcularDescuentoTotal(items)` - Calcular monto total en descuentos
- `calcularSubtotal(items)` - Calcular subtotal sin descuentos

**Ejemplo de uso:**
```javascript
import { CarritoController } from '@/controllers'

// Agregar producto
const agregar = await CarritoController.agregarProducto(5, 2)

// Obtener carrito
const carrito = await CarritoController.obtenerCarrito()
if (carrito.success) {
  console.log(carrito.items)
  console.log(carrito.total)
}

// Actualizar cantidad
await CarritoController.incrementarProducto(5)

// Eliminar producto
await CarritoController.eliminarProducto(5)
```

### 3. PedidoController
Gestiona el proceso de checkout y pagos.

**Métodos principales:**
- `obtenerCheckout()` - Obtener datos para el checkout
- `procesarPago(datosCliente)` - Procesar el pago
- `validarDatosCliente(datos)` - Validar datos del cliente
- `obtenerResumenPedido(items)` - Obtener resumen del pedido
- `calcularCostoEnvio(pais, monto)` - Calcular costo de envío
- `aplicarCodigoDescuento(codigo, monto)` - Aplicar código de descuento
- `obtenerMetodosPago()` - Obtener métodos de pago disponibles
- `obtenerEstadoPedido(ordenId)` - Obtener estado de un pedido
- `cancelarPedido(ordenId)` - Cancelar un pedido

**Ejemplo de uso:**
```javascript
import { PedidoController } from '@/controllers'

// Validar datos del cliente
const validacion = PedidoController.validarDatosCliente({
  nombre: 'Juan Pérez',
  email: 'juan@ejemplo.com',
  telefono: '+506 1234 5678',
  direccion: 'Calle Principal 123',
  ciudad: 'San José',
  codigoPostal: '10101'
})

if (validacion.valido) {
  // Procesar pago
  const pago = await PedidoController.procesarPago(validacion.datos)
}

// Obtener métodos de pago
const metodos = await PedidoController.obtenerMetodosPago()

// Calcular costo de envío
const envio = PedidoController.calcularCostoEnvio('Costa Rica', 150)
```

### 4. CategoriaController
Gestiona categorías, marcas, variedades y otros filtros.

**Métodos principales:**
- `obtenerCategorias()` - Obtener todas las categorías
- `obtenerCategoriaPorId(idCategoria)` - Obtener categoría específica
- `obtenerMarcas()` - Obtener todas las marcas
- `obtenerMarcaPorId(idMarca)` - Obtener marca específica
- `obtenerVariedades()` - Obtener todas las variedades
- `obtenerVariedadPorId(idVariedad)` - Obtener variedad específica
- `obtenerFiltrosDisponibles()` - Obtener todos los filtros disponibles
- `obtenerPaises()` - Obtener países disponibles
- `filtrarCategoriasPorNombre(categorias, termino)` - Filtrar categorías por nombre
- `agruparCategoriasPorTipo(categorias)` - Agrupar categorías por tipo
- `ordenarMarcasAlfabeticamente(marcas)` - Ordenar marcas alfabéticamente

**Ejemplo de uso:**
```javascript
import { CategoriaController } from '@/controllers'

// Obtener filtros disponibles
const filtros = await CategoriaController.obtenerFiltrosDisponibles()
console.log(filtros.categorias)
console.log(filtros.marcas)
console.log(filtros.paises)

// Filtrar categorías por nombre
const categoriasFiltradas = CategoriaController.filtrarCategoriasPorNombre(
  filtros.categorias,
  'vino'
)
```

## Importación en Componentes

### Opción 1: Importar controllers individuales
```javascript
import ProductoController from '@/controllers/ProductoController'
import CarritoController from '@/controllers/CarritoController'

export default {
  methods: {
    async cargarProductos() {
      const resultado = await ProductoController.obtenerCatalogo()
      this.productos = resultado.productos
    }
  }
}
```

### Opción 2: Importar desde index centralizado
```javascript
import { ProductoController, CarritoController } from '@/controllers'

export default {
  methods: {
    async cargarProductos() {
      const resultado = await ProductoController.obtenerCatalogo()
      this.productos = resultado.productos
    }
  }
}
```

## Manejo de Respuestas

Todos los controllers retornan objetos con la siguiente estructura:

```javascript
{
  success: boolean,           // Indica si la operación fue exitosa
  message: string,            // Mensaje descriptivo (si aplica)
  data: object|array,         // Datos devueltos por la API
  error: Error                // Objeto de error (si hubo error)
}
```

**Ejemplo de manejo:**
```javascript
const resultado = await ProductoController.obtenerCatalogo()

if (resultado.success) {
  // Hacer algo con los datos
  console.log(resultado.productos)
} else {
  // Manejar el error
  console.error(resultado.message)
  console.error(resultado.error)
}
```

## Integración con Estado (State Management)

Los controllers pueden integrarse fácilmente con un sistema de estado (Pinia, Vuex, etc):

```javascript
// store/productos.js
import { ProductoController } from '@/controllers'
import { defineStore } from 'pinia'

export const useProductosStore = defineStore('productos', {
  state: () => ({
    productos: [],
    loading: false,
    error: null
  }),
  
  actions: {
    async cargarCatalogo() {
      this.loading = true
      const resultado = await ProductoController.obtenerCatalogo()
      
      if (resultado.success) {
        this.productos = resultado.productos
        this.error = null
      } else {
        this.error = resultado.message
      }
      
      this.loading = false
    }
  }
})
```

## Notas Importantes

1. **Todos los métodos async** - Todos los métodos que hacen peticiones retornan Promesas
2. **Validación de entrada** - Los controllers validan inputs y lanzan errores descriptivos
3. **Manejo de errores** - Todos los errores se capturan y retornan en la estructura de respuesta
4. **Singleton pattern** - Los controllers se exportan como instancias únicas (singleton)
5. **API base** - Usa la variable `VITE_API_URL` para la URL base de la API

## Configuración de Variables de Entorno

En tu archivo `.env` o `.env.local`:

```
VITE_API_URL=http://localhost:8000
```

## Troubleshooting

### Error: "CORS policy"
Asegúrate de que el backend está configurado para permitir CORS desde la URL del frontend.

### Error: "API Error: 404"
Verifica que las rutas en el backend coincidan con las rutas llamadas en los controllers.

### Carrito vacío después de recargar la página
El carrito se almacena en sesión del servidor. Para persistencia, considera usar localStorage o un sistema de estado.

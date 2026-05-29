# Migración de Blade a Vue.js - La Última Botella

## 📋 Resumen de Cambios

Se ha completado la migración de todos los templates Blade de Laravel al nuevo proyecto Vue.js. Los siguientes archivos fueron convertidos:

### ✅ Frontend Views (Completadas)
- **HomePage.vue** - Inicio con hero, productos destacados y ofertas (home.blade.php)
- **CatalogPage.vue** - Catálogo con filtros avanzados (catalogo.blade.php)
- **ProductPage.vue** - Detalle del producto (producto.blade.php)
- **CartPage.vue** - Carrito de compras (carrito.blade.php)
- **CheckoutPage.vue** - Factura y checkout (checkout.blade.php)
- **AboutPage.vue** - Página de historia (about.blade.php)

### ✅ Layouts (Completadas)
- **AppLayout.vue** - Layout principal con navbar y footer (app.blade.php)

### 📁 Estructura de Directorios

```
src/
├── layouts/
│   └── AppLayout.vue          # Layout base
├── views/
│   ├── frontend/
│   │   ├── HomePage.vue
│   │   ├── CatalogPage.vue
│   │   ├── ProductPage.vue
│   │   ├── CartPage.vue
│   │   ├── CheckoutPage.vue
│   │   └── AboutPage.vue
│   └── admin/                 # Pendiente
├── components/                # Componentes reutilizables
├── services/
│   └── api.js                # Servicio API REST
├── router/
│   └── index.js              # Configuración de rutas
├── App.vue                   # Componente raíz
└── main.js                   # Entrada de la aplicación
```

## 🚀 Instalación y Uso

### Prerequisites
- Node.js 20.19.0 o mayor
- npm o pnpm

### Instalación

```bash
cd front-tienda-vinos
npm install
```

### Desarrollo

```bash
npm run dev
```

La aplicación estará disponible en `http://localhost:5173`

### Build para Producción

```bash
npm run build
```

### Preview de Build

```bash
npm run preview
```

## 🔗 Rutas Disponibles

- `/` - Página de inicio
- `/catalogo` - Catálogo con filtros
- `/producto/:id` - Detalle del producto
- `/carrito` - Carrito de compras
- `/checkout` - Factura y pago
- `/about` - Página de historia

## 🛠️ Características Implementadas

### HomePage.vue
- Hero section con call-to-action
- Productos destacados
- Ofertas imperdibles
- Ediciones especiales
- Sección "Nuestra Historia"

### CatalogPage.vue
- Barra lateral de filtros completa:
  - Búsqueda por nombre
  - Filtro por categoría
  - Filtro por país
  - Filtro por bodega/marca
  - Filtro por cepa/variedad
  - Filtro de ofertas
  - Ordenamiento (precio, nombre, fecha)
- Grid de productos con paginación
- Botón rápido para agregar al carrito
- Visualización de descuentos

### ProductPage.vue
- Imagen principal con zoom
- Información técnica (año, alcohol, contenido, stock)
- Precios con descuentos
- Descripción del vino
- Datos técnicos detallados
- Productos relacionados

### CartPage.vue
- Listado de productos en carrito
- Incrementar/decrementar cantidades
- Eliminar productos
- Resumen de compra
- Total estimado
- Botón para proceder al checkout

### CheckoutPage.vue
- Factura formateada
- Tabla de productos
- Detalles de totales
- Botón de confirmación y pago
- Link para volver al carrito

### AboutPage.vue
- Hero section con filosofía
- Sección de valores (El Origen, La Excelencia, El Vínculo)
- Break editorial con imagen
- Sección de misión
- Características de servicio

## 📡 Integración con API

Todas las vistas están configuradas para consumir una API REST en el backend. Los endpoints esperados son:

```
GET  /api/productos                    # Listar productos con filtros
GET  /api/productos/:id                # Obtener detalle de producto
GET  /api/productos/destacados         # Productos destacados y con descuento
GET  /api/filtros                      # Obtener opciones de filtros
POST /api/carrito/agregar/:id          # Agregar producto al carrito
POST /api/checkout/pay                 # Procesar pago
```

## 🎨 Diseño Visual

La aplicación mantiene el diseño visual exacto del proyecto 1:
- Tailwind CSS para estilos
- Paleta de colores original
- Material Symbols para iconos
- Font Awesome para redes sociales
- Google Fonts (Noto Serif, Manrope)

## 💾 Estado del Carrito

El carrito se almacena en **localStorage** para persistencia entre sesiones:

```javascript
localStorage.getItem('carrito') // Objeto con productos
```

Estructura:
```json
{
  "1": {
    "id": 1,
    "nombre": "Vino X",
    "precio": 45.99,
    "cantidad": 2,
    "imagen": "url"
  }
}
```

## 📝 Notas Importantes

1. **API Backend**: Requiere que el backend Laravel proporcione los endpoints REST listados arriba
2. **CORS**: Configurar CORS en el backend para permitir solicitudes desde el frontend
3. **LocalStorage**: Los carritos se guardan en el navegador del usuario
4. **Notifications**: Las notificaciones flotantes se muestran automáticamente
5. **Cart Counter**: El contador de carrito se actualiza en tiempo real

## ⏳ Trabajo Pendiente

- [ ] Admin Dashboard y vistas CRUD
- [ ] Admin Layout
- [ ] Autenticación de usuarios
- [ ] Sistema de pedidos
- [ ] Integración de pagos real
- [ ] Tests unitarios

## 📞 Contacto

Diseñado y desarrollado por Daniel y Josué para La Última Botella.

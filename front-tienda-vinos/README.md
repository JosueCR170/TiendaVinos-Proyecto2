# 🍷 La Última Botella - Front-end Vue.js

Tienda de vinos online construida con Vue.js 3, Vite y Tailwind CSS.

## 🚀 Inicio Rápido

### Requisitos
- Node.js 20.19.0 o mayor
- npm o pnpm

### Instalación

```bash
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

### Vista Previa

```bash
npm run preview
```

## 📁 Estructura del Proyecto

```
src/
├── layouts/AppLayout.vue      # Layout principal
├── views/frontend/            # Vistas frontend
│   ├── HomePage.vue
│   ├── CatalogPage.vue
│   ├── ProductPage.vue
│   ├── CartPage.vue
│   ├── CheckoutPage.vue
│   └── AboutPage.vue
├── services/api.js            # Cliente HTTP
├── router/index.js            # Configuración de rutas
├── App.vue                    # Componente raíz
└── main.js                    # Punto de entrada
```

## 🗺️ Rutas Disponibles

| Ruta | Descripción |
|------|-------------|
| `/` | Página de inicio |
| `/catalogo` | Catálogo de vinos con filtros |
| `/producto/:id` | Detalle de producto |
| `/carrito` | Carrito de compras |
| `/checkout` | Factura y confirmación |
| `/about` | Historia de la tienda |

## 🛠️ Tecnologías

- **Vue.js 3** - Framework JavaScript
- **Vite** - Build tool
- **Tailwind CSS** - CSS framework
- **Vue Router** - Enrutador
- **Material Symbols** - Iconos

## 🎨 Características

✅ Catálogo con filtros avanzados  
✅ Detalle de productos  
✅ Carrito con LocalStorage  
✅ Checkout y factura  
✅ Página de historia  
✅ Notificaciones flotantes  
✅ Completamente responsivo  

## 📚 Documentación

Ver `MIGRATION_GUIDE.md` para más detalles sobre la arquitectura y endpoints de API.

## 👥 Desarrolladores

Daniel y Josué - La Última Botella


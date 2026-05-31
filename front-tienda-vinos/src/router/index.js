import { createRouter, createWebHistory } from 'vue-router'

// Frontend Views
import HomePage from '@/views/frontend/HomePage.vue'
import CatalogPage from '@/views/frontend/CatalogPage.vue'
import ProductPage from '@/views/frontend/ProductPage.vue'
import CartPage from '@/views/frontend/CartPage.vue'
import CheckoutPage from '@/views/frontend/CheckoutPage.vue'
import AboutPage from '@/views/frontend/AboutPage.vue'

const routes = [
  // ── Frontend ─────────────────────────────────────────────────────────────
  {
    path: '/',
    name: 'home',
    component: HomePage,
    meta: { title: 'Inicio - La Última Botella' }
  },
  {
    path: '/catalogo',
    name: 'catalogo',
    component: CatalogPage,
    meta: { title: 'Catálogo - La Última Botella' }
  },
  {
    path: '/catalogo/:id', // Soporte para links existentes
    redirect: to => {
      return { path: '/producto/' + to.params.id }
    }
  },
  {
    path: '/producto/:id',
    name: 'producto',
    component: ProductPage,
    meta: { title: 'Producto - La Última Botella' }
  },
  {
    path: '/carrito',
    name: 'carrito',
    component: CartPage,
    meta: { title: 'Carrito - La Última Botella' }
  },
  {
    path: '/checkout',
    name: 'checkout',
    component: CheckoutPage,
    meta: { title: 'Checkout - La Última Botella' }
  },
  {
    path: '/about',
    name: 'about',
    component: AboutPage,
    meta: { title: 'Nuestra Historia - La Última Botella' }
  },

  // ── Admin ────────────────────────────────────────────────────────────────
  {
    path: '/admin',
    name: 'admin.dashboard',
    component: () => import('@/views/admin/AdminDashboard.vue'),
    meta: { title: 'Dashboard - Admin' }
  },
  {
    path: '/admin/productos',
    name: 'admin.productos.index',
    component: () => import('@/views/admin/productos/ProductosIndex.vue'),
    meta: { title: 'Productos - Admin' }
  },
  {
    path: '/admin/productos/crear',
    name: 'admin.productos.create',
    component: () => import('@/views/admin/productos/ProductosCreate.vue'),
    meta: { title: 'Crear Producto - Admin' }
  },
  {
    path: '/admin/productos/:id/editar',
    name: 'admin.productos.edit',
    component: () => import('@/views/admin/productos/ProductosEdit.vue'),
    meta: { title: 'Editar Producto - Admin' }
  },
  
  {
    path: '/admin/categorias',
    name: 'admin.categorias.index',
    component: () => import('@/views/admin/categorias/CategoriasIndex.vue'),
    meta: { title: 'Categorías - Admin' }
  },
  {
    path: '/admin/categorias/crear',
    name: 'admin.categorias.create',
    component: () => import('@/views/admin/categorias/CategoriasCreate.vue'),
    meta: { title: 'Crear Categoría - Admin' }
  },
  {
    path: '/admin/categorias/:id/editar',
    name: 'admin.categorias.edit',
    component: () => import('@/views/admin/categorias/CategoriasEdit.vue'),
    meta: { title: 'Editar Categoría - Admin' }
  },

  {
    path: '/admin/marcas',
    name: 'admin.marcas.index',
    component: () => import('@/views/admin/marcas/MarcasIndex.vue'),
    meta: { title: 'Marcas - Admin' }
  },
  {
    path: '/admin/marcas/crear',
    name: 'admin.marcas.create',
    component: () => import('@/views/admin/marcas/MarcasCreate.vue'),
    meta: { title: 'Crear Marca - Admin' }
  },
  {
    path: '/admin/marcas/:id/editar',
    name: 'admin.marcas.edit',
    component: () => import('@/views/admin/marcas/MarcasEdit.vue'),
    meta: { title: 'Editar Marca - Admin' }
  },

  {
    path: '/admin/variedades',
    name: 'admin.variedades.index',
    component: () => import('@/views/admin/variedades/VariedadesIndex.vue'),
    meta: { title: 'Variedades - Admin' }
  },
  {
    path: '/admin/variedades/crear',
    name: 'admin.variedades.create',
    component: () => import('@/views/admin/variedades/VariedadesCreate.vue'),
    meta: { title: 'Crear Variedad - Admin' }
  },
  {
    path: '/admin/variedades/:id/editar',
    name: 'admin.variedades.edit',
    component: () => import('@/views/admin/variedades/VariedadesEdit.vue'),
    meta: { title: 'Editar Variedad - Admin' }
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition
    return { top: 0 }
  }
})

router.beforeEach((to, from, next) => {
  document.title = to.meta.title || 'La Última Botella'
  next()
})

export default router

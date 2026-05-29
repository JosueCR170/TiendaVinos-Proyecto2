import { createRouter, createWebHistory } from 'vue-router'

// Frontend Views
import HomePage from '@/views/frontend/HomePage.vue'
import CatalogPage from '@/views/frontend/CatalogPage.vue'
import ProductPage from '@/views/frontend/ProductPage.vue'
import CartPage from '@/views/frontend/CartPage.vue'
import CheckoutPage from '@/views/frontend/CheckoutPage.vue'
import AboutPage from '@/views/frontend/AboutPage.vue'

const routes = [
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
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Actualizar título de la página
router.beforeEach((to, from, next) => {
  const title = to.meta.title || 'La Última Botella'
  document.title = title
  next()
})

export default router


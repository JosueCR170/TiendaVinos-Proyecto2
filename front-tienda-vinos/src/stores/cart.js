import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

/**
 * Store del carrito de compras.
 * Persiste en localStorage bajo la clave 'carrito'.
 * Estructura de items: { [idProducto]: { idProducto, nombre, precio, imagen, cantidad } }
 */
export const useCartStore = defineStore('cart', () => {
  const items = ref({})

  // ── Getters ────────────────────────────────────────────────────────────────

  const count = computed(() =>
    Object.values(items.value).reduce((sum, it) => sum + (it.cantidad || 0), 0)
  )

  const total = computed(() =>
    Object.values(items.value).reduce(
      (sum, it) => sum + (it.precio || 0) * (it.cantidad || 0),
      0
    )
  )

  const itemsList = computed(() => Object.values(items.value))

  // ── Persistencia ───────────────────────────────────────────────────────────

  function persist() {
    localStorage.setItem('carrito', JSON.stringify(items.value))
  }

  function hydrate() {
    try {
      const raw = localStorage.getItem('carrito')
      if (raw) items.value = JSON.parse(raw) || {}
    } catch {
      items.value = {}
    }
  }

  // ── Acciones ───────────────────────────────────────────────────────────────

  /**
   * Agrega un producto al carrito.
   * Acepta el id solo, o un objeto con { idProducto, nombre, precio, imagen }.
   */
  function addItem(idProducto, detalle = {}) {
    if (!idProducto) throw new Error('ID de producto requerido')

    const key = String(idProducto)

    if (!items.value[key]) {
      items.value[key] = {
        idProducto: Number(idProducto),
        nombre:     detalle.nombre  ?? '',
        precio:     detalle.precio  ?? 0,
        imagen:     detalle.imagen  ?? '',
        cantidad:   1,
      }
    } else {
      items.value[key].cantidad += 1
    }

    persist()

    return {
      success:      true,
      carritoCount: count.value,
      mensaje:      `"${items.value[key].nombre || 'Producto'}" agregado al carrito`,
    }
  }

  function increment(idProducto) {
    const key = String(idProducto)
    if (!items.value[key]) return
    items.value[key].cantidad += 1
    persist()
  }

  function decrement(idProducto) {
    const key = String(idProducto)
    if (!items.value[key]) return
    const next = items.value[key].cantidad - 1
    if (next <= 0) {
      delete items.value[key]
    } else {
      items.value[key].cantidad = next
    }
    persist()
  }

  function remove(idProducto) {
    delete items.value[String(idProducto)]
    persist()
  }

  function clear() {
    items.value = {}
    persist()
  }

  return {
    items,
    count,
    total,
    itemsList,
    hydrate,
    addItem,
    increment,
    decrement,
    remove,
    clear,
  }
})

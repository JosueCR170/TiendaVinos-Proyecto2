import BaseModel from './BaseModel'
import Categoria from './Categoria'
import Marca from './Marca'
import Variedad from './Variedad'

export default class Producto extends BaseModel {
  constructor(data = {}) {
    super(data)

    this.id_producto = data.id_producto ?? null
    this.id = this.id_producto
    this.nombre = data.nombre ?? ''
    this.descripcion = data.descripcion ?? ''
    this.cantidad = this.number(data.cantidad, 0)
    this.id_categoria = data.id_categoria ?? null
    this.id_marca = data.id_marca ?? null
    this.pais = data.pais ?? ''
    this.region = data.region ?? ''
    this.precio = this.number(data.precio, 0)
    this.contenido_ml = this.number(data.contenido_ml, null)
    this.anio_cosecha = this.number(data.anio_cosecha, null)
    this.alcohol_porcentaje = this.number(data.alcohol_porcentaje, null)
    this.imagen_url = data.imagen_url ?? ''
    this.descuento = this.number(data.descuento, 0)
    this.estado = this.boolean(data.estado)
    this.categoria = data.categoria ? new Categoria(data.categoria) : null
    this.marca = data.marca ? new Marca(data.marca) : null
    this.variedades = Array.isArray(data.variedades)
      ? data.variedades.map((variedad) => new Variedad(variedad))
      : []
  }

  get disponible() {
    return this.estado && this.cantidad > 0
  }

  get precioConDescuento() {
    if (!this.descuento) {
      return this.precio
    }

    return this.precio - (this.precio * this.descuento / 100)
  }

  toPayload() {
    return {
      nombre: this.nombre,
      descripcion: this.descripcion,
      cantidad: this.cantidad,
      id_categoria: this.id_categoria,
      id_marca: this.id_marca,
      pais: this.pais,
      region: this.region,
      precio: this.precio,
      contenido_ml: this.contenido_ml,
      anio_cosecha: this.anio_cosecha,
      alcohol_porcentaje: this.alcohol_porcentaje,
      imagen_url: this.imagen_url,
      descuento: this.descuento,
      estado: this.estado,
    }
  }
}

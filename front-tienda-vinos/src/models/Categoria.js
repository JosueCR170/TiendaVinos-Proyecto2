import BaseModel from './BaseModel'

export default class Categoria extends BaseModel {
  constructor(data = {}) {
    super(data)

    this.id_categoria = data.id_categoria ?? null
    this.id = this.id_categoria
    this.nombre = data.nombre ?? ''
    this.nombre_padre = data.nombre_padre ?? null
    this.descripcion = data.descripcion ?? ''
    this.nivel = this.number(data.nivel, 1)
    this.padre = data.padre ? new Categoria(data.padre) : null
    this.hijas = Array.isArray(data.hijas)
      ? data.hijas.map((categoria) => new Categoria(categoria))
      : []
    this.productos_count = this.number(data.productos_count, 0)
  }

  get esPrincipal() {
    return this.nivel === 1
  }

  get esSubcategoria() {
    return this.nivel === 2
  }

  toPayload() {
    return {
      nombre: this.nombre,
      nombre_padre: this.nombre_padre,
      descripcion: this.descripcion,
      nivel: this.nivel,
    }
  }
}

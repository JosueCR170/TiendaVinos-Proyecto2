import BaseModel from './BaseModel'

export default class Variedad extends BaseModel {
  constructor(data = {}) {
    super(data)

    this.id_variedad = data.id_variedad ?? null
    this.id = this.id_variedad
    this.nombre = data.nombre ?? ''
    this.descripcion = data.descripcion ?? ''
    this.tipo = data.tipo ?? ''
    this.porcentaje = this.number(data.pivot?.porcentaje ?? data.porcentaje, null)
    this.productos_count = this.number(data.productos_count, 0)
  }

  toPayload() {
    return {
      nombre: this.nombre,
      descripcion: this.descripcion,
      tipo: this.tipo,
    }
  }
}

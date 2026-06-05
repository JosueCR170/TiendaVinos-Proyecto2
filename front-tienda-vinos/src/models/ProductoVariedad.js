import BaseModel from './BaseModel'

export default class ProductoVariedad extends BaseModel {
  constructor(data = {}) {
    super(data)

    this.id_producto = data.id_producto ?? null
    this.id_variedad = data.id_variedad ?? null
    this.porcentaje = this.number(data.porcentaje, null)
  }

  toPayload() {
    return {
      id_producto: this.id_producto,
      id_variedad: this.id_variedad,
      porcentaje: this.porcentaje,
    }
  }
}

import BaseModel from './BaseModel'

export default class Marca extends BaseModel {
  constructor(data = {}) {
    super(data)

    this.id_marca = data.id_marca ?? null
    this.id = this.id_marca
    this.nombre = data.nombre ?? ''
    this.descripcion = data.descripcion ?? ''
    this.pais = data.pais ?? ''
    this.sitio_web = data.sitio_web ?? ''
    this.productos_count = this.number(data.productos_count, 0)
  }

  toPayload() {
    return {
      nombre: this.nombre,
      descripcion: this.descripcion,
      pais: this.pais,
      sitio_web: this.sitio_web,
    }
  }
}

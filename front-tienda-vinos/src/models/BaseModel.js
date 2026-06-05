export default class BaseModel {
  constructor(data = {}) {
    this.raw = data
    this.created_at = data.created_at ?? null
    this.updated_at = data.updated_at ?? null
  }

  number(value, fallback = 0) {
    if (value === null || value === undefined || value === '') {
      return fallback
    }

    const parsed = Number(value)
    return Number.isNaN(parsed) ? fallback : parsed
  }

  boolean(value) {
    return value === true || value === 1 || value === '1'
  }

  toPayload() {
    return { ...this.raw }
  }
}

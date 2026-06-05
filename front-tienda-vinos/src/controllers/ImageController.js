import api from '@/services/api'

export default class ImageController {
  async upload(file) {
    try {
      const formData = new FormData()
      formData.append('image', file)

      const response = await api.post('v1/images/upload', formData)

      return {
        success: response.data.success ?? true,
        message: response.data.message ?? '',
        data: response.data.data,
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message ?? error.message ?? 'Error al subir la imagen.',
        error: error.response?.data?.error ?? error,
      }
    }
  }

  async delete(path) {
    try {
      const response = await api.delete('v1/images/delete', {
        data: { path }
      })

      return {
        success: response.data.success ?? true,
        message: response.data.message ?? '',
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message ?? error.message ?? 'Error al eliminar la imagen.',
        error: error.response?.data?.error ?? error,
      }
    }
  }
}

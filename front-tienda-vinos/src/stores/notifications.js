import { ref } from 'vue'

let nextId = 1
const list = ref([])

function show(message, type = 'success', duration = 3500) {
  const id = nextId++
  list.value.push({ id, message, type })
  setTimeout(() => dismiss(id), duration)
}

function dismiss(id) {
  const idx = list.value.findIndex((n) => n.id === id)
  if (idx !== -1) list.value.splice(idx, 1)
}

export function useNotificationStore() {
  return {
    get list() { return list.value },
    show,
    dismiss
  }
}

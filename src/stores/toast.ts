import { defineStore } from 'pinia'

export type ToastType = 'success' | 'error' | 'info' | 'warning'

export type ToastItem = {
  id: string
  type: ToastType
  title?: string
  message: string
  durationMs: number
}

function uid() {
  return `${Date.now()}-${Math.random().toString(16).slice(2)}`
}

export const useToastStore = defineStore('toast', {
  state: () => ({
    items: [] as ToastItem[],
  }),
  actions: {
    show(params: Omit<ToastItem, 'id'>) {
      const id = uid()
      this.items.push({ id, ...params })
      if (params.durationMs > 0) {
        window.setTimeout(() => {
          this.dismiss(id)
        }, params.durationMs)
      }
    },
    success(message: string, title = 'Listo') {
      this.show({ type: 'success', title, message, durationMs: 3000 })
    },
    error(message: string, title = 'Ocurrió un error') {
      this.show({ type: 'error', title, message, durationMs: 4500 })
    },
    info(message: string, title = 'Info') {
      this.show({ type: 'info', title, message, durationMs: 3500 })
    },
    warning(message: string, title = 'Atención') {
      this.show({ type: 'warning', title, message, durationMs: 4000 })
    },
    dismiss(id: string) {
      this.items = this.items.filter((t) => t.id !== id)
    },
    clear() {
      this.items = []
    },
  },
})



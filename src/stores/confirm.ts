import { defineStore } from 'pinia'

export type ConfirmState = {
  open: boolean
  title: string
  message: string
  confirmText: string
  cancelText: string
  tone: 'danger' | 'default'
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  _resolve?: (value: any) => void
}

export const useConfirmStore = defineStore('confirm', {
  state: (): ConfirmState => ({
    open: false,
    title: 'Confirmar',
    message: '',
    confirmText: 'Confirmar',
    cancelText: 'Cancelar',
    tone: 'default',
    _resolve: undefined,
  }),
  actions: {
    ask(params: {
      title?: string
      message: string
      confirmText?: string
      cancelText?: string
      tone?: 'danger' | 'default'
    }): Promise<boolean> {
      this.open = true
      this.title = params.title ?? 'Confirmar'
      this.message = params.message
      this.confirmText = params.confirmText ?? 'Confirmar'
      this.cancelText = params.cancelText ?? 'Cancelar'
      this.tone = params.tone ?? 'default'

      return new Promise((resolve) => {
        this._resolve = resolve
      })
    },
    accept() {
      this.open = false
      this._resolve?.(true)
      this._resolve = undefined
    },
    cancel() {
      this.open = false
      this._resolve?.(false)
      this._resolve = undefined
    },
  },
})



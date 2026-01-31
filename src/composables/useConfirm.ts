import { useConfirmStore } from '@/stores/confirm'

export function useConfirm() {
  const store = useConfirmStore()
  return {
    confirm: (message: string, opts?: { title?: string; confirmText?: string; cancelText?: string; tone?: 'danger' | 'default' }) =>
      store.ask({
        message,
        title: opts?.title,
        confirmText: opts?.confirmText,
        cancelText: opts?.cancelText,
        tone: opts?.tone,
      }),
  }
}



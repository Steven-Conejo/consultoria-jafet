<template>
  <Teleport to="body">
    <div class="toast-host" role="status" aria-live="polite" aria-relevant="additions">
      <TransitionGroup name="toast" tag="div" class="toast-stack">
        <div v-for="t in toasts" :key="t.id" class="toast" :class="t.type">
          <div class="toast-icon" aria-hidden="true">
            <span v-if="t.type === 'success'">✓</span>
            <span v-else-if="t.type === 'error'">!</span>
            <span v-else-if="t.type === 'warning'">⚠</span>
            <span v-else>i</span>
          </div>
          <div class="toast-content">
            <div v-if="t.title" class="toast-title">{{ t.title }}</div>
            <div class="toast-message">{{ t.message }}</div>
          </div>
          <button class="toast-close" type="button" @click="dismiss(t.id)" aria-label="Cerrar notificación">
            ×
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia'
import { useToastStore } from '@/stores/toast'

const toastStore = useToastStore()
const { items: toasts } = storeToRefs(toastStore)

const dismiss = (id: string) => toastStore.dismiss(id)
</script>

<style scoped>
.toast-host {
  position: fixed;
  top: 16px;
  right: 16px;
  z-index: 9999;
  pointer-events: none;
}

.toast-stack {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.toast {
  pointer-events: auto;
  width: min(420px, calc(100vw - 32px));
  display: grid;
  grid-template-columns: 36px 1fr 28px;
  gap: 12px;
  align-items: start;
  padding: 14px 14px;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
  border: 1px solid rgba(226, 232, 240, 0.8);
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(10px);
}

.toast.success {
  border-left: 4px solid #16a34a;
}
.toast.error {
  border-left: 4px solid #dc2626;
}
.toast.warning {
  border-left: 4px solid #f59e0b;
}
.toast.info {
  border-left: 4px solid #2563eb;
}

.toast-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 900;
  color: #111827;
  background: #f1f5f9;
}

.toast.success .toast-icon {
  background: rgba(22, 163, 74, 0.12);
  color: #15803d;
}
.toast.error .toast-icon {
  background: rgba(220, 38, 38, 0.12);
  color: #b91c1c;
}
.toast.warning .toast-icon {
  background: rgba(245, 158, 11, 0.12);
  color: #b45309;
}
.toast.info .toast-icon {
  background: rgba(37, 99, 235, 0.12);
  color: #1d4ed8;
}

.toast-title {
  font-size: 14px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 2px;
}

.toast-message {
  font-size: 14px;
  color: #334155;
  line-height: 1.35;
  word-break: break-word;
}

.toast-close {
  background: transparent;
  border: none;
  width: 28px;
  height: 28px;
  border-radius: 8px;
  font-size: 20px;
  line-height: 1;
  cursor: pointer;
  color: #64748b;
  transition: background 0.2s ease, color 0.2s ease;
}

.toast-close:hover {
  background: rgba(226, 232, 240, 0.8);
  color: #0f172a;
}

/* Animaciones */
.toast-enter-active,
.toast-leave-active {
  transition: all 0.22s ease;
}
.toast-enter-from {
  opacity: 0;
  transform: translateY(-8px) translateX(8px);
}
.toast-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>



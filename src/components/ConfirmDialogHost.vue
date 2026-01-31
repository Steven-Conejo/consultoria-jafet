<template>
  <Teleport to="body">
    <div v-if="open" class="overlay" @keydown.esc="cancel" tabindex="-1">
      <div class="backdrop" @click="cancel"></div>
      <div class="dialog" role="dialog" aria-modal="true" :aria-label="title">
        <div class="dialog-header">
          <div class="dialog-title">{{ title }}</div>
          <button class="x" type="button" @click="cancel" aria-label="Cerrar">×</button>
        </div>
        <div class="dialog-body">
          <p class="dialog-message">{{ message }}</p>
        </div>
        <div class="dialog-actions">
          <button class="btn secondary" type="button" @click="cancel">{{ cancelText }}</button>
          <button class="btn" :class="tone === 'danger' ? 'danger' : 'primary'" type="button" @click="accept">
            {{ confirmText }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia'
import { useConfirmStore } from '@/stores/confirm'

const confirmStore = useConfirmStore()
const { open, title, message, confirmText, cancelText, tone } = storeToRefs(confirmStore)

const accept = () => confirmStore.accept()
const cancel = () => confirmStore.cancel()
</script>

<style scoped>
.overlay {
  position: fixed;
  inset: 0;
  z-index: 9998;
  display: grid;
  place-items: center;
  padding: 16px;
}

.backdrop {
  position: absolute;
  inset: 0;
  background: rgba(15, 23, 42, 0.55);
  backdrop-filter: blur(6px);
}

.dialog {
  position: relative;
  width: min(520px, calc(100vw - 32px));
  background: rgba(255, 255, 255, 0.96);
  border: 1px solid rgba(226, 232, 240, 0.9);
  border-radius: 16px;
  box-shadow: 0 18px 60px rgba(0, 0, 0, 0.25);
  overflow: hidden;
}

.dialog-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 18px 20px;
  background: linear-gradient(135deg, rgba(26, 54, 93, 0.08) 0%, rgba(44, 82, 130, 0.06) 100%);
  border-bottom: 1px solid rgba(226, 232, 240, 0.9);
}

.dialog-title {
  font-size: 18px;
  font-weight: 800;
  color: #0f172a;
}

.x {
  background: transparent;
  border: none;
  width: 34px;
  height: 34px;
  border-radius: 10px;
  font-size: 24px;
  line-height: 1;
  cursor: pointer;
  color: #64748b;
  transition: background 0.2s ease, color 0.2s ease;
}
.x:hover {
  background: rgba(226, 232, 240, 0.85);
  color: #0f172a;
}

.dialog-body {
  padding: 18px 20px;
}

.dialog-message {
  margin: 0;
  font-size: 15px;
  color: #334155;
  line-height: 1.5;
}

.dialog-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 16px 20px 20px;
}

.btn {
  border: none;
  padding: 10px 16px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 800;
  cursor: pointer;
  transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.2s ease;
}
.btn:active {
  transform: translateY(1px);
}

.btn.secondary {
  background: #e2e8f0;
  color: #0f172a;
}
.btn.secondary:hover {
  background: #cbd5e1;
}

.btn.primary {
  background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
  color: white;
}
.btn.primary:hover {
  box-shadow: 0 8px 22px rgba(26, 54, 93, 0.22);
}

.btn.danger {
  background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
  color: white;
}
.btn.danger:hover {
  box-shadow: 0 8px 22px rgba(220, 38, 38, 0.22);
}
</style>



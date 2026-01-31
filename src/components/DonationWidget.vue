<template>
  <div class="donation-widget">
    <button @click="goToDonationPage" class="donate-btn">
      <span class="btn-shine"></span>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
      </svg>
      <span class="btn-text">Donar</span>
      <div class="btn-particles">
        <span></span><span></span><span></span>
      </div>
    </button>
    <p class="donation-note">Las donaciones se realizan mediante criptomonedas. Tu contribución nos ayuda a mantener la plataforma gratuita y segura.</p>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'

const router = useRouter()

const goToDonationPage = () => {
  router.push('/donaciones')
}
</script>

<style scoped>
.donation-widget {
  background: rgba(255, 255, 255, 0.1);
  padding: 40px;
  border-radius: 12px;
  backdrop-filter: blur(10px);
  max-width: 500px;
  margin: 0 auto;
}

/* Botón de Donar - Mantener igual */
.donate-btn {
  width: 100%;
  padding: 28px 40px;
  background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
  color: #1a365d;
  border: none;
  border-radius: 16px;
  font-size: 26px;
  font-weight: 800;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 15px;
  position: relative;
  overflow: hidden;
  margin-bottom: 20px;
  box-shadow: 0 8px 30px rgba(255, 215, 0, 0.4);
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  letter-spacing: 0.5px;
}

.donate-btn::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.3);
  transform: translate(-50%, -50%);
  transition: width 0.6s, height 0.6s;
}

.donate-btn:hover::before {
  width: 300px;
  height: 300px;
}

.donate-btn:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 12px 40px rgba(255, 215, 0, 0.5),
              0 0 0 8px rgba(255, 215, 0, 0.1);
  background: linear-gradient(135deg, #ffed4e 0%, #ffd700 100%);
}

.donate-btn:active {
  transform: translateY(-2px) scale(0.98);
}

.btn-shine {
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  animation: shine 3s infinite;
}

@keyframes shine {
  0% { left: -100%; }
  50%, 100% { left: 100%; }
}

.btn-text {
  position: relative;
  z-index: 1;
  animation: pulse-text 2s ease-in-out infinite;
}

@keyframes pulse-text {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

.donate-btn svg {
  width: 32px;
  height: 32px;
  position: relative;
  z-index: 1;
  animation: heart-beat 1.5s ease-in-out infinite;
}

@keyframes heart-beat {
  0%, 100% { transform: scale(1); }
  25% { transform: scale(1.1); }
  50% { transform: scale(1); }
  75% { transform: scale(1.05); }
}

.btn-particles {
  position: absolute;
  width: 100%;
  height: 100%;
  overflow: hidden;
  border-radius: 16px;
}

.btn-particles span {
  position: absolute;
  width: 6px;
  height: 6px;
  background: rgba(255, 255, 255, 0.8);
  border-radius: 50%;
  pointer-events: none;
  animation: float 3s ease-in-out infinite;
}

.btn-particles span:nth-child(1) {
  left: 20%;
  animation-delay: 0s;
}

.btn-particles span:nth-child(2) {
  left: 50%;
  animation-delay: 1s;
}

.btn-particles span:nth-child(3) {
  left: 80%;
  animation-delay: 2s;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0) scale(0);
    opacity: 0;
  }
  50% {
    transform: translateY(-20px) scale(1);
    opacity: 1;
  }
}

.donation-note {
  font-size: 14px;
  text-align: center;
  opacity: 0.95;
  line-height: 1.6;
  color: white;
}
</style>

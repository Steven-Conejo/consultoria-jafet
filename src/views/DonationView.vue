<template>
  <div class="donation-page">
    <!-- Hero Section con imagen -->
    <div class="donation-hero">
      <div class="hero-background">
        <img :src="heroImage" alt="Background" class="hero-bg-image" loading="eager" decoding="async" fetchpriority="high" />
        <div class="hero-overlay"></div>
      </div>
      <div class="hero-content">
        <h1 class="hero-title">Donaciones</h1>
        <p class="hero-subtitle">Tu apoyo nos ayuda a seguir brindando un servicio de calidad</p>
      </div>
    </div>

    <div class="donation-container">
      <button @click="$router.push('/')" class="back-button">
        ← Volver al Inicio
      </button>

      <div class="donation-content">
        <!-- Lado Izquierdo: QR -->
        <div class="qr-section">
          <div class="qr-title-wrapper">
            <div class="qr-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <rect x="7" y="7" width="10" height="10"></rect>
                <rect x="9" y="9" width="6" height="6"></rect>
              </svg>
            </div>
            <h2 class="qr-title">Deposita USDT en Binance</h2>
            <p class="qr-subtitle">Escanea el código para realizar tu donación</p>
          </div>
          <div class="qr-wrapper">
            <div class="qr-glow"></div>
            <img :src="qrImage" alt="QR Code para donación" class="qr-image" loading="lazy" decoding="async" />
          </div>
        </div>

        <!-- Lado Derecho: Información -->
        <div class="info-section">
          <div class="info-card animated-card">
            <div class="card-icon-wrapper">
              <div class="card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="2" y1="12" x2="22" y2="12"></line>
                  <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                </svg>
              </div>
            </div>
            <div class="info-row">
              <span class="info-label">Red Blockchain</span>
              <span class="info-value">Tron (TRC20)</span>
            </div>
          </div>

          <div class="info-card address-card animated-card">
            <div class="card-icon-wrapper">
              <div class="card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
              </div>
            </div>
            <div class="info-row address-row">
              <span class="info-label">Dirección de la billetera</span>
              <div class="address-group">
                <code class="address-text">{{ walletAddress }}</code>
                <button @click="copyAddress" class="copy-btn" :class="{ copied: copied }">
                  <svg v-if="!copied" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                  </svg>
                  <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                  <span>{{ copied ? '¡Copiado!' : 'Copiar Dirección' }}</span>
                </button>
              </div>
            </div>
          </div>

          <div class="message-card animated-card">
            <div class="message-header">
              <div class="message-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
              </div>
              <h3 class="message-title">Realizar Donación</h3>
            </div>
            <p class="message-text">
              Puedes realizar tu donación de <strong>monto libre</strong> escaneando el código QR 
              o copiando la dirección de la cuenta. Cada contribución nos ayuda a seguir brindando 
              un <strong>servicio de calidad</strong> y a mantener la plataforma funcionando para todos.
            </p>
            <div class="benefits-list">
              <div class="benefit-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Monto libre y voluntario</span>
              </div>
              <div class="benefit-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Fácil y seguro</span>
              </div>
              <div class="benefit-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Ayudas a mejorar el servicio</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useToastStore } from '@/stores/toast'
import qrImage from '@/images/Imagen Para Donaciones.jpeg'
import heroImage from '@/images/pexels-sora-shimazaki-5668473.jpg'

const copied = ref(false)
const toast = useToastStore()

const walletAddress = 'TLzUSeXDpE8gXfhnBus1Axc4 TLVQ45uLLT'

const copyAddress = async () => {
  try {
    await navigator.clipboard.writeText(walletAddress.replace(/\s/g, ''))
    copied.value = true
    toast.success('Dirección copiada al portapapeles')
    
    setTimeout(() => {
      copied.value = false
    }, 2000)
  } catch (error) {
    toast.error('No se pudo copiar la dirección')
  }
}
</script>

<style scoped>
.donation-page {
  min-height: 100vh;
  padding-top: 0;
  background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
  padding-bottom: 60px;
}

/* Hero Section */
.donation-hero {
  position: relative;
  height: 350px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  margin-bottom: 50px;
}

.hero-background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 0;
}

.hero-bg-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: brightness(0.7);
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(26, 54, 93, 0.85) 0%, rgba(44, 82, 130, 0.75) 100%);
  z-index: 1;
}

.hero-content {
  position: relative;
  z-index: 2;
  text-align: center;
  color: white;
  animation: fadeInUp 0.8s ease-out;
}

.hero-title {
  font-size: 48px;
  font-weight: 800;
  margin: 0 0 12px 0;
  letter-spacing: -1px;
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.hero-subtitle {
  font-size: 20px;
  font-weight: 400;
  margin: 0;
  opacity: 0.95;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
    opacity: 1;
  }
  50% {
    transform: scale(1.05);
    opacity: 0.9;
  }
}

.donation-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 20px 40px;
}

.back-button {
  background: white;
  border: 2px solid #1a365d;
  color: #1a365d;
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-bottom: 40px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.back-button:hover {
  background: #1a365d;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(26, 54, 93, 0.3);
}

.donation-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  background: white;
  border-radius: 20px;
  padding: 60px 50px;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
  animation: fadeIn 0.6s ease-out 0.3s both;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Sección QR */
.qr-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  gap: 30px;
  padding-right: 40px;
  border-right: 2px solid #e5e7eb;
  animation: slideInLeft 0.8s ease-out;
}

@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.qr-title-wrapper {
  text-align: center;
  width: 100%;
}

.qr-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
  color: white;
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

.qr-icon svg {
  width: 30px;
  height: 30px;
}

.qr-title {
  font-size: 28px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 8px 0;
  letter-spacing: -0.5px;
}

.qr-subtitle {
  font-size: 15px;
  color: #64748b;
  margin: 0;
  font-weight: 500;
}

.qr-wrapper {
  position: relative;
  background: white;
  padding: 30px;
  border-radius: 20px;
  box-shadow: 
    0 10px 30px rgba(0, 0, 0, 0.15),
    0 0 0 1px rgba(226, 232, 240, 0.5);
  animation: scaleIn 0.6s ease-out 0.4s both;
}

@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.qr-glow {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 120%;
  height: 120%;
  background: radial-gradient(circle, rgba(102, 126, 234, 0.2) 0%, transparent 70%);
  border-radius: 50%;
  animation: glowPulse 2s ease-in-out infinite;
  pointer-events: none;
}

@keyframes glowPulse {
  0%, 100% {
    opacity: 0.5;
    transform: translate(-50%, -50%) scale(1);
  }
  50% {
    opacity: 0.8;
    transform: translate(-50%, -50%) scale(1.1);
  }
}

.qr-image {
  width: 450px;
  height: 450px;
  display: block;
  border-radius: 12px;
  position: relative;
  z-index: 1;
  transition: transform 0.3s ease;
}

.qr-wrapper:hover .qr-image {
  transform: scale(1.02);
}

/* Sección Info */
.info-section {
  display: flex;
  flex-direction: column;
  gap: 24px;
  padding-left: 40px;
  animation: slideInRight 0.8s ease-out;
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.animated-card {
  animation: fadeInUp 0.6s ease-out both;
}

.animated-card:nth-child(1) {
  animation-delay: 0.2s;
}

.animated-card:nth-child(2) {
  animation-delay: 0.4s;
}

.animated-card:nth-child(3) {
  animation-delay: 0.6s;
}

.info-card {
  background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  padding: 28px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.info-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
  transition: left 0.5s ease;
}

.info-card:hover::before {
  left: 100%;
}

.info-card:hover {
  background: #ffffff;
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  border-color: #cbd5e0;
}

.card-icon-wrapper {
  margin-bottom: 16px;
}

.card-icon {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
  animation: iconBounce 2s ease-in-out infinite;
}

@keyframes iconBounce {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-5px);
  }
}

.card-icon svg {
  width: 24px;
  height: 24px;
}

.info-row {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.info-label {
  font-size: 13px;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.8px;
}

.info-value {
  font-size: 20px;
  font-weight: 700;
  color: #1e293b;
}

.address-row {
  gap: 16px;
}

.address-group {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.address-text {
  font-family: 'Courier New', 'Courier', monospace;
  font-size: 14px;
  color: #1f2937;
  background: white;
  padding: 18px 20px;
  border-radius: 12px;
  border: 2px solid #e5e7eb;
  word-break: break-all;
  line-height: 1.6;
  font-weight: 500;
  display: block;
  transition: all 0.3s ease;
}

.address-text:hover {
  border-color: #667eea;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
}

.copy-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 16px 28px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  align-self: flex-start;
  box-shadow: 0 4px 14px rgba(102, 126, 234, 0.3);
  position: relative;
  overflow: hidden;
}

.copy-btn::before {
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

.copy-btn:hover::before {
  width: 300px;
  height: 300px;
}

.copy-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.copy-btn:active {
  transform: translateY(-1px);
}

.copy-btn.copied {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
}

.copy-btn svg {
  width: 18px;
  height: 18px;
  position: relative;
  z-index: 1;
}

.copy-btn span {
  position: relative;
  z-index: 1;
}

.message-card {
  background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
  border: 2px solid #bfdbfe;
  border-left: 5px solid #667eea;
  border-radius: 16px;
  padding: 32px;
  position: relative;
  overflow: hidden;
}

.message-card::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
  animation: rotate 20s linear infinite;
}

@keyframes rotate {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.message-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
  position: relative;
  z-index: 1;
}

.message-icon {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.message-icon svg {
  width: 24px;
  height: 24px;
}

.message-title {
  font-size: 22px;
  font-weight: 800;
  color: #1e293b;
  margin: 0;
  position: relative;
  z-index: 1;
}

.message-text {
  font-size: 16px;
  line-height: 1.8;
  color: #1f2937;
  margin: 0 0 24px 0;
  position: relative;
  z-index: 1;
}

.message-text strong {
  color: #667eea;
  font-weight: 700;
}

.benefits-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  position: relative;
  z-index: 1;
}

.benefit-item {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 15px;
  color: #1e293b;
  font-weight: 600;
}

.benefit-item svg {
  width: 20px;
  height: 20px;
  color: #10b981;
  flex-shrink: 0;
}

/* Responsive */
@media (max-width: 1200px) {
  .donation-content {
    gap: 50px;
    padding: 50px 40px;
  }

  .qr-image {
    width: 380px;
    height: 380px;
  }
}

@media (max-width: 1024px) {
  .donation-content {
    grid-template-columns: 1fr;
    gap: 50px;
    padding: 50px 40px;
  }

  .qr-section {
    border-right: none;
    border-bottom: 2px solid #e5e7eb;
    padding-right: 0;
    padding-bottom: 50px;
  }

  .info-section {
    padding-left: 0;
    padding-top: 0;
  }

  .qr-image {
    width: 350px;
    height: 350px;
  }
}

@media (max-width: 768px) {
  .donation-hero {
    height: 300px;
    margin-bottom: 40px;
  }

  .hero-title {
    font-size: 36px;
  }

  .hero-subtitle {
    font-size: 16px;
  }

  .donation-container {
    padding: 0 15px 30px;
  }

  .back-button {
    margin-bottom: 30px;
    font-size: 14px;
    padding: 10px 20px;
  }

  .donation-content {
    padding: 40px 30px;
    border-radius: 16px;
  }

  .qr-title {
    font-size: 24px;
  }

  .qr-image {
    width: 320px;
    height: 320px;
  }

  .qr-section {
    gap: 25px;
    padding-bottom: 40px;
  }

  .info-section {
    gap: 20px;
  }

  .info-card {
    padding: 24px;
  }

  .message-card {
    padding: 28px;
  }
}

@media (max-width: 480px) {
  .donation-hero {
    height: 250px;
  }

  .hero-title {
    font-size: 28px;
  }

  .hero-subtitle {
    font-size: 14px;
  }

  .donation-content {
    padding: 30px 20px;
  }

  .qr-title {
    font-size: 20px;
  }

  .qr-subtitle {
    font-size: 13px;
  }

  .qr-image {
    width: 280px;
    height: 280px;
  }

  .qr-wrapper {
    padding: 20px;
  }

  .info-value {
    font-size: 18px;
  }

  .address-text {
    font-size: 12px;
    padding: 16px 18px;
  }

  .copy-btn {
    width: 100%;
    padding: 14px 24px;
  }

  .message-title {
    font-size: 20px;
  }

  .message-text {
    font-size: 15px;
  }
}
</style>

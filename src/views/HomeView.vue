<template>
  <div class="home">
    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-background">
        <img :src="heroImage" alt="Background" class="hero-bg-image" loading="eager" decoding="async" fetchpriority="high" />
        <div class="hero-overlay"></div>
      </div>
      <div class="container">
        <div class="hero-content">
          <h1 class="hero-title">
            Revisión Gratuita de
            <span class="highlight">Contratos Legales</span>
          </h1>
          <p class="hero-subtitle">
            Servicio gratuito orientado a jóvenes y adultos. Revisamos y audítamos tus contratos de arrendamiento y anticrético,
            identificando cláusulas abusivas para que realices tus trámites con total claridad y seguridad jurídica.
          </p>
          <div class="hero-buttons">
            <router-link to="/servicios" class="btn btn-primary">Nuestros Servicios</router-link>
            <router-link to="/contacto" class="btn btn-primary">Contactar Ahora</router-link>
          </div>
        </div>
      </div>
      <div class="scroll-indicator">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 5v14M5 12l7 7 7-7"/>
        </svg>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features">
      <div class="container">
        <div class="section-header">
          <h2>¿Por qué elegir nuestro servicio?</h2>
          <p>Orientación integral gratuita para proteger tus derechos en contratos de arrendamiento y anticrético</p>
        </div>
        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <path d="M9 12l2 2 4-4"/>
              </svg>
            </div>
            <h3>Servicio 100% Gratuito</h3>
            <p>Ofrecemos orientación legal sin costo, especialmente para jóvenes y adultos que buscan protección en sus contratos.</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/>
              </svg>
            </div>
            <h3>Detección de Cláusulas Abusivas</h3>
            <p>Identificamos cláusulas que pueden perjudicarte en contratos de arrendamiento y anticrético.</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
              </svg>
            </div>
            <h3>Orientación Integral</h3>
            <p>Brindamos asesoría completa para que realices tus trámites con claridad y seguridad jurídica.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Upload Section -->
    <section class="upload-section">
      <div class="container">
        <div class="upload-content">
          <div class="upload-text">
            <h2>Sube tu contrato para revisión gratuita</h2>
            <p>Como profesional del derecho, reviso tus contratos de arrendamiento y anticrético de forma gratuita. Identifico cláusulas abusivas y verifico que todo esté en el ámbito de la legalidad, brindando orientación integral especialmente para jóvenes y adultos.</p>
            <ul class="security-features">
              <li>✓ Revisión profesional gratuita</li>
              <li>✓ Detección de cláusulas abusivas</li>
              <li>✓ Verificación de legalidad</li>
              <li>✓ Orientación personalizada</li>
              <li>✓ Protección de tus derechos</li>
            </ul>
            <p style="margin-top: 20px; font-size: 16px; color: #2d3748;">
              <strong>Tu documento es procesado bajo los más altos estándares de ciberseguridad</strong>, 
              garantizando la integridad y protección de la información que compartes.
            </p>
          </div>
          <div class="contact-cta">
            <div class="cta-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="17 8 12 3 7 8"/>
                <line x1="12" y1="3" x2="12" y2="15"/>
              </svg>
            </div>
            <h3>¿Listo para comenzar?</h3>
            <p>Envía tu contrato y recibe una revisión profesional gratuita. Nuestro equipo está listo para ayudarte.</p>
            <router-link to="/contacto" class="cta-button">
              <span>Enviar mi Contrato</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"/>
              </svg>
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Donation Section -->
    <section class="donation-section">
      <div class="container">
        <div class="donation-content">
          <div class="donation-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
          </div>
          <h2>Apoya nuestro proyecto</h2>
          <p>Tu contribución nos ayuda a mantener y mejorar nuestra plataforma de seguridad legal. Las donaciones se realizan mediante criptomonedas. Cada donación nos acerca a un futuro más seguro para los documentos jurídicos.</p>
          <DonationWidget />
        </div>
      </div>
    </section>

    <!-- About Service Section -->
    <section class="about-service-section">
      <div class="container">
        <div class="about-service-content">
          <div class="service-image">
            <img :src="featureImage1" alt="Servicio Legal" loading="lazy" decoding="async" />
          </div>
          <div class="service-text">
            <h2>Nuestro Propósito</h2>
            <p>
              Esta página nace con el propósito de ofrecer un servicio gratuito orientado a jóvenes y adultos. 
              Surge al identificar una problemática recurrente en nuestra sociedad: la deficiente redacción de 
              los contratos de arrendamiento y anticrético, los cuales suelen incluir cláusulas abusivas.
            </p>
            <p>
              Mi objetivo es brindar orientación integral a quienes no están familiarizados con estos procesos, 
              asegurando que realicen sus trámites con total claridad y seguridad jurídica.
            </p>
            <div class="service-highlights">
              <div class="highlight-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                  <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <span>Servicio completamente gratuito</span>
              </div>
              <div class="highlight-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4"/>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span>Orientación personalizada</span>
              </div>
              <div class="highlight-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                  <path d="M9 12l2 2 4-4"/>
                </svg>
                <span>Protección de tus derechos</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import DonationWidget from '@/components/DonationWidget.vue'
import heroImage from '@/images/pexels-pavel-danilyuk-8112199.jpg'
import featureImage1 from '@/images/pexels-karola-g-4968561.jpg'
</script>

<style scoped>
.home {
  padding-top: 80px;
}

.hero {
  position: relative;
  min-height: 100vh;
  min-height: clamp(600px, 100vh, 800px);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
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
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(26, 54, 93, 0.9) 0%, rgba(44, 82, 130, 0.8) 100%);
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  position: relative;
  z-index: 1;
}

.hero-content {
  text-align: center;
  color: white;
  max-width: 800px;
  margin: 0 auto;
}

.hero-title {
  font-size: clamp(32px, 5vw, 56px);
  font-weight: 800;
  line-height: 1.2;
  margin-bottom: 24px;
  animation: fadeInUp 0.8s ease;
}

.hero-title .highlight {
  color: #FF8C42;
}

.hero-subtitle {
  font-size: clamp(16px, 2.5vw, 20px);
  line-height: 1.6;
  margin-bottom: 40px;
  opacity: 0.95;
  animation: fadeInUp 0.8s ease 0.2s backwards;
  padding: 0 10px;
}

.hero-buttons {
  display: flex;
  gap: 20px;
  justify-content: center;
  flex-wrap: wrap;
  animation: fadeInUp 0.8s ease 0.4s backwards;
}

.btn {
  padding: 16px 32px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  display: inline-block;
}

.btn-primary {
  background: #ffd700;
  color: #1a365d;
}

.btn-primary:hover {
  background: #ffed4e;
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(255, 215, 0, 0.3);
}

.btn-secondary {
  background: transparent;
  color: white;
  border: 2px solid white;
}

.btn-secondary:hover {
  background: white;
  color: #1a365d;
  transform: translateY(-2px);
}

.scroll-indicator {
  position: absolute;
  bottom: 30px;
  left: 50%;
  transform: translateX(-50%);
  color: white;
  animation: bounce 2s infinite;
  z-index: 1;
}

.scroll-indicator svg {
  width: 30px;
  height: 30px;
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

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% {
    transform: translateX(-50%) translateY(0);
  }
  40% {
    transform: translateX(-50%) translateY(-10px);
  }
  60% {
    transform: translateX(-50%) translateY(-5px);
  }
}

.features {
  padding: clamp(60px, 8vw, 100px) 0;
  background: #f7fafc;
}

.section-header {
  text-align: center;
  margin-bottom: 60px;
}

.section-header h2 {
  font-size: clamp(28px, 4vw, 42px);
  font-weight: 700;
  color: #1a365d;
  margin-bottom: 16px;
}

.section-header p {
  font-size: clamp(16px, 2vw, 18px);
  color: #4a5568;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 30px;
}

.feature-card {
  background: #d6d9dc;
  padding: 40px 30px;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  text-align: center;
}

.feature-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.feature-icon {
  width: 60px;
  height: 60px;
  margin: 0 auto 20px;
  background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.feature-icon svg {
  width: 28px;
  height: 28px;
}

.feature-card h3 {
  font-size: clamp(20px, 2.5vw, 24px);
  font-weight: 600;
  color: #1a365d;
  margin-bottom: 16px;
}

.feature-card p {
  color: #4a5568;
  line-height: 1.6;
  font-size: clamp(14px, 1.8vw, 16px);
}

.upload-section {
  padding: clamp(60px, 8vw, 100px) 0;
  background: white;
}

.upload-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: center;
}

.upload-text h2 {
  font-size: clamp(24px, 4vw, 36px);
  font-weight: 700;
  color: #1a365d;
  margin-bottom: 24px;
}

.upload-text p {
  font-size: clamp(16px, 2vw, 18px);
  color: #4a5568;
  line-height: 1.7;
  margin-bottom: 30px;
}

.security-features {
  list-style: none;
  padding: 0;
}

.security-features li {
  font-size: 16px;
  color: #2d3748;
  margin-bottom: 12px;
  padding-left: 10px;
}

.contact-cta {
  background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
  border-radius: 16px;
  padding: 60px 40px;
  text-align: center;
  color: white;
  box-shadow: 0 10px 40px rgba(26, 54, 93, 0.3);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.contact-cta::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
  animation: pulse 3s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
    opacity: 0.5;
  }
  50% {
    transform: scale(1.1);
    opacity: 0.8;
  }
}

.contact-cta:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 50px rgba(26, 54, 93, 0.4);
}

.cta-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 30px;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  z-index: 1;
  backdrop-filter: blur(10px);
}

.cta-icon svg {
  width: 40px;
  height: 40px;
  color: white;
}

.contact-cta h3 {
  font-size: clamp(24px, 3vw, 32px);
  font-weight: 700;
  margin-bottom: 16px;
  position: relative;
  z-index: 1;
}

.contact-cta p {
  font-size: clamp(16px, 2vw, 18px);
  margin-bottom: 35px;
  opacity: 0.95;
  line-height: 1.6;
  position: relative;
  z-index: 1;
}

.cta-button {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  padding: 18px 40px;
  background: #ffd700;
  color: #1a365d;
  text-decoration: none;
  border-radius: 12px;
  font-size: 18px;
  font-weight: 700;
  transition: all 0.3s ease;
  position: relative;
  z-index: 1;
  box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}

.cta-button:hover {
  background: #ffed4e;
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4);
}

.cta-button svg {
  width: 20px;
  height: 20px;
  transition: transform 0.3s ease;
}

.cta-button:hover svg {
  transform: translateX(5px);
}

.donation-section {
  padding: clamp(60px, 8vw, 100px) 0;
  background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
  color: white;
}

.donation-content {
  text-align: center;
  max-width: 700px;
  margin: 0 auto;
}

.donation-icon {
  width: 60px;
  height: 60px;
  margin: 0 auto 25px;
  color: #ffd700;
}

.donation-icon svg {
  width: 100%;
  height: 100%;
}

.donation-content h2 {
  font-size: clamp(28px, 4vw, 42px);
  font-weight: 700;
  margin-bottom: 24px;
}

.donation-content p {
  font-size: clamp(16px, 2vw, 18px);
  line-height: 1.7;
  margin-bottom: 40px;
  opacity: 0.95;
}

.about-service-section {
  padding: clamp(60px, 8vw, 100px) 0;
  background: white;
}

.about-service-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: center;
}

.service-image {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
  max-height: 500px;
}

.service-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  max-height: 500px;
  min-height: 300px;
}

.service-text h2 {
  font-size: clamp(24px, 4vw, 36px);
  font-weight: 700;
  color: #1a365d;
  margin-bottom: 30px;
}

.service-text p {
  font-size: clamp(16px, 2vw, 18px);
  line-height: 1.8;
  color: #4a5568;
  margin-bottom: 20px;
}

.service-highlights {
  margin-top: 30px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.highlight-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 15px;
  background: #e2e8f0;
  border-radius: 8px;
  border-left: 4px solid #1a365d;
}

.highlight-item svg {
  width: 20px;
  height: 20px;
  color: #1a365d;
  flex-shrink: 0;
}

.highlight-item span {
  font-size: clamp(14px, 2vw, 16px);
  font-weight: 500;
  color: #2d3748;
}

/* Responsive Design */
@media (max-width: 968px) {
  .hero {
    min-height: clamp(500px, 80vh, 700px);
  }

  .hero-title {
    font-size: clamp(28px, 6vw, 42px);
    padding: 0 10px;
  }

  .hero-subtitle {
    padding: 0 15px;
    margin-bottom: 30px;
  }

  .hero-buttons {
    gap: 15px;
    padding: 0 10px;
  }

  .btn {
    padding: 14px 28px;
    font-size: 14px;
  }

  .upload-content {
    grid-template-columns: 1fr;
    gap: 40px;
  }

  .contact-cta {
    padding: 50px 30px;
  }

  .features-grid {
    grid-template-columns: 1fr;
    gap: 25px;
  }

  .feature-card {
    padding: 30px 20px;
  }

  .about-service-content {
    grid-template-columns: 1fr;
    gap: 40px;
  }

  .service-image {
    max-height: 350px;
  }

  .service-image img {
    min-height: 250px;
    max-height: 350px;
  }

  .section-header {
    margin-bottom: 40px;
    padding: 0 10px;
  }

  .donation-content {
    padding: 0 20px;
  }
}

@media (max-width: 480px) {
  .home {
    padding-top: 70px;
  }

  .hero {
    min-height: clamp(450px, 100vh, 600px);
  }

  .hero-title {
    font-size: clamp(24px, 7vw, 32px);
    margin-bottom: 16px;
    line-height: 1.3;
  }

  .hero-subtitle {
    font-size: 15px;
    margin-bottom: 25px;
  }

  .hero-buttons {
    flex-direction: column;
    width: 100%;
    padding: 0 15px;
  }

  .btn {
    width: 100%;
    padding: 12px 24px;
  }

  .features {
    padding: 50px 0;
  }

  .upload-section {
    padding: 50px 0;
  }

  .about-service-section {
    padding: 50px 0;
  }

  .donation-section {
    padding: 50px 0;
  }

  .feature-icon {
    width: 50px;
    height: 50px;
  }

  .feature-icon svg {
    width: 24px;
    height: 24px;
  }

  .donation-icon {
    width: 50px;
    height: 50px;
  }

  .scroll-indicator {
    bottom: 20px;
  }

  .scroll-indicator svg {
    width: 24px;
    height: 24px;
  }

  .security-features {
    font-size: 14px;
  }

  .upload-text h2,
  .service-text h2 {
    font-size: 22px;
  }
}
</style>


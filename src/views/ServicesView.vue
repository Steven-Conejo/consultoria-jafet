<template>
  <div class="services">
    <!-- Hero Section -->
    <section class="services-hero">
      <div class="hero-background">
        <img :src="heroImage" alt="Background" class="hero-bg-image" loading="eager" decoding="async" fetchpriority="high" />
        <div class="hero-overlay"></div>
      </div>
      <div class="container">
        <h1>Nuestros Servicios</h1>
        <p class="subtitle">Revisión gratuita de contratos de arrendamiento y anticrético</p>
      </div>
    </section>

    <!-- Main Services -->
    <section class="main-services">
      <div class="container">
        <div class="services-grid">
          <div class="service-card featured">
            <div class="service-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
              </svg>
            </div>
            <h2>Revisión de Contratos de Arrendamiento</h2>
            <p>
              Como profesional del derecho, reviso detalladamente tus contratos de arrendamiento 
              para identificar cláusulas abusivas, términos desfavorables y asegurar que todo 
              se mueva dentro del ámbito de la legalidad. Servicio 100% gratuito.
            </p>
            <ul class="service-features">
              <li>Revisión profesional completa</li>
              <li>Detección de cláusulas abusivas</li>
              <li>Verificación de legalidad</li>
              <li>Orientación personalizada</li>
            </ul>
            <router-link to="/contacto" class="service-btn">Enviar mi Contrato</router-link>
          </div>

          <div class="service-card featured">
            <div class="service-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                <polyline points="7.5 4.21 12 6.81 16.5 4.21"/>
                <polyline points="7.5 19.79 7.5 14.6 3 12"/>
                <polyline points="21 12 16.5 14.6 16.5 19.79"/>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                <line x1="12" y1="22.08" x2="12" y2="12"/>
              </svg>
            </div>
            <h2>Revisión de Contratos Anticrético</h2>
            <p>
              Análisis especializado de contratos anticrético para jóvenes y adultos que necesitan 
              orientación. Verifico que no contengan cláusulas abusivas y que todo esté conforme 
              a la ley vigente.
            </p>
            <ul class="service-features">
              <li>Análisis de términos anticrético</li>
              <li>Validación de condiciones</li>
              <li>Protección de tus derechos</li>
              <li>Asesoría legal gratuita</li>
            </ul>
            <router-link to="/contacto" class="service-btn">Enviar mi Contrato</router-link>
          </div>

          <div class="service-card featured">
            <div class="service-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
              </svg>
            </div>
            <h2>Detección de Cláusulas Abusivas</h2>
            <p>
              Identifico y explico las cláusulas abusivas que pueden perjudicarte en tus contratos. 
              Te ayudo a entender cada término para que puedas tomar decisiones informadas y 
              proteger tus derechos como inquilino.
            </p>
            <ul class="service-features">
              <li>Identificación de cláusulas problemáticas</li>
              <li>Explicación clara de términos legales</li>
              <li>Recomendaciones para negociar</li>
              <li>Protección de tus intereses</li>
            </ul>
            <router-link to="/contacto" class="service-btn">Enviar mi Contrato</router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Process Section -->
    <section class="process-section">
      <div class="container">
        <h2 class="section-title">Nuestro Proceso</h2>
        <p class="section-subtitle">Cómo funciona la revisión de tu contrato en 4 pasos simples</p>
        <div class="process-steps">
          <div class="step">
            <div class="step-number">1</div>
            <h3>Sube tu Contrato</h3>
            <p>Sube tu contrato de arrendamiento o anticrético en formato Word o PDF a través de nuestra plataforma segura.</p>
          </div>
          <div class="step-arrow">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="9 18 15 12 9 6"/>
            </svg>
          </div>
          <div class="step">
            <div class="step-number">2</div>
            <h3>Validación de Seguridad</h3>
            <p>Nuestro sistema verifica que el documento esté libre de amenazas y sea válido antes de procesarlo.</p>
          </div>
          <div class="step-arrow">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="9 18 15 12 9 6"/>
            </svg>
          </div>
          <div class="step">
            <div class="step-number">3</div>
            <h3>Revisión Legal Profesional</h3>
            <p>Como profesional del derecho, reviso detalladamente tu contrato identificando cláusulas abusivas y verificando legalidad.</p>
          </div>
          <div class="step-arrow">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="9 18 15 12 9 6"/>
            </svg>
          </div>
          <div class="step">
            <div class="step-number">4</div>
            <h3>Orientación y Recomendaciones</h3>
            <p>Recibes un análisis detallado con explicaciones claras y recomendaciones para proteger tus derechos.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Security Features -->
    <section class="security-features-section">
      <div class="container">
        <h2 class="section-title">Características del Servicio</h2>
        <div class="security-grid">
          <div class="security-item">
            <div class="security-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
              </svg>
            </div>
            <h3>100% Gratuito</h3>
            <p>Servicio completamente gratuito para jóvenes y adultos</p>
          </div>
          <div class="security-item">
            <div class="security-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <path d="M9 12l2 2 4-4"/>
              </svg>
            </div>
            <h3>Protección de Datos</h3>
            <p>Tus documentos son tratados con confidencialidad y seguridad</p>
          </div>
          <div class="security-item">
            <div class="security-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
              </svg>
            </div>
            <h3>Orientación Personalizada</h3>
            <p>Asesoría adaptada especialmente para quienes no conocen el ámbito legal</p>
          </div>
          <div class="security-item">
            <div class="security-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <path d="M14 2v6h6M16 13H8M16 17H8"/>
              </svg>
            </div>
            <h3>Revisión Profesional</h3>
            <p>Análisis realizado por un profesional del derecho comprometido</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import heroImage from '@/images/pexels-pavel-danilyuk-8112136.jpg'
</script>

<style scoped>
.services {
  padding-top: 80px;
}

.services-hero {
  position: relative;
  color: white;
  padding: 100px 0;
  text-align: center;
  overflow: hidden;
}

.services-hero .hero-background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 0;
}

.services-hero .hero-bg-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: blur(2px);
  transform: scale(1.1);
}

.services-hero .hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(26, 54, 93, 0.85) 0%, rgba(44, 82, 130, 0.75) 100%);
}

.services-hero h1 {
  font-size: 48px;
  font-weight: 700;
  margin-bottom: 10px;
}

.services-hero .subtitle {
  font-size: 20px;
  opacity: 0.9;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  position: relative;
  z-index: 1;
}

.main-services {
  padding: 100px 0;
  background: #f7fafc;
}

.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 30px;
}

.service-card {
  background: white;
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.service-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.service-card.featured {
  background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
  color: white;
}

.service-icon {
  width: 70px;
  height: 70px;
  margin-bottom: 24px;
  background: rgba(26, 54, 93, 0.1);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #1a365d;
}

.service-card.featured .service-icon {
  background: rgba(255, 255, 255, 0.2);
  color: white;
}

.service-icon svg {
  width: 35px;
  height: 35px;
}

.service-card h2 {
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 20px;
  color: #1a365d;
}

.service-card.featured h2 {
  color: white;
}

.service-card p {
  font-size: 16px;
  line-height: 1.7;
  margin-bottom: 24px;
  color: #4a5568;
}

.service-card.featured p {
  color: rgba(255, 255, 255, 0.9);
}

.service-features {
  list-style: none;
  padding: 0;
  margin-bottom: 30px;
}

.service-features li {
  padding: 10px 0;
  color: #4a5568;
  border-bottom: 1px solid #e2e8f0;
}

.service-card.featured .service-features li {
  color: rgba(255, 255, 255, 0.9);
  border-bottom-color: rgba(255, 255, 255, 0.2);
}

.service-features li:last-child {
  border-bottom: none;
}

.service-btn {
  display: inline-block;
  padding: 14px 28px;
  background: #1a365d;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.service-card.featured .service-btn {
  background: #ffd700;
  color: #1a365d;
}

.service-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(26, 54, 93, 0.3);
}

.service-card.featured .service-btn:hover {
  box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
}

.process-section {
  padding: 100px 0;
  background: white;
}

.section-title {
  font-size: 42px;
  font-weight: 700;
  color: #1a365d;
  text-align: center;
  margin-bottom: 20px;
}

.section-subtitle {
  font-size: 20px;
  color: #4a5568;
  text-align: center;
  margin-bottom: 60px;
}

.process-steps {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 30px;
}

.step {
  flex: 1;
  min-width: 200px;
  text-align: center;
  padding: 30px;
  background: #d6d9dc;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.step:hover {
  transform: translateY(-10px);
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.step-number {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  font-weight: 700;
  margin: 0 auto 20px;
}

.step h3 {
  font-size: 20px;
  font-weight: 600;
  color: #1a365d;
  margin-bottom: 12px;
}

.step p {
  color: #4a5568;
  line-height: 1.6;
}

.step-arrow {
  color: #cbd5e0;
  width: 40px;
  height: 40px;
}

.step-arrow svg {
  width: 100%;
  height: 100%;
}

.security-features-section {
  padding: 100px 0;
  background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
  color: white;
}

.security-features-section .section-title {
  color: white;
}

.security-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 40px;
}

.security-item {
  text-align: center;
  padding: 30px;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border-radius: 12px;
  transition: all 0.3s ease;
}

.security-item:hover {
  background: rgba(255, 255, 255, 0.15);
  transform: translateY(-5px);
}

.security-icon {
  width: 60px;
  height: 60px;
  margin: 0 auto 20px;
  color: #ffd700;
}

.security-icon svg {
  width: 100%;
  height: 100%;
}

.security-item h3 {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 12px;
}

.security-item p {
  opacity: 0.9;
  line-height: 1.6;
}

@media (max-width: 968px) {
  .process-steps {
    flex-direction: column;
  }

  .step-arrow {
    transform: rotate(90deg);
  }

  .services-hero {
    padding: 60px 0;
  }

  .services-hero h1 {
    font-size: 36px;
  }

  .services-hero .subtitle {
    font-size: 18px;
  }
}
</style>


<template>
  <div class="about">
    <!-- Hero Section -->
    <section class="about-hero">
      <div class="hero-background">
        <img :src="heroImage" alt="Background" class="hero-bg-image" loading="eager" decoding="async" fetchpriority="high" />
        <div class="hero-overlay"></div>
      </div>
      <div class="container">
        <h1>Sobre Nosotros</h1>
        <p class="subtitle">Transformando el futuro de la seguridad legal digital</p>
      </div>
    </section>

    <!-- Mission Section -->
    <section class="mission-section">
      <div class="container">
        <div class="content-grid">
          <div class="text-content">
            <h2>Nuestra Misión</h2>
            <p>
              Esta plataforma nace con el propósito de ofrecer un servicio gratuito orientado 
              a jóvenes y adultos. Surge al identificar una problemática recurrente en nuestra 
              sociedad: la deficiente redacción de los contratos de arrendamiento y anticrético, 
              los cuales suelen incluir cláusulas abusivas.
            </p>
            <p>
              Nuestro compromiso es brindar orientación integral a quienes no están familiarizados 
              con estos procesos, asegurando que realicen sus trámites con total claridad y 
              seguridad jurídica. Trabajamos para proteger tus derechos y ayudarte a tomar 
              decisiones informadas.
            </p>
          </div>
          <div class="image-content">
            <img :src="missionImage" alt="Misión" loading="lazy" decoding="async" />
          </div>
        </div>
      </div>
    </section>

    <!-- Vision Section -->
    <section class="vision-section">
      <div class="container">
        <div class="content-grid reverse">
          <div class="text-content">
            <h2>Nuestra Visión</h2>
            <p>
              Visualizamos un futuro donde todas las personas, especialmente jóvenes y adultos 
              que inician en este mundo, tengan acceso a información legal 
              clara y transparente. Aspiramos a ser el punto de referencia para la revisión 
              gratuita de contratos de arrendamiento y anticrético.
            </p>
            <p>
              Creemos en la democratización del acceso a la asesoría legal, haciendo que cada 
              persona pueda entender sus contratos y proteger sus derechos, sin importar su 
              nivel de conocimiento jurídico previo.
            </p>
          </div>
          <div class="image-content">
            <img :src="visionImage" alt="Visión" loading="lazy" decoding="async" />
          </div>
        </div>
      </div>
    </section>

    <!-- Values Section -->
    <section class="values-section">
      <div class="container">
        <h2 class="section-title">Nuestros Valores</h2>
        <div class="values-grid">
          <div class="value-card">
            <div class="value-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <path d="M9 12l2 2 4-4"/>
              </svg>
            </div>
            <h3>Protección de Derechos</h3>
            <p>Nos comprometemos a defender y proteger los derechos de los inquilinos, identificando cláusulas abusivas en contratos de arrendamiento y anticrético.</p>
          </div>
          <div class="value-card">
            <div class="value-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
              </svg>
            </div>
            <h3>Transparencia Legal</h3>
            <p>Explicamos de manera clara y sencilla cada cláusula de tu contrato para que comprendas completamente tus derechos y obligaciones legales.</p>
          </div>
          <div class="value-card">
            <div class="value-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
              </svg>
            </div>
            <h3>Orientación Integral</h3>
            <p>Brindamos asesoría legal completa especialmente para jóvenes y adultos, guiándote en cada paso del proceso con seguridad jurídica.</p>
          </div>
          <div class="value-card">
            <div class="value-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
              </svg>
            </div>
            <h3>Servicio Gratuito</h3>
            <p>Ofrecemos revisión profesional de contratos sin costo alguno, porque creemos que todos merecen acceder a orientación legal de calidad.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
      <div class="container">
        <h2 class="section-title">Nuestro Equipo</h2>
        <p class="section-subtitle">Profesionales expertos en derecho y tecnología</p>
        <div class="team-grid">
          <div class="team-card">
            <div class="team-avatar">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
            </div>
            <h3>Equipo Legal</h3>
            <p class="role">Especialistas en Derecho</p>
            <p>Profesionales del derecho especializados en legislación, con amplia experiencia en revisión de contratos de arrendamiento y anticrético. Identificamos cláusulas abusivas, verificamos la legalidad de los términos y brindamos orientación integral para proteger los derechos de los inquilinos.</p>
          </div>
          <div class="team-card">
            <div class="team-avatar">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                <line x1="8" y1="21" x2="16" y2="21"/>
                <line x1="12" y1="17" x2="12" y2="21"/>
              </svg>
            </div>
            <h3>Equipo Tecnológico</h3>
            <p class="role">Desarrollador de Software</p>
            <p>Graduado en Informática Empresarial de la UCR. Con más de un año de experiencia en desarrollo de proyectos web. Especialista en tecnologías como PHP, JavaScript, Vue.js, React, Laravel, bases de datos, entre otras tecnologías modernas. Experiencia en desarrollo y despliegue de proyectos web completos.</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import missionImage from '@/images/pexels-karola-g-4968561.jpg'
import visionImage from '@/images/pexels-pavel-danilyuk-8112136.jpg'
import heroImage from '@/images/pexels-karola-g-4968561.jpg'
</script>

<style scoped>
.about {
  padding-top: 80px;
}

.about-hero {
  position: relative;
  color: white;
  padding: 100px 0;
  text-align: center;
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
  filter: blur(2px);
  transform: scale(1.1);
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(26, 54, 93, 0.85) 0%, rgba(44, 82, 130, 0.75) 100%);
}

.about-hero h1 {
  font-size: 48px;
  font-weight: 700;
  margin-bottom: 10px;
}

.about-hero .subtitle {
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

.mission-section,
.vision-section {
  padding: 100px 0;
}

.mission-section {
  background: white;
}

.vision-section {
  background: #f7fafc;
}

.content-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: center;
}

.content-grid.reverse {
  direction: rtl;
}

.content-grid.reverse > * {
  direction: ltr;
}

.text-content h2 {
  font-size: 42px;
  font-weight: 700;
  color: #1a365d;
  margin-bottom: 30px;
}

.text-content p {
  font-size: 18px;
  line-height: 1.8;
  color: #4a5568;
  margin-bottom: 20px;
}

.image-content {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
}

.image-content:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 50px rgba(0, 0, 0, 0.25);
}

.image-content img {
  width: 100%;
  height: 400px;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.image-content:hover img {
  transform: scale(1.05);
}

.values-section {
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

.values-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 30px;
}

.value-card {
  text-align: center;
  padding: 40px 30px;
  background: #d6d9dc;
  border-radius: 12px;
  transition: all 0.3s ease;
}

.value-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.value-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 24px;
  background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.value-icon svg {
  width: 40px;
  height: 40px;
}

.value-card h3 {
  font-size: 24px;
  font-weight: 600;
  color: #1a365d;
  margin-bottom: 16px;
}

.value-card p {
  color: #4a5568;
  line-height: 1.6;
}

.team-section {
  padding: 100px 0;
  background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
  color: white;
}

.team-section .section-title,
.team-section .section-subtitle {
  color: white;
}

.team-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 40px;
}

.team-card {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  padding: 40px;
  border-radius: 12px;
  text-align: center;
  transition: all 0.3s ease;
}

.team-card:hover {
  background: rgba(255, 255, 255, 0.15);
  transform: translateY(-10px);
}

.team-avatar {
  width: 100px;
  height: 100px;
  margin: 0 auto 24px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.team-avatar svg {
  width: 50px;
  height: 50px;
}

.team-card h3 {
  font-size: 24px;
  font-weight: 600;
  margin-bottom: 8px;
}

.team-card .role {
  color: #ffd700;
  font-weight: 600;
  margin-bottom: 16px;
}

.team-card p {
  line-height: 1.6;
  opacity: 0.9;
}

@media (max-width: 968px) {
  .content-grid {
    grid-template-columns: 1fr;
  }

  .content-grid.reverse {
    direction: ltr;
  }

  .about-hero {
    padding: 60px 0;
  }

  .about-hero h1 {
    font-size: 36px;
  }

  .about-hero .subtitle {
    font-size: 18px;
  }

  .text-content h2 {
    font-size: 32px;
  }

  .mission-section,
  .vision-section {
    padding: 60px 0;
  }

  .vision-section {
    padding-bottom: 40px;
  }

  .values-section {
    padding-top: 40px;
    padding-bottom: 60px;
  }
}

@media (max-width: 480px) {
  .mission-section,
  .vision-section {
    padding: 40px 0;
  }

  .vision-section {
    padding-bottom: 30px;
  }

  .values-section {
    padding-top: 30px;
    padding-bottom: 50px;
  }

  .section-title {
    font-size: 28px;
    margin-bottom: 30px;
  }
}
</style>


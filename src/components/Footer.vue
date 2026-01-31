<template>
  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <div class="footer-section">
          <h3>LegisAudit</h3>
          <p>Servicio gratuito de revisión legal de contratos de arrendamiento y anticrético. Como profesional del derecho, brindo orientación integral especialmente para jóvenes y adultos, identificando cláusulas abusivas y asegurando que todo esté en el ámbito de la legalidad.</p>
        </div>
        <div class="footer-section">
          <h4>Enlaces Rápidos</h4>
          <ul>
            <li><router-link to="/">Inicio</router-link></li>
            <li><router-link to="/nosotros">Nosotros</router-link></li>
            <li><router-link to="/servicios">Servicios</router-link></li>
            <li><router-link to="/contacto">Contáctenos</router-link></li>
          </ul>
        </div>
        <div class="footer-section">
          <h4>Servicios</h4>
          <ul>
            <li>Revisión de Contratos de Arrendamiento</li>
            <li>Análisis de Contratos Anticrético</li>
            <li>Detección de Cláusulas Abusivas</li>
            <li>Orientación Legal Integral</li>
          </ul>
        </div>
        <div class="footer-section">
          <h4>Contacto</h4>
          <ul>
            <li>📧 servicioprofesionalabogadojcgy@gmail.com</li>
            <li>🕒 Lun - Vie: 9:00 - 18:00</li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2026 LegisAudit. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.footer {
  background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
  color: white;
  padding: 30px 0 12px;
  margin-top: 80px;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.footer-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 25px;
  margin-bottom: 25px;
}

.footer-section h3 {
  font-size: 18px;
  margin-bottom: 12px;
  font-weight: 700;
}

.footer-section h4 {
  font-size: 15px;
  margin-bottom: 12px;
  font-weight: 600;
}

.footer-section p {
  line-height: 1.5;
  margin-bottom: 12px;
  opacity: 0.9;
  font-size: 13px;
}

.footer-section ul {
  list-style: none;
  padding: 0;
}

.footer-section ul li {
  margin-bottom: 8px;
  opacity: 0.9;
  transition: opacity 0.3s ease;
  font-size: 13px;
}

.footer-section ul li:hover {
  opacity: 1;
}

.footer-section a {
  color: white;
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-section a:hover {
  color: #cbd5e0;
}

.social-links {
  display: flex;
  gap: 15px;
  margin-top: 20px;
}

.social-link {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.social-link svg {
  width: 20px;
  height: 20px;
}

.social-link:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-3px);
}

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  padding-top: 12px;
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  font-size: 12px;
}

.footer-links {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
  font-size: 13px;
}

.footer-links a {
  color: white;
  text-decoration: none;
  opacity: 0.8;
  transition: opacity 0.3s ease;
}

.footer-links a:hover {
  opacity: 1;
}

@media (max-width: 768px) {
  .footer-bottom {
    flex-direction: column;
    text-align: center;
  }
}
</style>


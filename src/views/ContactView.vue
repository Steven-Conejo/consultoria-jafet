<template>
  <div class="contact">
    <!-- Hero Section -->
    <section class="contact-hero">
      <div class="hero-background">
        <img :src="heroImage" alt="Background" class="hero-bg-image" loading="eager" decoding="async" fetchpriority="high" />
        <div class="hero-overlay"></div>
      </div>
      <div class="container">
        <h1>Contáctenos</h1>
        <p class="subtitle">Estamos aquí para ayudarte. Ponte en contacto con nuestro equipo</p>
      </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
      <div class="container">
        <div class="contact-content">
          <div class="contact-info">
            <h2>Información de Contacto</h2>
            <p class="contact-description">
              ¿Tienes un contrato de arrendamiento o anticrético que necesitas revisar? 
              ¿Tienes preguntas sobre cláusulas abusivas o términos legales? Estoy aquí para 
              ayudarte con orientación legal gratuita.
            </p>

            <div class="contact-details">
              <div class="contact-item">
                <div class="contact-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                  </svg>
                </div>
                <div>
                  <h3>Email</h3>
                  <p>servicioprofesionalabogadojcgy@gmail.com</p>
                  <p style="margin-top: 10px; font-size: 14px; opacity: 0.8;">Horario: Lun - Vie: 9:00 - 18:00</p>
                </div>
              </div>
            </div>

            <div class="contact-image">
              <img :src="contactImage" alt="Contacto Legal" loading="lazy" decoding="async" />
            </div>
          </div>

          <div class="contact-form-container">
            <form @submit.prevent="handleSubmit" class="contact-form">
              <h2>Envíanos un Mensaje</h2>
              
              <div class="form-group">
                <label for="name">Nombre Completo *</label>
                <input
                  type="text"
                  id="name"
                  v-model="form.name"
                  required
                  placeholder="Tu nombre"
                />
              </div>

              <div class="form-group">
                <label for="email">Email *</label>
                <input
                  type="email"
                  id="email"
                  v-model="form.email"
                  required
                  placeholder="tu@email.com"
                />
              </div>

              <div class="form-group">
                <label for="phone">Teléfono</label>
                <input
                  type="tel"
                  id="phone"
                  v-model="form.phone"
                  placeholder="+1 (555) 123-4567"
                />
              </div>

              <div class="form-group">
                <label for="subject">Asunto *</label>
                <select id="subject" v-model="form.subject" required>
                  <option value="">Selecciona un asunto</option>
                  <option value="auditoria">Auditoría de Documentos</option>
                  <option value="arrendamiento">Revisión de Contratos de Arrendamiento</option>
                  <option value="anticredito">Revisión de Contratos de Anticredito</option>
                  <option value="redaccion">Redacción de Contratos</option>
                  <option value="general">Consulta General</option>
                  <option value="otro">Otro</option>
                </select>
              </div>

              <div class="form-group">
                <label for="message">Mensaje *</label>
                <textarea
                  id="message"
                  v-model="form.message"
                  required
                  rows="6"
                  placeholder="Cuéntanos en qué podemos ayudarte..."
                ></textarea>
              </div>

              <div class="form-group">
                <label>Adjuntar Contrato (Opcional)</label>
                <FileUpload ref="fileUploadRef" mode="select" :compact="true" @file-selected="handleFileSelect" @file-removed="handleFileRemove" />
              </div>

              <button type="submit" class="submit-btn" :disabled="isSubmitting">
                <span v-if="!isSubmitting">Enviar Mensaje</span>
                <span v-else>Enviando...</span>
              </button>

              <div v-if="submitSuccess" class="success-message">
                ¡Mensaje enviado exitosamente! Te contactaremos pronto.
              </div>

              <div v-if="submitError" class="error-message">
                {{ submitError }}
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import FileUpload from '@/components/FileUpload.vue'
import { apiUrl } from '@/config/api'
import contactImage from '@/images/pexels-sora-shimazaki-5668473.jpg'
import heroImage from '@/images/pexels-pavel-danilyuk-8112199.jpg'

const form = ref({
  name: '',
  email: '',
  phone: '',
  subject: '',
  message: ''
})

const selectedFile = ref<File | null>(null)
const fileUploadRef = ref<InstanceType<typeof FileUpload> | null>(null)
const isSubmitting = ref(false)
const submitSuccess = ref(false)
const submitError = ref('')

const handleFileSelect = (file: File) => {
  selectedFile.value = file
}

const handleFileRemove = () => {
  selectedFile.value = null
}

const handleSubmit = async () => {
  isSubmitting.value = true
  submitSuccess.value = false
  submitError.value = ''

  try {
    // Crear FormData para enviar archivo si existe
    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('email', form.value.email)
    formData.append('phone', form.value.phone)
    formData.append('subject', form.value.subject)
    formData.append('message', form.value.message)
    
    if (selectedFile.value) {
      formData.append('file', selectedFile.value)
    }

    const response = await fetch(apiUrl('/contact.php'), {
      method: 'POST',
      body: formData
    })

    // Verificar si la respuesta es JSON válido
    const contentType = response.headers.get('content-type')
    if (!contentType || !contentType.includes('application/json')) {
      // Si no es JSON, leer como texto para ver el error
      const textResponse = await response.text()
      console.error('Respuesta no JSON:', textResponse)
      throw new Error('El servidor respondió con un formato inesperado. Por favor, contacta al administrador.')
    }

    const result = await response.json()

    if (response.ok && result.success) {
      submitSuccess.value = true
      form.value = {
        name: '',
        email: '',
        phone: '',
        subject: '',
        message: ''
      }
      selectedFile.value = null
      // Resetear el componente FileUpload
      if (fileUploadRef.value) {
        fileUploadRef.value.resetUpload()
      }
    } else {
      // Mostrar el mensaje de error del servidor
      const errorMsg = result.message || `Error ${response.status}: ${response.statusText}`
      throw new Error(errorMsg)
    }
  } catch (error) {
    console.error('Error al enviar formulario:', error)
    // Mostrar mensaje de error más específico
    if (error instanceof TypeError && error.message.includes('fetch')) {
      submitError.value = 'Error de conexión. Por favor, verifica tu conexión a internet e intenta nuevamente.'
    } else if (error instanceof Error) {
      submitError.value = error.message
    } else {
      submitError.value = 'Error al enviar el mensaje. Por favor, inténtalo de nuevo.'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
.contact {
  padding-top: 80px;
}

.contact-hero {
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

.contact-hero h1 {
  font-size: 48px;
  font-weight: 700;
  margin-bottom: 10px;
}

.contact-hero .subtitle {
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

.contact-section {
  padding: 100px 0;
  background: #f7fafc;
}

.contact-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: start;
}

.contact-info h2 {
  font-size: 36px;
  font-weight: 700;
  color: #1a365d;
  margin-bottom: 20px;
}

.contact-description {
  font-size: 18px;
  color: #4a5568;
  line-height: 1.7;
  margin-bottom: 40px;
}

.contact-details {
  margin-bottom: 40px;
}

.contact-image {
  margin-top: 30px;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  max-height: 400px;
}

.contact-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.contact-item {
  display: flex;
  gap: 20px;
  margin-bottom: 30px;
  align-items: start;
}

.contact-icon {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  flex-shrink: 0;
}

.contact-icon svg {
  width: 24px;
  height: 24px;
}

.contact-item h3 {
  font-size: 20px;
  font-weight: 600;
  color: #1a365d;
  margin-bottom: 8px;
}

.contact-item p {
  color: #4a5568;
  line-height: 1.6;
}

.social-section {
  padding-top: 30px;
  border-top: 1px solid #e2e8f0;
}

.social-section h3 {
  font-size: 20px;
  font-weight: 600;
  color: #1a365d;
  margin-bottom: 20px;
}

.social-links {
  display: flex;
  gap: 15px;
}

.social-link {
  width: 45px;
  height: 45px;
  background: #1a365d;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  transition: all 0.3s ease;
}

.social-link svg {
  width: 24px;
  height: 24px;
}

.social-link:hover {
  background: #2c5282;
  transform: translateY(-3px);
}

.contact-form-container {
  background: white;
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.contact-form h2 {
  font-size: 28px;
  font-weight: 700;
  color: #1a365d;
  margin-bottom: 30px;
}

.form-group {
  margin-bottom: 24px;
}

.form-group label {
  display: block;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 8px;
  font-size: 14px;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 14px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 16px;
  transition: all 0.3s ease;
  font-family: inherit;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #1a365d;
  box-shadow: 0 0 0 3px rgba(26, 54, 93, 0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 100px;
  padding: 12px;
  font-size: 15px;
}

.submit-btn {
  width: 100%;
  padding: 16px;
  background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 18px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(26, 54, 93, 0.3);
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.success-message {
  margin-top: 20px;
  padding: 15px;
  background: #f0fff4;
  color: #22543d;
  border-radius: 8px;
  border: 1px solid #9ae6b4;
  text-align: center;
}

.error-message {
  margin-top: 20px;
  padding: 15px;
  background: #fff5f5;
  color: #742a2a;
  border-radius: 8px;
  border: 1px solid #fc8181;
  text-align: center;
}

@media (max-width: 968px) {
  .contact-content {
    grid-template-columns: 1fr;
    gap: 40px;
  }

  .contact-hero {
    padding: 60px 0;
  }

  .contact-hero h1 {
    font-size: 36px;
  }

  .contact-hero .subtitle {
    font-size: 18px;
  }

  .contact-info h2 {
    font-size: clamp(28px, 4vw, 36px);
  }

  .contact-description {
    font-size: clamp(16px, 2vw, 18px);
  }

  .contact-image {
    max-height: 300px;
    margin-top: 20px;
  }

  .contact-item {
    flex-direction: column;
    gap: 15px;
  }

  .contact-icon {
    width: 45px;
    height: 45px;
  }
}

@media (max-width: 768px) {
  .container {
    padding: 0 15px;
  }

  .contact-section {
    padding: 60px 0;
  }

  .contact-form-container {
    padding: 30px 25px;
  }

  .contact-form h2 {
    font-size: clamp(24px, 4vw, 28px);
  }

  .social-links {
    flex-wrap: wrap;
    gap: 12px;
  }
}

@media (max-width: 480px) {
  .contact {
    padding-top: 70px;
  }

  .container {
    padding: 0 15px;
  }

  .contact-hero {
    padding: 60px 0;
  }

  .contact-hero h1 {
    font-size: 36px;
  }

  .contact-hero .subtitle {
    font-size: 18px;
  }

  .contact-section {
    padding: 40px 0;
  }

  .contact-content {
    gap: 30px;
  }

  .contact-info h2 {
    font-size: clamp(24px, 5vw, 28px);
    margin-bottom: 15px;
  }

  .contact-description {
    font-size: clamp(15px, 3vw, 17px);
    margin-bottom: 30px;
  }

  .contact-form-container {
    padding: 25px 15px;
  }

  .contact-form h2 {
    font-size: 22px;
    margin-bottom: 25px;
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-group label {
    font-size: 13px;
  }

  .form-group input,
  .form-group select,
  .form-group textarea {
    padding: 12px;
    font-size: 15px;
  }

  .submit-btn {
    padding: 14px;
    font-size: 16px;
  }

  .contact-image {
    max-height: 250px;
    margin-top: 20px;
  }

  .contact-item {
    margin-bottom: 25px;
  }

  .contact-icon {
    width: 40px;
    height: 40px;
  }

  .contact-icon svg {
    width: 20px;
    height: 20px;
  }

  .contact-item h3 {
    font-size: 18px;
  }

  .contact-item p {
    font-size: 14px;
  }

  .social-section h3 {
    font-size: 18px;
  }

  .social-links {
    gap: 10px;
  }
}
</style>


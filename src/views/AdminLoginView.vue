<template>
  <div class="admin-login">
    <div class="hero-background">
      <img :src="heroImage" alt="Background" class="hero-bg-image" loading="eager" decoding="async" fetchpriority="high" />
      <div class="hero-overlay"></div>
    </div>
    <div class="login-container">
      <div class="login-box">
        <div class="login-header">
          <h1>Panel de Administración</h1>
          <p>LegisAudit - Consultoría</p>
        </div>
        
        <form @submit.prevent="login" class="login-form">
          <div class="form-group">
            <label>Usuario</label>
            <input 
              v-model="credentials.usuario" 
              type="text" 
              required 
              autocomplete="username"
              placeholder="Ingresa tu usuario"
            />
          </div>
          
          <div class="form-group">
            <label>Contraseña</label>
            <input 
              v-model="credentials.password" 
              type="password" 
              required 
              autocomplete="current-password"
              placeholder="Ingresa tu contraseña"
            />
          </div>
          
          <div v-if="error" class="error-message">
            {{ error }}
          </div>
          
          <button type="submit" class="login-button" :disabled="loading">
            {{ loading ? 'Iniciando sesión...' : 'Iniciar Sesión' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { apiUrl } from '@/config/api'
import heroImage from '@/images/pexels-karola-g-7876051.jpg'

const router = useRouter()

const credentials = ref({
  usuario: '',
  password: ''
})

const loading = ref(false)
const error = ref('')

const login = async () => {
  try {
    loading.value = true
    error.value = ''
    
    // En algunos entornos (proxy/Apache en Windows) el body JSON puede llegar vacío.
    // Usamos x-www-form-urlencoded para garantizar que PHP lo reciba en $_POST.
    const formBody = new URLSearchParams()
    formBody.set('usuario', credentials.value.usuario)
    formBody.set('password', credentials.value.password)

    const response = await fetch(apiUrl('/admin/auth.php'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: formBody.toString()
    })
    
    const data = await response.json()
    
    if (data.success) {
      router.push('/admin/dashboard')
    } else {
      error.value = data.message || 'Error al iniciar sesión'
    }
  } catch (err) {
    console.error('Error:', err)
    error.value = 'Error de conexión. Por favor intenta de nuevo.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.admin-login {
  min-height: 100vh;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  position: relative;
  padding: 20px;
  padding-top: 80px;
  overflow: hidden;
}

.admin-login .hero-background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 0;
}

.admin-login .hero-bg-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.admin-login .hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(26, 54, 93, 0.85) 0%, rgba(44, 82, 130, 0.75) 100%);
}

.login-container {
  width: 100%;
  max-width: 380px;
  margin-top: auto;
  margin-bottom: auto;
  position: relative;
  z-index: 1;
}

.login-box {
  background: white;
  border-radius: 16px;
  padding: 30px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.login-header {
  text-align: center;
  margin-bottom: 30px;
}

.login-header h1 {
  font-size: 28px;
  color: #1a365d;
  margin-bottom: 8px;
}

.login-header p {
  font-size: 14px;
  color: #718096;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 6px;
  font-size: 13px;
}

.form-group input {
  padding: 12px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 15px;
  font-family: inherit;
  transition: border-color 0.3s ease;
}

.form-group input:focus {
  outline: none;
  border-color: #1a365d;
}

.error-message {
  background: #fed7d7;
  color: #c53030;
  padding: 12px;
  border-radius: 8px;
  font-size: 14px;
  text-align: center;
}

.login-button {
  background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
  color: white;
  border: none;
  padding: 12px;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 5px;
}

.login-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(26, 54, 93, 0.3);
}

.login-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 480px) {
  .login-box {
    padding: 25px 18px;
  }

  .login-header h1 {
    font-size: 24px;
  }

  .login-header p {
    font-size: 13px;
  }
}
</style>


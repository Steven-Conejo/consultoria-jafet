<template>
  <div class="blog-view">
    <div class="blog-hero">
      <div class="hero-background">
        <img :src="heroImage" alt="Background" class="hero-bg-image" loading="eager" decoding="async" fetchpriority="high" />
        <div class="hero-overlay"></div>
      </div>
      <div class="container">
        <h1 class="blog-title">Blog Legal</h1>
        <p class="blog-subtitle">Artículos informativos sobre temas legales y jurídicos</p>
      </div>
    </div>

    <div class="container blog-container">
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Cargando artículos...</p>
      </div>

      <div v-else-if="articulos.length === 0" class="no-articles">
        <div class="no-articles-content">
          <svg class="no-articles-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M16 13H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M16 17H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10 9H9H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <h2>¡Próximamente!</h2>
          <p>Estamos preparando contenido valioso para ti. Los artículos se irán publicando progresivamente con el tiempo.</p>
          <p class="no-articles-subtitle">Mantente atento a nuestras actualizaciones sobre temas legales y jurídicos.</p>
        </div>
      </div>

      <div v-else class="articles-grid">
        <article 
          v-for="articulo in articulos" 
          :key="articulo.id" 
          class="article-card"
          @click="verArticulo(articulo.id)"
        >
          <div v-if="articulo.imagen_principal" class="article-image">
            <img :src="articulo.imagen_principal" :alt="articulo.titulo" loading="lazy" decoding="async" />
          </div>
          <div class="article-content">
            <div class="article-meta">
              <span class="article-date">{{ formatDate(articulo.fecha_creacion) }}</span>
              <span class="article-views">{{ articulo.vistas }} vistas</span>
            </div>
            <h2 class="article-title">{{ articulo.titulo }}</h2>
            <p class="article-summary">{{ articulo.resumen || getSummary(articulo.texto) }}</p>
            <button class="article-button">Ver más</button>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { apiUrl } from '@/config/api'
import heroImage from '@/images/pexels-sora-shimazaki-5668473.jpg'

const router = useRouter()
const articulos = ref<any[]>([])
const loading = ref(true)

const fetchArticulos = async () => {
  try {
    loading.value = true
    const response = await fetch(apiUrl('/blog/articles.php'))
    const data = await response.json()
    
    if (data.success) {
      articulos.value = data.articulos
    }
  } catch (error) {
    console.error('Error al cargar artículos:', error)
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getSummary = (text: string) => {
  if (!text) return ''
  const plainText = text.replace(/<[^>]*>/g, '')
  return plainText.length > 200 ? plainText.substring(0, 200) + '...' : plainText
}

const verArticulo = (id: number) => {
  router.push(`/blog/${id}`)
}

onMounted(() => {
  fetchArticulos()
})
</script>

<style scoped>
.blog-view {
  min-height: 100vh;
  padding-top: 80px;
  background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
}

.blog-hero {
  position: relative;
  color: white;
  padding: 100px 0;
  text-align: center;
  overflow: hidden;
}

.blog-hero .hero-background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 0;
}

.blog-hero .hero-bg-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: blur(2px);
  transform: scale(1.1);
}

.blog-hero .hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(26, 54, 93, 0.85) 0%, rgba(44, 82, 130, 0.75) 100%);
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  position: relative;
  z-index: 1;
}

.blog-title {
  font-size: 48px;
  font-weight: 700;
  margin-bottom: 10px;
}

.blog-subtitle {
  font-size: 20px;
  opacity: 0.9;
}

.blog-container {
  padding: 60px 20px;
}

.loading {
  text-align: center;
  padding: 80px 20px;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #e2e8f0;
  border-top-color: #1a365d;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.no-articles {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 500px;
  padding: 40px 20px;
}

.no-articles-content {
  text-align: center;
  max-width: 600px;
  background: white;
  padding: 60px 40px;
  border-radius: 16px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.no-articles-icon {
  width: 120px;
  height: 120px;
  color: #1a365d;
  margin: 0 auto 30px;
  opacity: 0.7;
}

.no-articles-content h2 {
  font-size: 32px;
  color: #1a365d;
  margin-bottom: 20px;
}

.no-articles-content p {
  font-size: 18px;
  color: #4a5568;
  line-height: 1.8;
  margin-bottom: 15px;
}

.no-articles-subtitle {
  font-size: 16px;
  color: #718096;
  font-style: italic;
}

.articles-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 30px;
  margin-top: 40px;
}

.article-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  cursor: pointer;
  display: flex;
  flex-direction: column;
}

.article-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.article-image {
  width: 100%;
  height: 220px;
  overflow: hidden;
  background: #e2e8f0;
}

.article-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.article-card:hover .article-image img {
  transform: scale(1.05);
}

.article-content {
  padding: 25px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.article-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  font-size: 14px;
  color: #718096;
}

.article-date {
  font-weight: 500;
}

.article-views {
  display: flex;
  align-items: center;
  gap: 5px;
}

.article-title {
  font-size: 24px;
  font-weight: 700;
  color: #1a365d;
  margin-bottom: 15px;
  line-height: 1.3;
}

.article-summary {
  font-size: 16px;
  color: #4a5568;
  line-height: 1.6;
  margin-bottom: 20px;
  flex: 1;
}

.article-button {
  background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  align-self: flex-start;
}

.article-button:hover {
  transform: translateX(5px);
  box-shadow: 0 4px 15px rgba(26, 54, 93, 0.3);
}

@media (max-width: 768px) {
  .blog-hero {
    padding: 60px 0;
  }

  .blog-title {
    font-size: 36px;
  }

  .blog-subtitle {
    font-size: 18px;
  }

  .articles-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }

  .no-articles-content {
    padding: 40px 20px;
  }
}
</style>


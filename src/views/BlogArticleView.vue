<template>
  <div class="article-view">
    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Cargando artículo...</p>
    </div>

    <div v-else-if="articulo" class="article-container">
      <div class="article-header">
        <button @click="$router.push('/blog')" class="back-button">
          ← Volver al Blog
        </button>
        <div class="article-meta">
          <span class="article-date">{{ formatDate(articulo.fecha_creacion) }}</span>
          <span class="article-views">{{ articulo.vistas }} vistas</span>
        </div>
      </div>

      <article class="article-content">
        <h1 class="article-title">{{ articulo.titulo }}</h1>
        
        <div v-if="articulo.imagen_principal" class="article-image-main">
          <img :src="articulo.imagen_principal" :alt="articulo.titulo" loading="eager" decoding="async" />
        </div>

        <div class="article-text" v-html="articulo.texto"></div>
      </article>

      <div class="comments-section">
        <h2 class="comments-title">Comentarios</h2>
        
        <div class="comment-form">
          <h3>Deja un comentario</h3>
          <form @submit.prevent="enviarComentario">
            <div class="form-row">
              <div class="form-group">
                <label>Nombre *</label>
                <input v-model="nuevoComentario.nombre" type="text" required />
              </div>
              <div class="form-group">
                <label>Email *</label>
                <input v-model="nuevoComentario.email" type="email" required />
              </div>
            </div>
            <div class="form-group">
              <label>Comentario *</label>
              <textarea v-model="nuevoComentario.comentario" rows="4" required></textarea>
            </div>
            <button type="submit" class="submit-button" :disabled="enviando">
              {{ enviando ? 'Enviando...' : 'Publicar Comentario' }}
            </button>
          </form>
        </div>

        <div v-if="loadingComments" class="loading-comments">
          <p>Cargando comentarios...</p>
        </div>

        <div v-else-if="comentarios.length === 0" class="no-comments">
          <p>No hay comentarios aún. ¡Sé el primero en comentar!</p>
        </div>

        <div v-else class="comments-list">
          <div 
            v-for="comentario in comentarios" 
            :key="comentario.id" 
            class="comment-item"
          >
            <div class="comment-header">
              <div class="comment-author">
                <strong>{{ comentario.nombre }}</strong>
                <span class="comment-date">{{ formatDate(comentario.fecha_creacion) }}</span>
              </div>
            </div>
            <div class="comment-text">{{ comentario.comentario }}</div>
            
            <button 
              @click="toggleReply(comentario.id)" 
              class="reply-button"
            >
              Responder
            </button>

            <div v-if="replyingTo === comentario.id" class="reply-form">
              <form @submit.prevent="enviarRespuesta(comentario.id)">
                <div class="form-row">
                  <div class="form-group">
                    <label>Nombre *</label>
                    <input v-model="nuevaRespuesta.nombre" type="text" required />
                  </div>
                  <div class="form-group">
                    <label>Email *</label>
                    <input v-model="nuevaRespuesta.email" type="email" required />
                  </div>
                </div>
                <div class="form-group">
                  <label>Respuesta *</label>
                  <textarea v-model="nuevaRespuesta.comentario" rows="2" required></textarea>
                </div>
                <div class="reply-actions">
                  <button type="submit" class="submit-button" :disabled="enviando">
                    {{ enviando ? 'Enviando...' : 'Publicar Respuesta' }}
                  </button>
                  <button type="button" @click="cancelarRespuesta" class="cancel-button">
                    Cancelar
                  </button>
                </div>
              </form>
            </div>

            <div v-if="comentario.respuestas && comentario.respuestas.length > 0" class="replies">
              <div 
                v-for="respuesta in comentario.respuestas" 
                :key="respuesta.id" 
                class="reply-item"
              >
                <div class="comment-header">
                  <div class="comment-author">
                    <strong>{{ respuesta.nombre }}</strong>
                    <span class="comment-date">{{ formatDate(respuesta.fecha_creacion) }}</span>
                  </div>
                </div>
                <div class="comment-text">{{ respuesta.comentario }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="error">
      <p>Artículo no encontrado</p>
      <button @click="$router.push('/blog')" class="back-button">Volver al Blog</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { apiUrl } from '@/config/api'
import { useToastStore } from '@/stores/toast'

const route = useRoute()
const toast = useToastStore()

const articulo = ref<any>(null)
const comentarios = ref<any[]>([])
const loading = ref(true)
const loadingComments = ref(false)
const enviando = ref(false)
const replyingTo = ref<number | null>(null)

const nuevoComentario = ref({
  nombre: '',
  email: '',
  comentario: ''
})

const nuevaRespuesta = ref({
  nombre: '',
  email: '',
  comentario: ''
})

const fetchArticulo = async () => {
  try {
    loading.value = true
    const id = route.params.id
    const response = await fetch(apiUrl(`/blog/articles.php?id=${id}&public=1`))
    const data = await response.json()
    
    if (data.success) {
      articulo.value = data.articulo
    } else {
      articulo.value = null
    }
  } catch (error) {
    console.error('Error al cargar artículo:', error)
    articulo.value = null
  } finally {
    loading.value = false
  }
}

const fetchComentarios = async () => {
  try {
    loadingComments.value = true
    const id = route.params.id
    const response = await fetch(apiUrl(`/blog/comments.php?articulo_id=${id}`))
    const data = await response.json()
    
    if (data.success) {
      comentarios.value = data.comentarios
    }
  } catch (error) {
    console.error('Error al cargar comentarios:', error)
  } finally {
    loadingComments.value = false
  }
}

const enviarComentario = async () => {
  try {
    enviando.value = true
    const id = route.params.id
    
    const response = await fetch(apiUrl('/blog/comments.php'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        articulo_id: id,
        nombre: nuevoComentario.value.nombre,
        email: nuevoComentario.value.email,
        comentario: nuevoComentario.value.comentario
      })
    })
    
    const data = await response.json()
    
    if (data.success) {
      nuevoComentario.value = { nombre: '', email: '', comentario: '' }
      await fetchComentarios()
      toast.success('Comentario publicado correctamente')
    } else {
      toast.error(data.message || 'No se pudo publicar el comentario')
    }
  } catch (error) {
    console.error('Error al enviar comentario:', error)
    toast.error('Error al enviar el comentario')
  } finally {
    enviando.value = false
  }
}

const toggleReply = (id: number) => {
  replyingTo.value = replyingTo.value === id ? null : id
  if (replyingTo.value === id) {
    nuevaRespuesta.value = { nombre: '', email: '', comentario: '' }
  }
}

const cancelarRespuesta = () => {
  replyingTo.value = null
  nuevaRespuesta.value = { nombre: '', email: '', comentario: '' }
}

const enviarRespuesta = async (comentarioPadreId: number) => {
  try {
    enviando.value = true
    const id = route.params.id
    
    const response = await fetch(apiUrl('/blog/comments.php'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        articulo_id: id,
        nombre: nuevaRespuesta.value.nombre,
        email: nuevaRespuesta.value.email,
        comentario: nuevaRespuesta.value.comentario,
        comentario_padre_id: comentarioPadreId
      })
    })
    
    const data = await response.json()
    
    if (data.success) {
      nuevaRespuesta.value = { nombre: '', email: '', comentario: '' }
      replyingTo.value = null
      await fetchComentarios()
      toast.success('Respuesta publicada correctamente')
    } else {
      toast.error(data.message || 'No se pudo publicar la respuesta')
    }
  } catch (error) {
    console.error('Error al enviar respuesta:', error)
    toast.error('Error al enviar la respuesta')
  } finally {
    enviando.value = false
  }
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  fetchArticulo()
  fetchComentarios()
})
</script>

<style scoped>
.article-view {
  min-height: 100vh;
  padding-top: 80px;
  background: #f7fafc;
}

.loading {
  text-align: center;
  padding: 100px 20px;
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

.article-container {
  max-width: 900px;
  margin: 0 auto;
  padding: 40px 20px;
}

.article-header {
  margin-bottom: 30px;
}

.back-button {
  background: white;
  border: 2px solid #1a365d;
  color: #1a365d;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-bottom: 20px;
}

.back-button:hover {
  background: #1a365d;
  color: white;
}

.article-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 14px;
  color: #718096;
}

.article-content {
  background: white;
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  margin-bottom: 40px;
}

.article-title {
  font-size: 42px;
  font-weight: 700;
  color: #1a365d;
  margin-bottom: 30px;
  line-height: 1.2;
}

.article-image-main {
  width: 100%;
  margin-bottom: 30px;
  border-radius: 8px;
  overflow: hidden;
  display: flex;
  justify-content: center;
}

.article-image-main img {
  max-width: 75%;
  height: auto;
  display: block;
}

.article-text {
  font-size: 18px;
  line-height: 1.8;
  color: #2d3748;
}

.article-text :deep(p) {
  margin-bottom: 20px;
}

.article-text :deep(h2) {
  font-size: 32px;
  color: #1a365d;
  margin-top: 40px;
  margin-bottom: 20px;
}

.article-text :deep(h3) {
  font-size: 24px;
  color: #2c5282;
  margin-top: 30px;
  margin-bottom: 15px;
}

.article-text :deep(ul), .article-text :deep(ol) {
  margin-bottom: 20px;
  padding-left: 30px;
}

.article-text :deep(li) {
  margin-bottom: 10px;
}

.comments-section {
  background: rgba(255, 255, 255, 0.96);
  padding: 24px;
  border-radius: 14px;
  box-shadow: 0 10px 28px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(226, 232, 240, 0.85);
}

.comments-title {
  font-size: 22px;
  color: #1a365d;
  margin-bottom: 16px;
  letter-spacing: -0.2px;
}

.comment-form {
  margin-bottom: 20px;
  padding: 16px;
  border-radius: 12px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
}

.comment-form h3 {
  font-size: 16px;
  color: #0f172a;
  margin-bottom: 12px;
  font-weight: 800;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 12px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-weight: 700;
  color: #334155;
  margin-bottom: 6px;
  font-size: 12px;
  letter-spacing: 0.2px;
}

.form-group input,
.form-group textarea {
  padding: 10px 12px;
  border: 1.5px solid #dbe4f0;
  border-radius: 10px;
  font-size: 14px;
  font-family: inherit;
  transition: border-color 0.3s ease;
  background: rgba(255, 255, 255, 0.9);
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #2c5282;
  box-shadow: 0 0 0 3px rgba(44, 82, 130, 0.12);
}

.submit-button {
  background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 800;
  cursor: pointer;
  transition: all 0.3s ease;
}

.comment-form .submit-button {
  margin-top: 8px;
}

.submit-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(26, 54, 93, 0.3);
}

.submit-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.loading-comments,
.no-comments {
  text-align: center;
  padding: 18px 12px;
  color: #718096;
  font-size: 14px;
}

.comments-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.comment-item {
  padding: 14px 14px;
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.comment-header {
  margin-bottom: 8px;
}

.comment-author {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.comment-author strong {
  color: #1a365d;
  font-size: 14px;
}

.comment-date {
  font-size: 12px;
  color: #718096;
}

.comment-text {
  font-size: 14px;
  line-height: 1.55;
  color: #2d3748;
  margin-bottom: 10px;
  white-space: pre-wrap;
}

.reply-button {
  background: transparent;
  border: 1.5px solid #2c5282;
  color: #2c5282;
  padding: 7px 12px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 800;
  cursor: pointer;
  transition: all 0.3s ease;
}

.reply-button:hover {
  background: #2c5282;
  color: white;
}

.reply-form {
  margin-top: 12px;
  padding: 14px;
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.reply-actions {
  display: flex;
  gap: 10px;
}

.cancel-button {
  background: transparent;
  border: 1.5px solid #cbd5e0;
  color: #4a5568;
  padding: 10px 18px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 800;
  cursor: pointer;
  transition: all 0.3s ease;
}

.cancel-button:hover {
  background: #e2e8f0;
}

.replies {
  margin-top: 12px;
  padding-left: 16px;
  border-left: 2px solid #e2e8f0;
}

.reply-item {
  padding: 12px;
  background: #ffffff;
  border-radius: 12px;
  margin-bottom: 10px;
  border: 1px solid #e2e8f0;
}

.error {
  text-align: center;
  padding: 100px 20px;
}

@media (max-width: 768px) {
  .article-content {
    padding: 25px;
  }

  .article-title {
    font-size: 32px;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .comments-section {
    padding: 18px;
  }

  .replies {
    padding-left: 12px;
  }
}
</style>


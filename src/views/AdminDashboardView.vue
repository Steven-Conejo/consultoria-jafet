<template>
  <div class="admin-dashboard">
    <!-- Mostrar loading mientras se verifica autenticación -->
    <div v-if="checkingAuth" class="auth-checking">
      <div class="loading-spinner"></div>
      <p>Verificando autenticación...</p>
    </div>
    
    <template v-else>
      <div class="admin-header">
        <div class="container">
          <div class="header-content">
            <h1>Panel de Administración</h1>
            <div class="header-actions">
              <span class="user-info">Bienvenido, {{ user?.nombre_completo }}</span>
              <button @click="logout" class="logout-button">Cerrar Sesión</button>
            </div>
          </div>
        </div>
      </div>

      <div class="container admin-container">
      <div class="admin-tabs">
        <button 
          @click="activeTab = 'articles'" 
          :class="['tab-button', { active: activeTab === 'articles' }]"
        >
          Artículos
        </button>
        <button 
          @click="activeTab = 'users'" 
          :class="['tab-button', { active: activeTab === 'users' }]"
        >
          Usuarios
        </button>
      </div>

      <!-- Tab de Artículos -->
      <div v-if="activeTab === 'articles'" class="tab-content">
        <div class="section-header">
          <h2>Gestión de Artículos</h2>
          <button @click="showArticleForm = true" class="add-button">
            + Nuevo Artículo
          </button>
        </div>

        <div v-if="showArticleForm" class="article-form-modal">
          <div class="modal-content">
            <div class="modal-header">
              <h3>{{ editingArticle ? 'Editar Artículo' : 'Nuevo Artículo' }}</h3>
              <button @click="closeArticleForm" class="close-button">×</button>
            </div>
            
            <form @submit.prevent="saveArticle" class="article-form">
              <div class="form-group">
                <label>Título del Artículo *</label>
                <input v-model="articleForm.titulo" type="text" required />
              </div>

              <div class="form-group">
                <label>Imagen Principal</label>
                <div class="image-upload">
                  <input 
                    ref="imageInput"
                    type="file" 
                    accept="image/*" 
                    @change="handleImageUpload"
                    style="display: none"
                  />
                  <button type="button" @click="imageInput?.click()" class="upload-button">
                    {{ articleForm.imagen_principal ? 'Cambiar Imagen' : 'Subir Imagen' }}
                  </button>
                  <div v-if="articleForm.imagen_principal" class="image-preview">
                    <img :src="articleForm.imagen_principal" alt="Preview" loading="lazy" decoding="async" />
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>Texto del Artículo *</label>
                <RichTextEditor 
                  v-model="articleForm.texto"
                />
                <small>Usa las herramientas de la barra superior para formatear el texto</small>
              </div>

              <div class="form-actions">
                <button type="submit" class="save-button" :disabled="saving">
                  {{ saving ? 'Guardando...' : 'Guardar Artículo' }}
                </button>
                <button type="button" @click="closeArticleForm" class="cancel-button">
                  Cancelar
                </button>
              </div>
            </form>
          </div>
        </div>

        <div v-if="loadingArticles" class="loading">
          <p>Cargando artículos...</p>
        </div>

        <div v-else class="articles-list">
          <div 
            v-for="articulo in articulos" 
            :key="articulo.id" 
            class="article-item"
          >
            <div class="article-info">
              <h3>{{ articulo.titulo }}</h3>
              <p class="article-meta">
                Creado: {{ formatDate(articulo.fecha_creacion) }} | 
                Vistas: {{ articulo.vistas }}
              </p>
            </div>
            <div class="article-actions">
              <button @click="openComments(articulo)" class="comments-button">Comentarios</button>
              <button @click="editArticle(articulo)" class="edit-button">Editar</button>
              <button @click="deleteArticle(articulo.id)" class="delete-button">Eliminar</button>
            </div>
          </div>
        </div>

        <!-- Modal de Comentarios -->
        <div v-if="showCommentsModal" class="article-form-modal">
          <div class="modal-content">
            <div class="modal-header">
              <h3>Comentarios — {{ commentsArticle?.titulo }}</h3>
              <button @click="closeComments" class="close-button">×</button>
            </div>

            <div class="comments-modal-body">
              <div v-if="loadingComments" class="loading">
                <p>Cargando comentarios...</p>
              </div>

              <div v-else-if="comments.length === 0" class="empty-comments">
                <p>No hay comentarios para este artículo todavía.</p>
              </div>

              <div v-else class="comments-admin-list">
                <div v-for="c in comments" :key="c.id" class="comment-admin-item">
                  <div class="comment-admin-header">
                    <div class="comment-admin-author">
                      <strong>{{ c.nombre }}</strong>
                      <span class="comment-admin-meta">{{ c.email }} · {{ formatDateTime(c.fecha_creacion) }}</span>
                    </div>
                    <button class="delete-button" @click="deleteComment(c.id, true)">Eliminar</button>
                  </div>
                  <div class="comment-admin-text">{{ c.comentario }}</div>

                  <div v-if="c.respuestas && c.respuestas.length" class="comment-admin-replies">
                    <div v-for="r in c.respuestas" :key="r.id" class="comment-admin-reply">
                      <div class="comment-admin-header">
                        <div class="comment-admin-author">
                          <strong>{{ r.nombre }}</strong>
                          <span class="comment-admin-meta">{{ r.email }} · {{ formatDateTime(r.fecha_creacion) }}</span>
                        </div>
                        <button class="delete-button" @click="deleteComment(r.id, false)">Eliminar</button>
                      </div>
                      <div class="comment-admin-text">{{ r.comentario }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab de Usuarios -->
      <div v-if="activeTab === 'users'" class="tab-content">
        <div class="section-header">
          <h2>Gestión de Usuarios</h2>
          <button @click="showUserForm = true" class="add-button">
            + Nuevo Usuario
          </button>
        </div>

        <div v-if="showUserForm" class="user-form-modal">
          <div class="modal-content">
            <div class="modal-header">
              <h3>Nuevo Usuario</h3>
              <button @click="closeUserForm" class="close-button">×</button>
            </div>
            
            <form @submit.prevent="saveUser" class="user-form">
              <div class="form-group">
                <label>Nombre Completo *</label>
                <input v-model="userForm.nombre_completo" type="text" required />
              </div>

              <div class="form-group">
                <label>Usuario *</label>
                <input v-model="userForm.usuario" type="text" required />
              </div>

              <div class="form-group">
                <label>Contraseña *</label>
                <input v-model="userForm.password" type="password" required minlength="4" />
                <small>Mínimo 4 caracteres</small>
              </div>

              <div class="form-actions">
                <button type="submit" class="save-button" :disabled="saving">
                  {{ saving ? 'Guardando...' : 'Crear Usuario' }}
                </button>
                <button type="button" @click="closeUserForm" class="cancel-button">
                  Cancelar
                </button>
              </div>
            </form>
          </div>
        </div>

        <div v-if="loadingUsers" class="loading">
          <p>Cargando usuarios...</p>
        </div>

        <div v-else class="users-list">
          <div 
            v-for="usuario in usuarios" 
            :key="usuario.id" 
            class="user-item"
          >
            <div class="user-info">
              <h3>{{ usuario.nombre_completo }}</h3>
              <p class="user-meta">
                Usuario: {{ usuario.usuario }} | 
                Creado: {{ formatDate(usuario.fecha_creacion) }}
              </p>
            </div>
            <div class="user-actions">
              <button 
                @click="deleteUser(usuario.id)" 
                class="delete-button"
                :disabled="usuario.id === user?.id"
              >
                Eliminar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useToastStore } from '@/stores/toast'
import { useConfirm } from '@/composables/useConfirm'
import RichTextEditor from '@/components/RichTextEditor.vue'

const router = useRouter()
const toast = useToastStore()
const { confirm } = useConfirm()

const user = ref<any>(null)
const checkingAuth = ref(true)
const activeTab = ref('articles')
const showArticleForm = ref(false)
const showUserForm = ref(false)
const editingArticle = ref<any>(null)
const saving = ref(false)

const articulos = ref<any[]>([])
const usuarios = ref<any[]>([])
const loadingArticles = ref(false)
const loadingUsers = ref(false)
const showCommentsModal = ref(false)
const loadingComments = ref(false)
const comments = ref<any[]>([])
const commentsArticle = ref<any | null>(null)

const articleForm = ref({
  titulo: '',
  imagen_principal: '',
  texto: ''
})

const imageInput = ref<HTMLInputElement | null>(null)

const userForm = ref({
  nombre_completo: '',
  usuario: '',
  password: ''
})

import { apiUrl } from '@/config/api'

const checkAuth = async () => {
  try {
    checkingAuth.value = true
    
    console.log('[Dashboard] Verificando autenticación...')
    
    // Verificar autenticación con headers optimizados
    const authUrl = apiUrl('/admin/auth.php') + '?check=1&t=' + Date.now()
    const response = await fetch(authUrl, {
      method: 'GET',
      credentials: 'include', // Incluir cookies de sesión
      cache: 'no-store', // No usar caché
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Cache-Control': 'no-cache, no-store, must-revalidate',
        'Pragma': 'no-cache'
      }
    })
    
    console.log('[Dashboard] Respuesta recibida, status:', response?.status)
    
    // Si la respuesta no es exitosa, bloquear acceso
    if (!response || !response.ok || response.status !== 200) {
      console.warn('[Dashboard] Respuesta de auth no exitosa:', response?.status, '- Redirigiendo a login')
      window.location.href = '/admin'
      return
    }
    
    let data
    try {
      const text = await response.text()
      if (!text || text.trim() === '') {
        console.warn('[Dashboard] Respuesta vacía - Redirigiendo a login')
        window.location.href = '/admin'
        return
      }
      data = JSON.parse(text)
      console.log('[Dashboard] Datos de autenticación:', { success: data?.success, authenticated: data?.authenticated })
    } catch (jsonError) {
      console.error('[Dashboard] Error parseando respuesta:', jsonError, '- Redirigiendo a login')
      window.location.href = '/admin'
      return
    }
    
    // Verificar explícitamente que está autenticado
    const isAuthenticated = data && data.success === true && data.authenticated === true
    
    if (isAuthenticated) {
      console.log('[Dashboard] Usuario autenticado, permitiendo acceso')
      user.value = data.user
      checkingAuth.value = false
    } else {
      // No autenticado, redirigir al login INMEDIATAMENTE
      console.warn('[Dashboard] Usuario NO autenticado - Redirigiendo a login')
      window.location.href = '/admin'
    }
  } catch (error) {
    // Cualquier error = redirigir al login por seguridad
    console.error('[Dashboard] Error verificando autenticación:', error, '- Redirigiendo a login')
    window.location.href = '/admin'
  } finally {
    // Si llegamos aquí sin autenticación, checkingAuth se mantiene true
    // para evitar mostrar contenido
  }
}

const logout = async () => {
  try {
    await fetch(apiUrl('/admin/auth.php') + '?logout=1', { method: 'POST' })
    router.push('/admin')
  } catch (error) {
    console.error('Error:', error)
    router.push('/admin')
  }
}

const fetchArticles = async () => {
  try {
    loadingArticles.value = true
    const response = await fetch(apiUrl('/admin/articles.php'))
    const data = await response.json()
    
    if (data.success) {
      articulos.value = data.articulos
    }
  } catch (error) {
    console.error('Error al cargar artículos:', error)
  } finally {
    loadingArticles.value = false
  }
}

const fetchUsers = async () => {
  try {
    loadingUsers.value = true
    const response = await fetch(apiUrl('/admin/users.php'))
    const data = await response.json()
    
    if (data.success) {
      usuarios.value = data.usuarios
    }
  } catch (error) {
    console.error('Error al cargar usuarios:', error)
  } finally {
    loadingUsers.value = false
  }
}

const openComments = async (articulo: any) => {
  commentsArticle.value = articulo
  showCommentsModal.value = true
  await fetchComments(articulo.id)
}

const closeComments = () => {
  showCommentsModal.value = false
  loadingComments.value = false
  comments.value = []
  commentsArticle.value = null
}

const fetchComments = async (articuloId: number) => {
  try {
    loadingComments.value = true
    const response = await fetch(apiUrl(`/admin/comments.php?articulo_id=${articuloId}`))
    const data = await response.json()
    if (data.success) {
      comments.value = data.comentarios || []
    } else {
      toast.error(data.message || 'No se pudieron cargar los comentarios')
      comments.value = []
    }
  } catch (error) {
    console.error('Error al cargar comentarios:', error)
    toast.error('Error al cargar comentarios')
    comments.value = []
  } finally {
    loadingComments.value = false
  }
}

const deleteComment = async (id: number, isParent: boolean) => {
  const ok = await confirm(
    isParent
      ? '¿Eliminar este comentario? Si tiene respuestas, también se eliminarán automáticamente.'
      : '¿Eliminar esta respuesta?',
    {
      title: 'Eliminar comentario',
      confirmText: 'Eliminar',
      tone: 'danger',
    }
  )
  if (!ok) return

  try {
    const response = await fetch(apiUrl(`/admin/comments.php?id=${id}`), { method: 'DELETE' })
    const data = await response.json()

    if (data.success) {
      toast.success('Comentario eliminado correctamente')
      if (commentsArticle.value) {
        await fetchComments(commentsArticle.value.id)
      }
    } else {
      toast.error(data.message || 'No se pudo eliminar el comentario')
    }
  } catch (error) {
    console.error('Error al eliminar comentario:', error)
    toast.error('Error al eliminar el comentario')
  }
}

const handleImageUpload = async (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (!file) return

  try {
    const formData = new FormData()
    formData.append('image', file)

    const response = await fetch(apiUrl('/admin/upload_image.php'), {
      method: 'POST',
      body: formData
    })

    const data = await response.json()
    
    if (data.success) {
      articleForm.value.imagen_principal = data.url
    } else {
      toast.error(data.message || 'No se pudo subir la imagen')
    }
  } catch (error) {
    console.error('Error al subir imagen:', error)
    toast.error('Error al subir la imagen')
  }
}

const saveArticle = async () => {
  try {
    saving.value = true
    
    // Preparar datos a enviar, incluyendo el ID si estamos editando
    const dataToSend: any = { ...articleForm.value }
    if (editingArticle.value) {
      dataToSend.id = editingArticle.value.id
    }
    
    const response = await fetch(apiUrl('/admin/articles.php'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(dataToSend)
    })
    
    const data = await response.json()
    
    if (data.success) {
      closeArticleForm()
      await fetchArticles()
      toast.success('Artículo guardado correctamente')
    } else {
      toast.error(data.message || 'No se pudo guardar el artículo')
    }
  } catch (error) {
    console.error('Error al guardar artículo:', error)
    toast.error('Error al guardar el artículo')
  } finally {
    saving.value = false
  }
}

const editArticle = (articulo: any) => {
  editingArticle.value = articulo
  articleForm.value = {
    titulo: articulo.titulo,
    imagen_principal: articulo.imagen_principal || '',
    texto: articulo.texto
  }
  showArticleForm.value = true
}

const deleteArticle = async (id: number) => {
  const ok = await confirm('¿Estás seguro de eliminar este artículo?', {
    title: 'Eliminar artículo',
    confirmText: 'Eliminar',
    tone: 'danger',
  })
  if (!ok) return

  try {
    const response = await fetch(apiUrl(`/admin/articles.php?id=${id}`), {
      method: 'DELETE'
    })
    
    const data = await response.json()
    
    if (data.success) {
      await fetchArticles()
      toast.success('Artículo eliminado correctamente')
    } else {
      toast.error(data.message || 'No se pudo eliminar el artículo')
    }
  } catch (error) {
    console.error('Error al eliminar artículo:', error)
    toast.error('Error al eliminar el artículo')
  }
}

const closeArticleForm = () => {
  showArticleForm.value = false
  editingArticle.value = null
  articleForm.value = {
    titulo: '',
    imagen_principal: '',
    texto: ''
  }
}

const saveUser = async () => {
  try {
    saving.value = true
    
    const response = await fetch(apiUrl('/admin/users.php'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(userForm.value)
    })
    
    const data = await response.json()
    
    if (data.success) {
      closeUserForm()
      await fetchUsers()
      toast.success('Usuario creado correctamente')
    } else {
      toast.error(data.message || 'No se pudo crear el usuario')
    }
  } catch (error) {
    console.error('Error al crear usuario:', error)
    toast.error('Error al crear el usuario')
  } finally {
    saving.value = false
  }
}

const deleteUser = async (id: number) => {
  const ok = await confirm('¿Estás seguro de eliminar este usuario?', {
    title: 'Eliminar usuario',
    confirmText: 'Eliminar',
    tone: 'danger',
  })
  if (!ok) return

  try {
    const response = await fetch(apiUrl(`/admin/users.php?id=${id}`), {
      method: 'DELETE'
    })
    
    const data = await response.json()
    
    if (data.success) {
      await fetchUsers()
      toast.success('Usuario eliminado correctamente')
    } else {
      toast.error(data.message || 'No se pudo eliminar el usuario')
    }
  } catch (error) {
    console.error('Error al eliminar usuario:', error)
    toast.error('Error al eliminar el usuario')
  }
}

const closeUserForm = () => {
  showUserForm.value = false
  userForm.value = {
    nombre_completo: '',
    usuario: '',
    password: ''
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

const formatDateTime = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(async () => {
  // Verificar autenticación INMEDIATAMENTE antes de hacer nada más
  console.log('[Dashboard] Componente montado, verificando autenticación...')
  await checkAuth()
  
  // Solo cargar datos si el usuario está autenticado y checkAuth no redirigió
  // checkAuth redirigirá con window.location.href si no está autenticado
  if (user.value && !checkingAuth.value) {
    console.log('[Dashboard] Usuario autenticado, cargando datos...')
    await fetchArticles()
    await fetchUsers()
  }
})
</script>

<style scoped>
.admin-dashboard {
  min-height: 100vh;
  background: #f7fafc;
  padding-top: 0;
}

.auth-checking {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 20px;
  background: #f7fafc;
}

.auth-checking p {
  font-size: 18px;
  color: #4a5568;
  font-weight: 500;
}

.loading-spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #e2e8f0;
  border-top-color: #1a365d;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.admin-header {
  background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
  color: white;
  padding: 20px 0;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-content h1 {
  font-size: 28px;
  margin: 0;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 20px;
}

.user-info {
  font-size: 16px;
}

.logout-button {
  background: rgba(255, 255, 255, 0.2);
  border: 2px solid white;
  color: white;
  padding: 8px 20px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.logout-button:hover {
  background: white;
  color: #1a365d;
}

.admin-container {
  padding: 40px 20px;
}

.admin-tabs {
  display: flex;
  gap: 10px;
  margin-bottom: 30px;
  border-bottom: 2px solid #e2e8f0;
}

.tab-button {
  background: transparent;
  border: none;
  padding: 15px 30px;
  font-size: 16px;
  font-weight: 600;
  color: #718096;
  cursor: pointer;
  border-bottom: 3px solid transparent;
  transition: all 0.3s ease;
}

.tab-button:hover {
  color: #1a365d;
}

.tab-button.active {
  color: #1a365d;
  border-bottom-color: #1a365d;
}

.tab-content {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.section-header h2 {
  font-size: 24px;
  color: #1a365d;
}

.add-button {
  background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.add-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(26, 54, 93, 0.3);
}

.article-form-modal,
.user-form-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 25px 30px;
  border-bottom: 2px solid #e2e8f0;
}

.modal-header h3 {
  font-size: 24px;
  color: #1a365d;
  margin: 0;
}

.close-button {
  background: transparent;
  border: none;
  font-size: 32px;
  color: #718096;
  cursor: pointer;
  line-height: 1;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-button:hover {
  color: #1a365d;
}

.article-form,
.user-form {
  padding: 30px;
}

.form-group {
  margin-bottom: 25px;
}

.form-group label {
  display: block;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 8px;
  font-size: 14px;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 12px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 16px;
  font-family: inherit;
  transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #1a365d;
}

.form-group small {
  display: block;
  margin-top: 5px;
  color: #718096;
  font-size: 12px;
}

.image-upload {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.upload-button {
  background: #e2e8f0;
  border: 2px dashed #cbd5e0;
  color: #4a5568;
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  align-self: flex-start;
}

.upload-button:hover {
  background: #cbd5e0;
  border-color: #a0aec0;
}

.image-preview {
  max-width: 300px;
  border-radius: 8px;
  overflow: hidden;
}

.image-preview img {
  width: 100%;
  height: auto;
  display: block;
}

.form-actions {
  display: flex;
  gap: 15px;
  margin-top: 30px;
}

.save-button {
  background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
  color: white;
  border: none;
  padding: 12px 30px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.save-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(26, 54, 93, 0.3);
}

.save-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.cancel-button {
  background: transparent;
  border: 2px solid #cbd5e0;
  color: #4a5568;
  padding: 12px 30px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.cancel-button:hover {
  background: #e2e8f0;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #718096;
}

.articles-list,
.users-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.article-item,
.user-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  background: #f7fafc;
  border-radius: 8px;
  border-left: 4px solid #1a365d;
}

.article-info,
.user-info {
  flex: 1;
}

.article-info h3,
.user-info h3 {
  font-size: 20px;
  color: #1a365d;
  margin-bottom: 8px;
}

.article-meta,
.user-meta {
  font-size: 14px;
  color: #718096;
}

.article-actions,
.user-actions {
  display: flex;
  gap: 10px;
}

.comments-button {
  background: #0f766e;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.comments-button:hover {
  background: #115e59;
}

.comments-modal-body {
  padding: 20px 30px 30px;
}

.empty-comments {
  padding: 25px;
  background: #f7fafc;
  border-radius: 8px;
  border-left: 4px solid #1a365d;
  color: #4a5568;
}

.comments-admin-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.comment-admin-item {
  padding: 18px;
  background: #f7fafc;
  border-radius: 10px;
  border-left: 4px solid #1a365d;
}

.comment-admin-replies {
  margin-top: 12px;
  padding-left: 18px;
  border-left: 3px solid #cbd5e0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.comment-admin-reply {
  padding: 14px;
  background: #ffffff;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
}

.comment-admin-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 8px;
}

.comment-admin-author {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.comment-admin-meta {
  font-size: 12px;
  color: #718096;
}

.comment-admin-text {
  font-size: 14px;
  color: #2d3748;
  line-height: 1.55;
  white-space: pre-wrap;
}

.edit-button {
  background: #2c5282;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.edit-button:hover {
  background: #1a365d;
}

.delete-button {
  background: #e53e3e;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.delete-button:hover:not(:disabled) {
  background: #c53030;
}

.delete-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    gap: 15px;
    text-align: center;
  }

  .admin-tabs {
    flex-direction: column;
  }

  .tab-content {
    padding: 20px;
  }

  .article-item,
  .user-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }

  .article-actions,
  .user-actions {
    width: 100%;
  }

  .edit-button,
  .delete-button,
  .comments-button {
    flex: 1;
  }
}
</style>


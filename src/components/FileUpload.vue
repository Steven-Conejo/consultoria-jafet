<template>
  <div class="file-upload" :class="{ compact: compact }">
    <div class="upload-area" 
         :class="{ 'drag-over': isDragOver, 'uploading': isUploading, 'success': uploadSuccess, 'error': uploadError, 'compact': compact }"
         @drop.prevent="handleDrop"
         @dragover.prevent="isDragOver = true"
         @dragleave.prevent="isDragOver = false"
         @click="triggerFileInput">
      <input
        ref="fileInput"
        type="file"
        accept=".pdf,.doc,.docx"
        @change="handleFileSelect"
        class="file-input"
      />
      <div v-if="!isUploading && !uploadSuccess && !uploadError" class="upload-content" :class="{ compact: compact }">
        <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="17 8 12 3 7 8"/>
          <line x1="12" y1="3" x2="12" y2="15"/>
        </svg>
        <h3>Arrastra tus archivos aquí</h3>
        <p>o haz clic para seleccionar</p>
        <span class="file-types">Soporta: PDF, DOC, DOCX (Máx. 10MB)</span>
      </div>
      <div v-if="isUploading" class="upload-status">
        <div class="spinner"></div>
        <p>Analizando archivo y escaneando en busca de amenazas...</p>
        <div class="progress-bar">
          <div class="progress" :style="{ width: uploadProgress + '%' }"></div>
        </div>
      </div>
      <div v-if="uploadSuccess" class="upload-status success-status">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
          <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
        <h3>Archivo verificado exitosamente</h3>
        <p>{{ successMessage }}</p>
      </div>
      <div v-if="uploadError" class="upload-status error-status">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <h3>Error en la verificación</h3>
        <p>{{ errorMessage }}</p>
        <button @click.stop="resetUpload" class="retry-btn">Intentar de nuevo</button>
      </div>
    </div>
    <div v-if="selectedFile" class="file-info">
      <div class="file-details">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
        </svg>
        <div>
          <p class="file-name">{{ selectedFile.name }}</p>
          <p class="file-size">{{ formatFileSize(selectedFile.size) }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  mode?: 'upload' | 'select' // 'upload' para comportamiento original, 'select' para solo validar
  compact?: boolean // Si es true, usa un diseño más compacto
}>()

const emit = defineEmits<{
  fileSelected: [file: File]
  fileRemoved: []
}>()

const fileInput = ref<HTMLInputElement | null>(null)
const isDragOver = ref(false)
const isUploading = ref(false)
const uploadSuccess = ref(false)
const uploadError = ref(false)
const uploadProgress = ref(0)
const selectedFile = ref<File | null>(null)
const successMessage = ref('')
const errorMessage = ref('')

const mode = props.mode || 'upload'
const compact = props.compact || false

const MAX_FILE_SIZE = 10 * 1024 * 1024 // 10MB
const ALLOWED_TYPES = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']

const triggerFileInput = () => {
  fileInput.value?.click()
}

const validateFile = (file: File): string | null => {
  if (file.size > MAX_FILE_SIZE) {
    return 'El archivo es demasiado grande. Tamaño máximo: 10MB'
  }
  
  if (!ALLOWED_TYPES.includes(file.type) && !file.name.match(/\.(pdf|doc|docx)$/i)) {
    return 'Tipo de archivo no permitido. Solo se aceptan PDF, DOC y DOCX'
  }

  // Validar extensión real del archivo
  const extension = file.name.split('.').pop()?.toLowerCase()
  if (!['pdf', 'doc', 'docx'].includes(extension || '')) {
    return 'Extensión de archivo no válida'
  }

  return null
}

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    processFile(target.files[0])
  }
}

const handleDrop = (event: DragEvent) => {
  isDragOver.value = false
  if (event.dataTransfer?.files && event.dataTransfer.files.length > 0) {
    processFile(event.dataTransfer.files[0])
  }
}

const processFile = async (file: File) => {
  // Validación inicial
  const validationError = validateFile(file)
  if (validationError) {
    uploadError.value = true
    errorMessage.value = validationError
    return
  }

  selectedFile.value = file
  uploadError.value = false
  uploadSuccess.value = false

  // Si el modo es 'select', solo validar y emitir evento
  if (mode === 'select') {
    uploadSuccess.value = true
    successMessage.value = 'Archivo seleccionado correctamente'
    emit('fileSelected', file)
    return
  }

  // Modo 'upload': comportamiento original
  isUploading.value = true
  uploadProgress.value = 0

  // Simular progreso
  const progressInterval = setInterval(() => {
    if (uploadProgress.value < 90) {
      uploadProgress.value += 10
    }
  }, 200)

  try {
    // Crear FormData
    const formData = new FormData()
    formData.append('file', file)
    formData.append('security_check', 'true')

    // Enviar al backend PHP
    const response = await fetch('/api/upload.php', {
      method: 'POST',
      body: formData
    })

    clearInterval(progressInterval)
    uploadProgress.value = 100

    const result = await response.json()

    if (response.ok && result.success) {
      uploadSuccess.value = true
      successMessage.value = result.message || 'Tu archivo ha sido verificado y está seguro'
      isUploading.value = false
    } else {
      throw new Error(result.message || 'Error al procesar el archivo')
    }
  } catch (error) {
    clearInterval(progressInterval)
    isUploading.value = false
    uploadError.value = true
    errorMessage.value = error instanceof Error ? error.message : 'Error al subir el archivo. Por favor, inténtalo de nuevo.'
  }
}

const resetUpload = () => {
  isUploading.value = false
  uploadSuccess.value = false
  uploadError.value = false
  uploadProgress.value = 0
  selectedFile.value = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
  emit('fileRemoved')
}

// Exponer método para resetear desde el componente padre
defineExpose({
  resetUpload,
  getSelectedFile: () => selectedFile.value
})

const formatFileSize = (bytes: number): string => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
}
</script>

<style scoped>
.file-upload {
  width: 100%;
}

.upload-area {
  border: 3px dashed #cbd5e0;
  border-radius: 12px;
  padding: 60px 40px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  background: #f7fafc;
  position: relative;
  min-height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.file-upload.compact .upload-area,
.upload-area.compact {
  padding: 16px 12px !important;
  min-height: 110px !important;
  border-width: 2px;
}

.file-upload.compact .upload-content.compact .upload-icon {
  width: 28px !important;
  height: 28px !important;
  margin-bottom: 8px !important;
}

.file-upload.compact .upload-content.compact h3 {
  font-size: 15px !important;
  margin-bottom: 4px !important;
}

.file-upload.compact .upload-content.compact p {
  font-size: 12px !important;
  margin-bottom: 5px !important;
}

.file-upload.compact .upload-content.compact .file-types {
  font-size: 10px !important;
}

.file-upload.compact .file-info {
  margin-top: 12px;
  padding: 12px;
}

.file-upload.compact .file-details svg {
  width: 30px;
  height: 30px;
}

.file-upload.compact .file-name {
  font-size: 14px;
}

.file-upload.compact .file-size {
  font-size: 12px;
}

.file-upload.compact .upload-status svg {
  width: 35px !important;
  height: 35px !important;
}

.file-upload.compact .spinner {
  width: 35px !important;
  height: 35px !important;
}

.upload-area:hover {
  border-color: #1a365d;
  background: #edf2f7;
}

.upload-area.drag-over {
  border-color: #1a365d;
  background: #e6fffa;
  transform: scale(1.02);
}

.upload-area.uploading {
  border-color: #4299e1;
  background: #ebf8ff;
}

.upload-area.success {
  border-color: #48bb78;
  background: #f0fff4;
}

.upload-area.error {
  border-color: #f56565;
  background: #fff5f5;
}

.file-input {
  display: none;
}

.upload-content {
  width: 100%;
}

.upload-icon {
  width: 50px;
  height: 50px;
  color: #1a365d;
  margin: 0 auto 20px;
}

.upload-content h3 {
  font-size: clamp(20px, 3vw, 24px);
  font-weight: 600;
  color: #1a365d;
  margin-bottom: 10px;
}

.upload-content p {
  font-size: 16px;
  color: #4a5568;
  margin-bottom: 15px;
}

.file-types {
  font-size: 14px;
  color: #718096;
  display: block;
  margin-top: 10px;
}

.upload-status {
  width: 100%;
}

.upload-status h3 {
  font-size: 20px;
  font-weight: 600;
  margin: 20px 0 10px;
}

.upload-status p {
  color: #4a5568;
  margin-bottom: 20px;
}

.success-status {
  color: #22543d;
}

.success-status svg {
  width: 50px;
  height: 50px;
  color: #48bb78;
  margin: 0 auto;
}

.error-status {
  color: #742a2a;
}

.error-status svg {
  width: 50px;
  height: 50px;
  color: #f56565;
  margin: 0 auto;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #e2e8f0;
  border-top: 4px solid #1a365d;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.progress-bar {
  width: 100%;
  height: 8px;
  background: #e2e8f0;
  border-radius: 4px;
  overflow: hidden;
  margin-top: 20px;
}

.progress {
  height: 100%;
  background: linear-gradient(90deg, #1a365d, #2c5282);
  transition: width 0.3s ease;
}

.retry-btn {
  padding: 10px 24px;
  background: #1a365d;
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 15px;
}

.retry-btn:hover {
  background: #2c5282;
  transform: translateY(-2px);
}

.file-info {
  margin-top: 20px;
  padding: 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.file-details {
  display: flex;
  align-items: center;
  gap: 15px;
}

.file-details svg {
  width: 40px;
  height: 40px;
  color: #1a365d;
  flex-shrink: 0;
}

.file-name {
  font-weight: 600;
  color: #1a365d;
  margin-bottom: 5px;
}

.file-size {
  font-size: 14px;
  color: #718096;
}

@media (max-width: 768px) {
  .upload-area {
    padding: 40px 20px;
    min-height: 250px;
  }

  .upload-icon {
    width: 40px;
    height: 40px;
  }

  .upload-content h3 {
    font-size: 18px;
  }

  .upload-content p {
    font-size: 14px;
  }

  .file-types {
    font-size: 12px;
  }

  .success-status svg,
  .error-status svg {
    width: 40px;
    height: 40px;
  }

  .spinner {
    width: 40px;
    height: 40px;
  }

  .upload-status h3 {
    font-size: 18px;
  }
}

@media (max-width: 480px) {
  .upload-area {
    padding: 30px 15px;
    min-height: 220px;
  }

  .upload-icon {
    width: 35px;
    height: 35px;
  }
}
</style>


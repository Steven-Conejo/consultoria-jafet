<template>
  <div class="rich-text-editor">
    <div class="toolbar">
      <div class="toolbar-group">
        <button 
          type="button"
          @click="execCommand('bold')" 
          :class="{ active: isActive('bold') }"
          title="Negrita"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/>
            <path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/>
          </svg>
        </button>
        <button 
          type="button"
          @click="execCommand('italic')" 
          :class="{ active: isActive('italic') }"
          title="Cursiva"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="4" x2="10" y2="4"/>
            <line x1="14" y1="20" x2="5" y2="20"/>
            <line x1="15" y1="4" x2="9" y2="20"/>
          </svg>
        </button>
        <button 
          type="button"
          @click="execCommand('underline')" 
          :class="{ active: isActive('underline') }"
          title="Subrayar"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 3v7a6 6 0 0 0 6 6 6 6 0 0 0 6-6V3"/>
            <line x1="4" y1="21" x2="20" y2="21"/>
          </svg>
        </button>
      </div>

      <div class="toolbar-group">
        <div class="dropdown">
          <button 
            type="button"
            @click="toggleDropdown('fontSize')"
            class="dropdown-toggle"
            title="Tamaño de fuente"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <text x="4" y="12" font-size="10">Aa</text>
              <text x="4" y="20" font-size="14">Aa</text>
            </svg>
            <span class="dropdown-arrow">▼</span>
          </button>
          <div v-if="activeDropdown === 'fontSize'" class="dropdown-menu">
            <button type="button" @click="setFontSize('12px')">Pequeño</button>
            <button type="button" @click="setFontSize('16px')">Mediano</button>
            <button type="button" @click="setFontSize('18px')">Grande</button>
            <button type="button" @click="setFontSize('24px')">Muy Grande</button>
          </div>
        </div>

        <div class="dropdown">
          <button 
            type="button"
            @click.stop="toggleDropdown('color')"
            class="dropdown-toggle"
            title="Color de texto"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>
            </svg>
            <span class="color-indicator" :style="{ backgroundColor: currentColor }"></span>
            <span class="dropdown-arrow">▼</span>
          </button>
          <div v-if="activeDropdown === 'color'" class="dropdown-menu color-picker">
            <button 
              type="button"
              v-for="color in colors" 
              :key="color.value"
              @click.stop="setColor(color.value)"
              :class="{ active: currentColor === color.value }"
              :style="{ backgroundColor: color.value }"
              :title="color.name"
            >
            </button>
          </div>
        </div>
      </div>

      <div class="toolbar-group">
        <button 
          type="button"
          @click="execCommand('insertUnorderedList')" 
          :class="{ active: isActive('insertUnorderedList') }"
          title="Lista con viñetas"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="8" y1="6" x2="21" y2="6"/>
            <line x1="8" y1="12" x2="21" y2="12"/>
            <line x1="8" y1="18" x2="21" y2="18"/>
            <line x1="3" y1="6" x2="3.01" y2="6"/>
            <line x1="3" y1="12" x2="3.01" y2="12"/>
            <line x1="3" y1="18" x2="3.01" y2="18"/>
          </svg>
        </button>
        <button 
          type="button"
          @click="execCommand('insertOrderedList')" 
          :class="{ active: isActive('insertOrderedList') }"
          title="Lista numerada"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="10" y1="6" x2="21" y2="6"/>
            <line x1="10" y1="12" x2="21" y2="12"/>
            <line x1="10" y1="18" x2="21" y2="18"/>
            <line x1="4" y1="6" x2="4" y2="6"/>
            <line x1="4" y1="12" x2="4" y2="12"/>
            <line x1="4" y1="18" x2="4" y2="18"/>
          </svg>
        </button>
      </div>

      <div class="toolbar-group">
        <button 
          type="button"
          @click="execCommand('justifyLeft')" 
          :class="{ active: isActive('justifyLeft') }"
          title="Alinear izquierda"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="21" y1="10" x2="7" y2="10"/>
            <line x1="21" y1="6" x2="3" y2="6"/>
            <line x1="21" y1="14" x2="3" y2="14"/>
            <line x1="21" y1="18" x2="7" y2="18"/>
          </svg>
        </button>
        <button 
          type="button"
          @click="execCommand('justifyCenter')" 
          :class="{ active: isActive('justifyCenter') }"
          title="Centrar"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="10" x2="6" y2="10"/>
            <line x1="21" y1="6" x2="3" y2="6"/>
            <line x1="21" y1="14" x2="3" y2="14"/>
            <line x1="18" y1="18" x2="6" y2="18"/>
          </svg>
        </button>
        <button 
          type="button"
          @click="execCommand('justifyRight')" 
          :class="{ active: isActive('justifyRight') }"
          title="Alinear derecha"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="21" y1="10" x2="7" y2="10"/>
            <line x1="21" y1="6" x2="3" y2="6"/>
            <line x1="21" y1="14" x2="3" y2="14"/>
            <line x1="21" y1="18" x2="7" y2="18"/>
          </svg>
        </button>
      </div>


    </div>
    
    <div 
      ref="editor"
      class="editor-content"
      contenteditable="true"
      @input="handleInput"
      @keydown="handleKeyDown"
      @focus="updateActiveState"
      @blur="updateActiveState"
      @click="updateActiveState"
      @mouseenter="handleMouseEnter"
      v-html="modelValue"
    ></div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, nextTick } from 'vue'

const props = defineProps<{
  modelValue: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const editor = ref<HTMLElement | null>(null)
const activeDropdown = ref<string | null>(null)
const currentColor = ref('#000000')

const colors = [
  { name: 'Negro', value: '#000000' },
  { name: 'Gris Oscuro', value: '#4a5568' },
  { name: 'Gris', value: '#718096' },
  { name: 'Azul Oscuro', value: '#1a365d' },
  { name: 'Azul', value: '#2c5282' },
  { name: 'Rojo', value: '#e53e3e' },
  { name: 'Verde', value: '#38a169' },
  { name: 'Naranja', value: '#dd6b20' },
  { name: 'Morado', value: '#805ad5' }
]

const execCommand = (command: string, showUI: boolean = false, value: string | null = null) => {
  document.execCommand(command, showUI, value || undefined)
  updateActiveState()
  handleInput()
}

const isActive = (command: string): boolean => {
  try {
    return document.queryCommandState(command)
  } catch {
    return false
  }
}

const setColor = (color: string) => {
  currentColor.value = color
  if (editor.value) {
    // Asegurar que el editor tenga foco
    editor.value.focus()
    
    const selection = window.getSelection()
    if (selection && selection.rangeCount > 0) {
      const range = selection.getRangeAt(0)
      if (!range.collapsed) {
        // Hay texto seleccionado - aplicar color directamente
        document.execCommand('foreColor', false, color)
      } else {
        // No hay texto seleccionado, aplicar al siguiente texto que se escriba
        // Usar execCommand para establecer el color
        document.execCommand('foreColor', false, color)
      }
    } else {
      // Si no hay selección, crear un rango al final del contenido
      const range = document.createRange()
      range.selectNodeContents(editor.value)
      range.collapse(false)
      const selection = window.getSelection()
      if (selection) {
        selection.removeAllRanges()
        selection.addRange(range)
        document.execCommand('foreColor', false, color)
      }
    }
  }
  activeDropdown.value = null
  handleInput()
}

const setFontSize = (size: string) => {
  execCommand('fontSize', false, '3')
  if (editor.value) {
    const selection = window.getSelection()
    if (selection && selection.rangeCount > 0) {
      const range = selection.getRangeAt(0)
      const selectedText = range.extractContents()
      const span = document.createElement('span')
      span.style.fontSize = size
      span.appendChild(selectedText)
      range.insertNode(span)
      selection.removeAllRanges()
      selection.addRange(range)
    }
  }
  activeDropdown.value = null
  handleInput()
}

const toggleDropdown = (dropdown: string) => {
  activeDropdown.value = activeDropdown.value === dropdown ? null : dropdown
}

const saveSelection = () => {
  const selection = window.getSelection()
  if (selection && selection.rangeCount > 0 && editor.value) {
    const range = selection.getRangeAt(0)
    const preCaretRange = range.cloneRange()
    preCaretRange.selectNodeContents(editor.value)
    preCaretRange.setEnd(range.endContainer, range.endOffset)
    return preCaretRange.toString().length
  }
  return null
}

const restoreSelection = (savedPos: number | null) => {
  if (savedPos === null || !editor.value) return
  
  const selection = window.getSelection()
  if (selection) {
    const range = document.createRange()
    let charCount = 0
    let nodeStack: Node[] = [editor.value]
    let node: Node | undefined
    let foundStart = false
    
    while (!foundStart && (node = nodeStack.pop())) {
      if (node.nodeType === Node.TEXT_NODE) {
        const nextCharCount = charCount + (node.textContent?.length || 0)
        if (savedPos <= nextCharCount) {
          range.setStart(node, savedPos - charCount)
          range.setEnd(node, savedPos - charCount)
          foundStart = true
        }
        charCount = nextCharCount
      } else {
        let i = node.childNodes.length
        while (i--) {
          const child = node.childNodes[i]
          if (child) {
            nodeStack.push(child)
          }
        }
      }
    }
    
    if (foundStart) {
      selection.removeAllRanges()
      selection.addRange(range)
    }
  }
}

const handleKeyDown = (e: KeyboardEvent) => {
  if (e.key === 'Enter') {
    e.preventDefault()
    const selection = window.getSelection()
    if (selection && selection.rangeCount > 0) {
      const range = selection.getRangeAt(0)
      const p = document.createElement('p')
      p.innerHTML = '<br>'
      range.insertNode(p)
      // Mover el cursor al nuevo párrafo
      range.setStart(p, 0)
      range.collapse(true)
      selection.removeAllRanges()
      selection.addRange(range)
      handleInput()
    }
  }
}

const handleInput = () => {
  if (editor.value) {
    // Asegurar que siempre haya al menos un párrafo
    if (!editor.value.innerHTML.trim() || editor.value.innerHTML === '<br>') {
      editor.value.innerHTML = '<p><br></p>'
    }
    const savedPos = saveSelection()
    emit('update:modelValue', editor.value.innerHTML)
    if (savedPos !== null) {
      nextTick(() => {
        restoreSelection(savedPos)
      })
    }
  }
}

const updateActiveState = () => {
  nextTick(() => {
    // Forzar actualización del estado activo
  })
}

const handleMouseEnter = () => {
  if (editor.value) {
    editor.value.style.cursor = 'text'
  }
}

// Cerrar dropdowns al hacer clic fuera
onMounted(() => {
  // Asegurar que el editor tenga contenido inicial
  if (editor.value && !editor.value.innerHTML.trim()) {
    editor.value.innerHTML = '<p><br></p>'
  }
  
  document.addEventListener('click', (e) => {
    const target = e.target as HTMLElement
    // No cerrar si el click es en el dropdown o en un botón del dropdown
    if (target.closest('.dropdown-menu') || target.closest('.dropdown-toggle')) {
      return
    }
    if (editor.value && !editor.value.contains(target)) {
      activeDropdown.value = null
    }
  })
})

watch(() => props.modelValue, (newValue) => {
  if (editor.value && editor.value.innerHTML !== newValue) {
    const savedPos = saveSelection()
    // Si el nuevo valor está vacío, asegurar que haya un párrafo
    if (!newValue || newValue.trim() === '' || newValue === '<br>') {
      editor.value.innerHTML = '<p><br></p>'
    } else {
      editor.value.innerHTML = newValue
    }
    if (savedPos !== null) {
      nextTick(() => {
        restoreSelection(savedPos)
      })
    }
  }
})
</script>

<style scoped>
.rich-text-editor {
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
  background: white;
}

.toolbar {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  padding: 10px;
  background: #f7fafc;
  border-bottom: 2px solid #e2e8f0;
  align-items: center;
}

.toolbar-group {
  display: flex;
  gap: 4px;
  padding-right: 8px;
  border-right: 1px solid #e2e8f0;
}

.toolbar-group:last-child {
  border-right: none;
}

.toolbar button {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 8px 10px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  color: #2d3748;
  font-size: 14px;
  min-width: 36px;
  height: 36px;
}

.toolbar button:hover {
  background: #edf2f7;
  border-color: #cbd5e0;
}

.toolbar button.active {
  background: #1a365d;
  color: white;
  border-color: #1a365d;
}

.toolbar button svg {
  width: 18px;
  height: 18px;
}

.dropdown {
  position: relative;
}

.dropdown-toggle {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 10px !important;
}

.color-indicator {
  width: 16px;
  height: 16px;
  border-radius: 3px;
  border: 1px solid #cbd5e0;
}

.dropdown-arrow {
  font-size: 10px;
  opacity: 0.6;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  margin-top: 4px;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  z-index: 100;
  min-width: 120px;
  overflow: hidden;
}

.dropdown-menu button {
  width: 100%;
  justify-content: flex-start;
  border: none;
  border-radius: 0;
  padding: 10px 14px;
  text-align: left;
  background: white;
  color: #2d3748;
  font-size: 14px;
  min-width: auto;
  height: auto;
}

.dropdown-menu button:hover {
  background: #f7fafc;
}

.dropdown-menu button.active {
  background: #e6fffa;
  color: #1a365d;
}

.color-picker {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 4px;
  padding: 8px;
  min-width: 200px;
}

.color-picker button {
  aspect-ratio: 1;
  border-radius: 6px;
  padding: 0;
  min-height: 40px;
  position: relative;
  border: 2px solid transparent;
  font-size: 0;
  overflow: hidden;
}

.color-picker button:hover {
  border-color: #1a365d;
  transform: scale(1.05);
}

.color-picker button.active {
  border-color: #1a365d;
  border-width: 3px;
}

.color-picker button::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: inherit;
}

.editor-content {
  min-height: 300px;
  max-height: 500px;
  overflow-y: auto;
  padding: 15px;
  font-size: 16px;
  line-height: 1.6;
  color: #2d3748;
  outline: none;
  cursor: text;
  caret-color: #000000;
}

.editor-content:focus {
  outline: none;
  cursor: text;
  caret-color: #000000;
}

.editor-content * {
  cursor: text;
}

.editor-content :deep(p) {
  margin-bottom: 12px;
}

.editor-content :deep(h2) {
  font-size: 24px;
  font-weight: 700;
  color: #1a365d;
  margin-top: 20px;
  margin-bottom: 12px;
}

.editor-content :deep(h3) {
  font-size: 20px;
  font-weight: 600;
  color: #2c5282;
  margin-top: 16px;
  margin-bottom: 10px;
}

.editor-content :deep(ul),
.editor-content :deep(ol) {
  margin-left: 20px;
  margin-bottom: 12px;
}

.editor-content :deep(li) {
  margin-bottom: 6px;
}

@media (max-width: 768px) {
  .toolbar {
    padding: 8px;
    gap: 6px;
  }

  .toolbar-group {
    padding-right: 6px;
  }

  .toolbar button {
    padding: 6px 8px;
    min-width: 32px;
    height: 32px;
  }

  .toolbar button svg {
    width: 16px;
    height: 16px;
  }

  .editor-content {
    min-height: 250px;
    padding: 12px;
    font-size: 14px;
  }
}
</style>


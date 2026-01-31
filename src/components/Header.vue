<template>
  <header class="header" :class="{ 'scrolled': scrolled }">
    <div class="container">
      <div class="header-content">
        <div class="logo" @click="$router.push('/')">
          <img :src="logoImage" alt="Logo" class="logo-icon" loading="eager" decoding="async" fetchpriority="high" />
          <span class="logo-text">
            <span class="logo-legis">Legis</span><span class="logo-audit">AUDIT</span>
          </span>
        </div>
        <nav ref="navRef" class="nav" :class="{ 'active': menuOpen }">
          <router-link to="/" class="nav-link" @click="handleNavLinkClick">Inicio</router-link>
          <router-link to="/nosotros" class="nav-link" @click="handleNavLinkClick">Nosotros</router-link>
          <router-link to="/servicios" class="nav-link" @click="handleNavLinkClick">Servicios</router-link>
          <router-link to="/blog" class="nav-link" @click="handleNavLinkClick">Blog</router-link>
          <router-link to="/contacto" class="nav-link" @click="handleNavLinkClick">Contáctenos</router-link>
        </nav>
        <button ref="menuToggleRef" class="menu-toggle" @click="menuOpen = !menuOpen">
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import logoImage from '@/images/Logo.png'

const scrolled = ref(false)
const menuOpen = ref(false)
const navRef = ref<HTMLElement | null>(null)
const menuToggleRef = ref<HTMLElement | null>(null)

const handleScroll = () => {
  scrolled.value = window.scrollY > 50
}

const closeMenu = () => {
  menuOpen.value = false
}

const handleClickOutside = (event: MouseEvent) => {
  // Si el menú está abierto y el clic fue fuera del menú y del botón toggle
  if (menuOpen.value) {
    const target = event.target as HTMLElement
    const nav = navRef.value
    const toggle = menuToggleRef.value
    
    // Si el clic fue fuera del menú y del botón toggle, cerrar el menú
    if (nav && toggle && !nav.contains(target) && !toggle.contains(target)) {
      closeMenu()
    }
  }
}

const handleNavLinkClick = () => {
  // Cerrar el menú cuando se hace clic en un enlace de navegación
  closeMenu()
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  // Agregar listener para cerrar el menú al hacer clic fuera
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
  border-bottom: 2px solid #FF6600;
  z-index: 1000;
  transition: all 0.3s ease;
}

.header.scrolled {
  box-shadow: 0 2px 30px rgba(0, 0, 0, 0.1);
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
  height: 80px;
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.logo:hover {
  transform: scale(1.05);
}

.logo-icon {
  width: 80px;
  height: 80px;
  object-fit: contain;
}

.logo-text {
  font-size: 24px;
  font-weight: 700;
  letter-spacing: -0.5px;
}

.logo-legis {
  color: #1a365d;
}

.logo-audit {
  color: #FF6600;
}

.nav {
  display: flex;
  gap: 30px;
  align-items: center;
}

.nav-link {
  color: #2d3748;
  text-decoration: none;
  font-weight: 500;
  font-size: 18px;
  position: relative;
  transition: color 0.3s ease;
}

.nav-link:hover {
  color: #1a365d;
}

.nav-link.router-link-active {
  color: #1a365d;
  font-weight: 600;
}

.nav-link.router-link-active::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  right: 0;
  height: 2px;
  background: #1a365d;
}

.menu-toggle {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 5px;
}

.menu-toggle span {
  width: 25px;
  height: 3px;
  background: #1a365d;
  transition: all 0.3s ease;
  border-radius: 2px;
}

@media (max-width: 768px) {
  .menu-toggle {
    display: flex;
  }

  .nav {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    flex-direction: column;
    padding: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    transform: translateY(-100%);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
  }

  .nav.active {
    transform: translateY(0);
    opacity: 1;
    visibility: visible;
  }

  .nav-link {
    padding: 10px 0;
  }
}
</style>


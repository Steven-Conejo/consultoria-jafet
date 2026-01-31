<script setup lang="ts">
import { RouterView } from 'vue-router'
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'
import ToastHost from './components/ToastHost.vue'
import ConfirmDialogHost from './components/ConfirmDialogHost.vue'
</script>

<template>
  <div id="app">
    <Header />
    <main>
      <RouterView />
    </main>
    <Footer />
    <ToastHost />
    <ConfirmDialogHost />
  </div>
</template>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

:root {
  --primary-color: #1a365d;
  --secondary-color: #2c5282;
  --accent-color: #ffd700;
  --text-color: #2d3748;
  --text-light: #4a5568;
  --bg-light: #f7fafc;
  --border-color: #e2e8f0;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  line-height: 1.6;
  color: var(--text-color);
  background: #ffffff;
}

#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
}

a {
  color: inherit;
  text-decoration: none;
}

button {
  font-family: inherit;
}

/* Smooth scrolling */
html {
  scroll-behavior: smooth;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 10px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #1a365d;
  border-radius: 5px;
}

::-webkit-scrollbar-thumb:hover {
  background: #2c5282;
}

/* Optimización de imágenes */
img {
  max-width: 100%;
  height: auto;
  image-rendering: -webkit-optimize-contrast;
  image-rendering: crisp-edges;
  image-rendering: optimize-quality;
}

/* Mejora de rendimiento para imágenes de fondo */
.hero-bg-image {
  will-change: transform;
  backface-visibility: hidden;
  transform: translateZ(0);
}

/* Optimización para imágenes lazy */
img[loading="lazy"] {
  content-visibility: auto;
}
</style>

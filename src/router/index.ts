import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import AboutView from '../views/AboutView.vue'
import ServicesView from '../views/ServicesView.vue'
import ContactView from '../views/ContactView.vue'
import BlogView from '../views/BlogView.vue'
import BlogArticleView from '../views/BlogArticleView.vue'
import AdminLoginView from '../views/AdminLoginView.vue'
import AdminDashboardView from '../views/AdminDashboardView.vue'
import DonationView from '../views/DonationView.vue'
import { apiUrl } from '@/config/api'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/nosotros',
      name: 'about',
      component: AboutView
    },
    {
      path: '/servicios',
      name: 'services',
      component: ServicesView
    },
    {
      path: '/contacto',
      name: 'contact',
      component: ContactView
    },
    {
      path: '/blog',
      name: 'blog',
      component: BlogView
    },
    {
      path: '/blog/:id',
      name: 'blog-article',
      component: BlogArticleView
    },
    {
      path: '/admin',
      name: 'admin-login',
      component: AdminLoginView
    },
    {
      path: '/admin/dashboard',
      name: 'admin-dashboard',
      component: AdminDashboardView,
      meta: { requiresAuth: true }
    },
    {
      path: '/donaciones',
      name: 'donation',
      component: DonationView
    }
  ],
  scrollBehavior(_to, _from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0, behavior: 'smooth' }
    }
  }
})

// Navigation guard para proteger rutas admin
// IMPORTANTE: Este guard se ejecuta ANTES de que el componente se cargue
router.beforeEach(async (to, _from, next) => {
  // Si la ruta requiere autenticación
  if (to.matched.some(record => record.meta.requiresAuth)) {
    // Bloquear acceso inmediatamente - redirigir a login primero
    // Luego verificar autenticación asíncronamente
    
    console.log('[Auth Guard] Protegiendo ruta:', to.path)
    
    try {
      // Verificar autenticación ANTES de permitir cualquier acceso
      const authUrl = apiUrl('/admin/auth.php') + '?check=1&t=' + Date.now()
      
      const response = await fetch(authUrl, {
        method: 'GET',
        credentials: 'include', // Incluir cookies de sesión
        cache: 'no-store', // No usar caché para nada
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Cache-Control': 'no-cache, no-store, must-revalidate',
          'Pragma': 'no-cache'
        }
      })
      
      // Si la respuesta no es exitosa (no es 200), BLOQUEAR acceso
      if (!response || !response.ok || response.status !== 200) {
        console.warn('[Auth Guard] Respuesta no exitosa, redirigiendo al login. Status:', response?.status)
        // REDIRIGIR inmediatamente al login sin permitir acceso
        window.location.href = '/admin'
        return // IMPORTANTE: No continuar
      }
      
      // Intentar parsear JSON
      let data
      try {
        const text = await response.text()
        if (!text || text.trim() === '') {
          // Respuesta vacía = no autenticado
          console.warn('[Auth Guard] Respuesta vacía, redirigiendo al login')
          window.location.href = '/admin'
          return
        }
        data = JSON.parse(text)
      } catch (jsonError) {
        // Error al parsear = bloquear acceso
        console.error('[Auth Guard] Error parseando JSON, redirigiendo al login:', jsonError)
        window.location.href = '/admin'
        return
      }
      
      // Verificar explícitamente que está autenticado
      // Solo permitir si success === true Y authenticated === true
      const isAuthenticated = data && 
                              data.success === true && 
                              data.authenticated === true
      
      if (isAuthenticated) {
        // Usuario autenticado, PERMITIR acceso
        console.log('[Auth Guard] Usuario autenticado, permitiendo acceso')
        next()
      } else {
        // Usuario NO autenticado, BLOQUEAR acceso
        console.warn('[Auth Guard] Usuario no autenticado, redirigiendo al login')
        window.location.href = '/admin'
      }
    } catch (error) {
      // Cualquier error = bloquear acceso por seguridad
      console.error('[Auth Guard] Error verificando autenticación, redirigiendo al login:', error)
      window.location.href = '/admin'
    }
  } else {
    // Ruta pública, permitir acceso sin verificación
    next()
  }
})

export default router

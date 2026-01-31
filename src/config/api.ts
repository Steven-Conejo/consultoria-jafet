/**
 * Configuración de URLs de API
 */

// Usamos siempre la ruta relativa '/api' y dejamos que Vite proxy
// redirija hacia XAMPP según la configuración de vite.config.ts
export const API_BASE_URL = '/api'

/**
 * Construye una URL completa para la API
 */
export function apiUrl(path: string): string {
  // Normalizar path
  const cleanPath = path.startsWith('/') ? path : `/${path}`
  // Evitar duplicar /api si ya viene incluido
  if (cleanPath.startsWith('/api/')) {
    return cleanPath
  }
  return `${API_BASE_URL}${cleanPath}`
}


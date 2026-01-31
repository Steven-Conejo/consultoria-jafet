/**
 * Middleware de autenticación
 */

/**
 * Verifica si el usuario está autenticado
 */
export function requireAuth(req, res, next) {
  if (!req.session || !req.session.admin_id) {
    return res.status(401).json({
      success: false,
      message: 'No autorizado'
    });
  }
  next();
}

/**
 * Verifica autenticación opcional (para endpoints que pueden funcionar con o sin auth)
 */
export function optionalAuth(req, res, next) {
  // Si hay sesión, la agregamos al request
  // Si no, continuamos sin error
  next();
}

export default { requireAuth, optionalAuth };

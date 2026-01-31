/**
 * API de Autenticación
 */

import express from 'express';
import { getDb } from '../config/db.js';
import bcrypt from 'bcryptjs';

const router = express.Router();

/**
 * POST /api/admin/auth
 * Login de administrador
 */
router.post('/', async (req, res) => {
  try {
    const { usuario, password } = req.body;
    
    if (!usuario || !password) {
      return res.status(400).json({
        success: false,
        message: 'Usuario y contraseña son requeridos'
      });
    }
    
    const db = await getDb();
    const [rows] = await db.execute(
      'SELECT id, nombre_completo, usuario, password FROM usuarios WHERE usuario = ? AND activo = 1',
      [usuario]
    );
    
    if (rows.length === 0) {
      return res.status(401).json({
        success: false,
        message: 'Usuario o contraseña incorrectos'
      });
    }
    
    const user = rows[0];
    const passwordMatch = await bcrypt.compare(password, user.password);
    
    if (!passwordMatch) {
      return res.status(401).json({
        success: false,
        message: 'Usuario o contraseña incorrectos'
      });
    }
    
    // Crear sesión
    req.session.admin_id = user.id;
    req.session.admin_usuario = user.usuario;
    req.session.admin_nombre = user.nombre_completo;
    
    res.json({
      success: true,
      user: {
        id: user.id,
        nombre_completo: user.nombre_completo,
        usuario: user.usuario
      }
    });
    
  } catch (error) {
    console.error('Error en login:', error);
    res.status(500).json({
      success: false,
      message: 'Error al procesar la autenticación'
    });
  }
});

/**
 * GET /api/admin/auth?check=1
 * Verificar sesión activa
 */
router.get('/', async (req, res) => {
  try {
    if (req.query.check === '1') {
      if (req.session && req.session.admin_id) {
        const db = await getDb();
        const [rows] = await db.execute(
          'SELECT id, nombre_completo, usuario FROM usuarios WHERE id = ? AND activo = 1',
          [req.session.admin_id]
        );
        
        if (rows.length > 0) {
          return res.json({
            success: true,
            authenticated: true,
            user: rows[0]
          });
        }
      }
      
      return res.status(401).json({
        success: false,
        authenticated: false,
        message: 'No autenticado'
      });
    }
    
    res.status(400).json({
      success: false,
      message: 'Parámetro check requerido'
    });
    
  } catch (error) {
    console.error('Error al verificar sesión:', error);
    res.status(500).json({
      success: false,
      message: 'Error al verificar autenticación'
    });
  }
});

/**
 * DELETE /api/admin/auth
 * Logout
 */
router.delete('/', (req, res) => {
  req.session.destroy((err) => {
    if (err) {
      return res.status(500).json({
        success: false,
        message: 'Error al cerrar sesión'
      });
    }
    
    res.json({
      success: true,
      message: 'Sesión cerrada correctamente'
    });
  });
});

export default router;

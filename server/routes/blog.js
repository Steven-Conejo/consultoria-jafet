/**
 * API Pública del Blog
 */

import express from 'express';
import { getDb } from '../config/db.js';

const router = express.Router();

/**
 * GET /api/blog/articles
 * Listar artículos públicos o obtener uno específico
 */
router.get('/articles', async (req, res) => {
  try {
    const { id } = req.query;
    const db = await getDb();
    
    if (id) {
      // Obtener un artículo específico
      const [rows] = await db.execute(
        'SELECT * FROM articulos WHERE id = ? AND activo = 1',
        [id]
      );
      
      if (rows.length === 0) {
        return res.status(404).json({
          success: false,
          message: 'Artículo no encontrado'
        });
      }
      
      // Incrementar vistas
      await db.execute(
        'UPDATE articulos SET vistas = vistas + 1 WHERE id = ?',
        [id]
      );
      
      res.json({
        success: true,
        articulo: rows[0]
      });
    } else {
      // Listar todos los artículos activos
      const [rows] = await db.execute(
        'SELECT id, titulo, imagen_principal, resumen, fecha_creacion, vistas FROM articulos WHERE activo = 1 ORDER BY fecha_creacion DESC'
      );
      
      res.json({
        success: true,
        articulos: rows
      });
    }
  } catch (error) {
    console.error('Error en blog/articles:', error);
    res.status(500).json({
      success: false,
      message: 'Error al obtener artículos'
    });
  }
});

/**
 * GET /api/blog/comments
 * Obtener comentarios de un artículo
 */
router.get('/comments', async (req, res) => {
  try {
    const { articulo_id } = req.query;
    
    if (!articulo_id) {
      return res.status(400).json({
        success: false,
        message: 'ID de artículo requerido'
      });
    }
    
    const db = await getDb();
    const [rows] = await db.execute(
      'SELECT * FROM comentarios WHERE articulo_id = ? AND activo = 1 ORDER BY fecha_creacion DESC',
      [articulo_id]
    );
    
    res.json({
      success: true,
      comentarios: rows
    });
  } catch (error) {
    console.error('Error en blog/comments:', error);
    res.status(500).json({
      success: false,
      message: 'Error al obtener comentarios'
    });
  }
});

/**
 * POST /api/blog/comments
 * Crear un comentario
 */
router.post('/comments', async (req, res) => {
  try {
    const { articulo_id, nombre, email, comentario } = req.body;
    
    if (!articulo_id || !nombre || !email || !comentario) {
      return res.status(400).json({
        success: false,
        message: 'Todos los campos son requeridos'
      });
    }
    
    // Validar email
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      return res.status(400).json({
        success: false,
        message: 'Email inválido'
      });
    }
    
    const db = await getDb();
    const [result] = await db.execute(
      'INSERT INTO comentarios (articulo_id, nombre, email, comentario) VALUES (?, ?, ?, ?)',
      [articulo_id, nombre.trim(), email.trim(), comentario.trim()]
    );
    
    res.json({
      success: true,
      message: 'Comentario agregado correctamente',
      id: result.insertId
    });
  } catch (error) {
    console.error('Error al crear comentario:', error);
    res.status(500).json({
      success: false,
      message: 'Error al crear comentario'
    });
  }
});

export default router;

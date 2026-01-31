/**
 * API de Administración
 */

import express from 'express';
import { requireAuth } from '../middleware/auth.js';
import { getDb } from '../config/db.js';
import multer from 'multer';
import path from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs/promises';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const router = express.Router();

// Configurar multer para subida de archivos
const upload = multer({
  dest: path.join(__dirname, '../../uploads'),
  limits: { fileSize: 10 * 1024 * 1024 }, // 10MB
  fileFilter: (req, file, cb) => {
    const allowedTypes = /jpeg|jpg|png|gif|webp/;
    const extname = allowedTypes.test(path.extname(file.originalname).toLowerCase());
    const mimetype = allowedTypes.test(file.mimetype);
    
    if (mimetype && extname) {
      return cb(null, true);
    }
    cb(new Error('Solo se permiten imágenes (JPEG, JPG, PNG, GIF, WEBP)'));
  }
});

// Aplicar autenticación a todas las rutas
router.use(requireAuth);

/**
 * GET /api/admin/articles
 * Listar artículos (admin) o obtener uno específico
 */
router.get('/articles', async (req, res) => {
  try {
    const { id, public: publicOnly } = req.query;
    const db = await getDb();
    
    if (id) {
      const [rows] = await db.execute(
        'SELECT * FROM articulos WHERE id = ?',
        [id]
      );
      
      if (rows.length === 0) {
        return res.status(404).json({
          success: false,
          message: 'Artículo no encontrado'
        });
      }
      
      res.json({
        success: true,
        articulo: rows[0]
      });
    } else {
      const where = publicOnly === '1' ? 'WHERE activo = 1' : '';
      const [rows] = await db.execute(
        `SELECT id, titulo, imagen_principal, resumen, texto, fecha_creacion, fecha_actualizacion, vistas FROM articulos ${where} ORDER BY fecha_creacion DESC`
      );
      
      res.json({
        success: true,
        articulos: rows
      });
    }
  } catch (error) {
    console.error('Error en admin/articles:', error);
    res.status(500).json({
      success: false,
      message: 'Error al obtener artículos'
    });
  }
});

/**
 * POST /api/admin/articles
 * Crear o actualizar artículo
 */
router.post('/articles', async (req, res) => {
  try {
    const { id, titulo, imagen_principal, texto } = req.body;
    
    if (!titulo || !texto) {
      return res.status(400).json({
        success: false,
        message: 'Título y texto son requeridos'
      });
    }
    
    // Generar resumen automático
    const resumen = texto.replace(/<[^>]*>/g, '').substring(0, 200) + '...';
    
    const db = await getDb();
    
    if (id) {
      // Actualizar artículo existente
      const [result] = await db.execute(
        'UPDATE articulos SET titulo = ?, imagen_principal = ?, texto = ?, resumen = ?, fecha_actualizacion = CURRENT_TIMESTAMP WHERE id = ?',
        [titulo, imagen_principal || null, texto, resumen, id]
      );
      
      if (result.affectedRows === 0) {
        return res.status(404).json({
          success: false,
          message: 'Artículo no encontrado'
        });
      }
      
      res.json({
        success: true,
        message: 'Artículo actualizado correctamente'
      });
    } else {
      // Crear nuevo artículo
      const [result] = await db.execute(
        'INSERT INTO articulos (titulo, imagen_principal, texto, resumen) VALUES (?, ?, ?, ?)',
        [titulo, imagen_principal || null, texto, resumen]
      );
      
      res.json({
        success: true,
        message: 'Artículo creado correctamente',
        id: result.insertId
      });
    }
  } catch (error) {
    console.error('Error al guardar artículo:', error);
    res.status(500).json({
      success: false,
      message: 'Error al guardar artículo'
    });
  }
});

/**
 * DELETE /api/admin/articles
 * Eliminar artículo
 */
router.delete('/articles', async (req, res) => {
  try {
    const { id } = req.query;
    
    if (!id) {
      return res.status(400).json({
        success: false,
        message: 'ID de artículo requerido'
      });
    }
    
    const db = await getDb();
    const [result] = await db.execute(
      'DELETE FROM articulos WHERE id = ?',
      [id]
    );
    
    if (result.affectedRows === 0) {
      return res.status(404).json({
        success: false,
        message: 'Artículo no encontrado'
      });
    }
    
    res.json({
      success: true,
      message: 'Artículo eliminado correctamente'
    });
  } catch (error) {
    console.error('Error al eliminar artículo:', error);
    res.status(500).json({
      success: false,
      message: 'Error al eliminar artículo'
    });
  }
});

/**
 * GET /api/admin/comments
 * Listar comentarios
 */
router.get('/comments', async (req, res) => {
  try {
    const db = await getDb();
    const [rows] = await db.execute(
      'SELECT * FROM comentarios ORDER BY fecha_creacion DESC'
    );
    
    res.json({
      success: true,
      comentarios: rows
    });
  } catch (error) {
    console.error('Error al obtener comentarios:', error);
    res.status(500).json({
      success: false,
      message: 'Error al obtener comentarios'
    });
  }
});

/**
 * DELETE /api/admin/comments
 * Eliminar comentario
 */
router.delete('/comments', async (req, res) => {
  try {
    const { id } = req.query;
    
    if (!id) {
      return res.status(400).json({
        success: false,
        message: 'ID de comentario requerido'
      });
    }
    
    const db = await getDb();
    const [result] = await db.execute(
      'DELETE FROM comentarios WHERE id = ?',
      [id]
    );
    
    if (result.affectedRows === 0) {
      return res.status(404).json({
        success: false,
        message: 'Comentario no encontrado'
      });
    }
    
    res.json({
      success: true,
      message: 'Comentario eliminado correctamente'
    });
  } catch (error) {
    console.error('Error al eliminar comentario:', error);
    res.status(500).json({
      success: false,
      message: 'Error al eliminar comentario'
    });
  }
});

/**
 * GET /api/admin/users
 * Listar usuarios
 */
router.get('/users', async (req, res) => {
  try {
    const db = await getDb();
    const [rows] = await db.execute(
      'SELECT id, nombre_completo, usuario, fecha_creacion, activo FROM usuarios ORDER BY fecha_creacion DESC'
    );
    
    res.json({
      success: true,
      usuarios: rows
    });
  } catch (error) {
    console.error('Error al obtener usuarios:', error);
    res.status(500).json({
      success: false,
      message: 'Error al obtener usuarios'
    });
  }
});

/**
 * POST /api/admin/users
 * Crear usuario
 */
router.post('/users', async (req, res) => {
  try {
    const { nombre_completo, usuario, password } = req.body;
    
    if (!nombre_completo || !usuario || !password) {
      return res.status(400).json({
        success: false,
        message: 'Todos los campos son requeridos'
      });
    }
    
    if (password.length < 4) {
      return res.status(400).json({
        success: false,
        message: 'La contraseña debe tener al menos 4 caracteres'
      });
    }
    
    const db = await getDb();
    
    // Verificar si el usuario ya existe
    const [existing] = await db.execute(
      'SELECT id FROM usuarios WHERE usuario = ?',
      [usuario]
    );
    
    if (existing.length > 0) {
      return res.status(400).json({
        success: false,
        message: 'El usuario ya existe'
      });
    }
    
    // Hash de contraseña
    const bcrypt = (await import('bcryptjs')).default;
    const passwordHash = await bcrypt.hash(password, 10);
    
    const [result] = await db.execute(
      'INSERT INTO usuarios (nombre_completo, usuario, password) VALUES (?, ?, ?)',
      [nombre_completo.trim(), usuario.trim(), passwordHash]
    );
    
    res.json({
      success: true,
      message: 'Usuario creado correctamente',
      id: result.insertId
    });
  } catch (error) {
    console.error('Error al crear usuario:', error);
    res.status(500).json({
      success: false,
      message: 'Error al crear usuario'
    });
  }
});

/**
 * POST /api/admin/upload_image
 * Subir imagen
 */
router.post('/upload_image', upload.single('image'), async (req, res) => {
  try {
    if (!req.file) {
      return res.status(400).json({
        success: false,
        message: 'No se proporcionó ninguna imagen'
      });
    }
    
    // Mover archivo a ubicación permanente
    const uploadsDir = path.join(__dirname, '../../uploads');
    await fs.mkdir(uploadsDir, { recursive: true });
    
    const ext = path.extname(req.file.originalname);
    const newFileName = `img_${Date.now()}${ext}`;
    const newPath = path.join(uploadsDir, newFileName);
    
    await fs.rename(req.file.path, newPath);
    
    // URL relativa para el frontend
    const imageUrl = `/uploads/${newFileName}`;
    
    res.json({
      success: true,
      url: imageUrl,
      filename: newFileName
    });
  } catch (error) {
    console.error('Error al subir imagen:', error);
    res.status(500).json({
      success: false,
      message: 'Error al subir imagen'
    });
  }
});

export default router;

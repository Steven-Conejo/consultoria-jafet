/**
 * API de Subida de Archivos
 */

import express from 'express';
import multer from 'multer';
import path from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs/promises';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const router = express.Router();

// Configurar multer
const upload = multer({
  dest: path.join(__dirname, '../../uploads'),
  limits: { fileSize: 10 * 1024 * 1024 }, // 10MB
  fileFilter: (req, file, cb) => {
    // Permitir varios tipos de archivos
    const allowedTypes = /jpeg|jpg|png|gif|webp|pdf|doc|docx|txt/;
    const extname = allowedTypes.test(path.extname(file.originalname).toLowerCase());
    const mimetype = allowedTypes.test(file.mimetype);
    
    if (mimetype && extname) {
      return cb(null, true);
    }
    cb(new Error('Tipo de archivo no permitido'));
  }
});

/**
 * POST /api/upload
 * Subir archivo
 */
router.post('/', upload.single('file'), async (req, res) => {
  try {
    if (!req.file) {
      return res.status(400).json({
        success: false,
        message: 'No se proporcionó ningún archivo'
      });
    }
    
    // Mover archivo a ubicación permanente
    const uploadsDir = path.join(__dirname, '../../uploads');
    await fs.mkdir(uploadsDir, { recursive: true });
    
    const ext = path.extname(req.file.originalname);
    const newFileName = `file_${Date.now()}${ext}`;
    const newPath = path.join(uploadsDir, newFileName);
    
    await fs.rename(req.file.path, newPath);
    
    const fileUrl = `/uploads/${newFileName}`;
    
    res.json({
      success: true,
      url: fileUrl,
      filename: newFileName,
      originalName: req.file.originalname
    });
  } catch (error) {
    console.error('Error al subir archivo:', error);
    res.status(500).json({
      success: false,
      message: 'Error al subir archivo'
    });
  }
});

export default router;

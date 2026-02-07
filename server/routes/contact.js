/**
 * API de Contacto
 */

import express from 'express';
import multer from 'multer';
import { sendEmail } from '../utils/mailer.js';
import { getSMTPConfig } from '../config/smtp.js';
import fs from 'fs/promises';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const router = express.Router();

// Configurar multer para manejar archivos adjuntos
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

function sanitizeInput(data) {
  if (typeof data !== 'string') return data;
  return data.trim().replace(/[<>]/g, '');
}

function validateEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function validatePhone(phone) {
  if (!phone) return true;
  return /^[\+]?[0-9\s\-\(\)]{10,}$/.test(phone);
}

// Crear directorio de logs si no existe
const logDir = path.join(__dirname, '../../logs');
fs.mkdir(logDir, { recursive: true }).catch(() => {});

/**
 * POST /api/contact
 * Envía un mensaje de contacto
 * Acepta tanto JSON como FormData (multipart/form-data)
 */
router.post('/', (req, res, next) => {
  // Solo usar multer si el Content-Type es multipart/form-data
  if (req.is('multipart/form-data')) {
    return upload.single('file')(req, res, next);
  }
  next();
}, async (req, res) => {
  try {
    // Manejar tanto JSON como FormData
    let nombre, email, telefono, asunto, mensaje;
    
    if (req.is('multipart/form-data')) {
      // FormData desde el frontend (campos en inglés)
      nombre = req.body.name || req.body.nombre;
      email = req.body.email;
      telefono = req.body.phone || req.body.telefono;
      asunto = req.body.subject || req.body.asunto;
      mensaje = req.body.message || req.body.mensaje;
    } else {
      // JSON (compatibilidad hacia atrás - campos en español)
      nombre = req.body.nombre;
      email = req.body.email;
      telefono = req.body.telefono;
      asunto = req.body.asunto;
      mensaje = req.body.mensaje;
    }
    
    // Validaciones
    if (!nombre || !email || !asunto || !mensaje) {
      return res.status(400).json({
        success: false,
        message: 'Todos los campos requeridos deben ser completados'
      });
    }
    
    if (!validateEmail(email)) {
      return res.status(400).json({
        success: false,
        message: 'El email proporcionado no es válido'
      });
    }
    
    if (telefono && !validatePhone(telefono)) {
      return res.status(400).json({
        success: false,
        message: 'El teléfono proporcionado no es válido'
      });
    }
    
    const smtpConfig = await getSMTPConfig();
    const recipients = [
      smtpConfig.to_email,
      'steven@staygold.cr'
    ];
    
    // Información del archivo adjunto si existe
    const fileInfo = req.file ? `<p><strong>Archivo adjunto:</strong> ${sanitizeInput(req.file.originalname)} (${(req.file.size / 1024).toFixed(2)} KB)</p>` : '';
    
    // Mensaje HTML para los administradores
    const adminMessage = `
      <h2>Nuevo Mensaje de Contacto</h2>
      <p><strong>Nombre:</strong> ${sanitizeInput(nombre)}</p>
      <p><strong>Email:</strong> ${sanitizeInput(email)}</p>
      ${telefono ? `<p><strong>Teléfono:</strong> ${sanitizeInput(telefono)}</p>` : ''}
      <p><strong>Asunto:</strong> ${sanitizeInput(asunto)}</p>
      ${fileInfo}
      <p><strong>Mensaje:</strong></p>
      <p>${sanitizeInput(mensaje).replace(/\n/g, '<br>')}</p>
      <hr>
      <p><small>Fecha: ${new Date().toLocaleString('es-ES')}</small></p>
    `;
    
    // Enviar a administradores
    let emailSent = false;
    try {
      await sendEmail({
        to: recipients,
        subject: `Nuevo Contacto: ${sanitizeInput(asunto)}`,
        message: adminMessage,
        html: true,
        replyTo: email
      });
      emailSent = true;
    } catch (emailError) {
      console.error('Error al enviar email a administradores:', emailError);
    }
    
    // Mensaje de confirmación al cliente
    const confirmationMessage = `
      <h2>Mensaje Recibido</h2>
      <p>Hola ${sanitizeInput(nombre)},</p>
      <p>Hemos recibido tu mensaje correctamente. Nuestro equipo revisará tu consulta y te contactará a la brevedad posible.</p>
      <h3>Detalles de tu mensaje:</h3>
      <p><strong>Asunto:</strong> ${sanitizeInput(asunto)}</p>
      <p><strong>Fecha:</strong> ${new Date().toLocaleString('es-ES')}</p>
      <p>Gracias por contactarnos.</p>
      <p>Atentamente,<br>Equipo LegisAudit</p>
    `;
    
    // Enviar confirmación al cliente
    try {
      await sendEmail({
        to: email,
        subject: 'Mensaje Recibido - LegisAudit',
        message: confirmationMessage,
        html: true,
        replyTo: smtpConfig.from_email
      });
    } catch (confirmError) {
      console.error('Error al enviar confirmación al cliente:', confirmError);
    }
    
    // Log del contacto
    const logEntry = `${new Date().toISOString()} | ${nombre} | ${email} | ${asunto}\n`;
    try {
      await fs.appendFile(path.join(logDir, 'contact.log'), logEntry);
    } catch (logError) {
      console.error('Error al escribir log:', logError);
    }
    
    // Limpiar archivo temporal si existe
    if (req.file) {
      try {
        await fs.unlink(req.file.path);
      } catch (unlinkError) {
        console.error('Error al eliminar archivo temporal:', unlinkError);
      }
    }
    
    res.json({
      success: true,
      message: 'Mensaje enviado correctamente'
    });
    
  } catch (error) {
    console.error('Error en contacto:', error);
    
    // Limpiar archivo temporal en caso de error
    if (req.file) {
      try {
        await fs.unlink(req.file.path);
      } catch (unlinkError) {
        console.error('Error al eliminar archivo temporal:', unlinkError);
      }
    }
    
    // Manejar errores específicos de multer
    if (error instanceof multer.MulterError) {
      if (error.code === 'LIMIT_FILE_SIZE') {
        return res.status(400).json({
          success: false,
          message: 'El archivo es demasiado grande. El tamaño máximo es 10MB.'
        });
      }
      return res.status(400).json({
        success: false,
        message: error.message || 'Error al procesar el archivo'
      });
    }
    
    res.status(500).json({
      success: false,
      message: error.message || 'Error al procesar el mensaje'
    });
  }
});

export default router;

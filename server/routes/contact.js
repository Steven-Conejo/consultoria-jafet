/**
 * API de Contacto
 */

import express from 'express';
import { sendEmail } from '../utils/mailer.js';
import { getSMTPConfig } from '../config/smtp.js';
import fs from 'fs/promises';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const router = express.Router();

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
 */
router.post('/', async (req, res) => {
  try {
    const { nombre, email, telefono, asunto, mensaje, archivo } = req.body;
    
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
    
    // Mensaje HTML para los administradores
    const adminMessage = `
      <h2>Nuevo Mensaje de Contacto</h2>
      <p><strong>Nombre:</strong> ${sanitizeInput(nombre)}</p>
      <p><strong>Email:</strong> ${sanitizeInput(email)}</p>
      ${telefono ? `<p><strong>Teléfono:</strong> ${sanitizeInput(telefono)}</p>` : ''}
      <p><strong>Asunto:</strong> ${sanitizeInput(asunto)}</p>
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
    
    res.json({
      success: true,
      message: 'Mensaje enviado correctamente'
    });
    
  } catch (error) {
    console.error('Error en contacto:', error);
    res.status(500).json({
      success: false,
      message: 'Error al procesar el mensaje'
    });
  }
});

export default router;

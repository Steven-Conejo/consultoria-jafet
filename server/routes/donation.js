/**
 * API de Donaciones
 */

import express from 'express';
import fs from 'fs/promises';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const router = express.Router();

const logDir = path.join(__dirname, '../../logs');
fs.mkdir(logDir, { recursive: true }).catch(() => {});

function sanitizeInput(data) {
  if (typeof data !== 'string') return data;
  return data.trim().replace(/[<>]/g, '');
}

/**
 * POST /api/donation
 * Registrar donación
 */
router.post('/', async (req, res) => {
  try {
    const { amount, currency } = req.body;
    
    // Validaciones
    const amountNum = parseFloat(amount);
    if (!amountNum || amountNum <= 0) {
      return res.status(400).json({
        success: false,
        message: 'El monto debe ser mayor a cero'
      });
    }
    
    if (amountNum > 10000) {
      return res.status(400).json({
        success: false,
        message: 'El monto máximo permitido es $10,000'
      });
    }
    
    const validCurrencies = ['USD', 'EUR', 'MXN'];
    if (!currency || !validCurrencies.includes(currency)) {
      return res.status(400).json({
        success: false,
        message: 'Moneda no soportada'
      });
    }
    
    // Log de la donación
    const logEntry = `${new Date().toISOString()} | ${amountNum} ${currency} | IP: ${req.ip}\n`;
    try {
      await fs.appendFile(path.join(logDir, 'donations.log'), logEntry);
    } catch (logError) {
      console.error('Error al escribir log:', logError);
    }
    
    res.json({
      success: true,
      message: 'Gracias por tu donación. En un entorno de producción, aquí se procesaría el pago.',
      amount: amountNum,
      currency: currency
    });
  } catch (error) {
    console.error('Error en donación:', error);
    res.status(500).json({
      success: false,
      message: 'Error al procesar la donación'
    });
  }
});

export default router;

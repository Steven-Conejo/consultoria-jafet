/**
 * Configuración SMTP
 * LegisAudit - Consultoría Jafet
 */

import { config } from 'dotenv';

config();

/**
 * Obtiene la configuración SMTP
 */
export async function getSMTPConfig() {
  // Intentar cargar desde variables de entorno primero
  if (process.env.SMTP_HOST) {
    return {
      host: process.env.SMTP_HOST,
      port: parseInt(process.env.SMTP_PORT || '587'),
      username: process.env.SMTP_USER,
      password: process.env.SMTP_PASSWORD,
      from_email: process.env.SMTP_FROM_EMAIL,
      from_name: process.env.SMTP_FROM_NAME || 'LegisAudit - Plataforma Legal',
      to_email: process.env.SMTP_TO_EMAIL,
      encryption: process.env.SMTP_ENCRYPTION || 'tls',
      timeout: parseInt(process.env.SMTP_TIMEOUT || '30'),
      use_native_mail: process.env.SMTP_USE_NATIVE === 'true'
    };
  }
  
  // Fallback a archivo de configuración (si existe como .js)
  try {
    const smtpConfigModule = await import('../api/smtp_config.js');
    return smtpConfigModule.default || smtpConfigModule;
  } catch (error) {
    // Si no existe, usar valores por defecto
    return {
      host: 'mail.legisaudit-abogados.cu.ma',
      port: 587,
      username: 'servicioprofesionalabogadojcgy@legisaudit-abogados.cu.ma',
      password: 'MasterYDario2803',
      from_email: 'servicioprofesionalabogadojcgy@legisaudit-abogados.cu.ma',
      from_name: 'LegisAudit - Plataforma Legal',
      to_email: 'sevicioprofesionalabogadojcgy@gmail.com',
      encryption: 'tls',
      timeout: 30,
      use_native_mail: false
    };
  }
}

export default { getSMTPConfig };

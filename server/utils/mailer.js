/**
 * Mailer para Node.js
 * Soporta SMTP (nodemailer) y fallback a sendmail
 */

import nodemailer from 'nodemailer';
import path from 'path';
import { getSMTPConfig } from '../config/smtp.js';

/**
 * Envía un correo usando SMTP o sendmail como fallback
 */
export async function sendEmail({
  to,
  subject,
  message,
  html = true,
  replyTo = null,
  attachmentPath = null,
  attachmentName = null
}) {
  const smtpConfig = await getSMTPConfig();
  
  // Si use_native_mail está activado, usar sendmail
  if (smtpConfig.use_native_mail) {
    return sendWithSendmail({ to, subject, message, html, replyTo, attachmentPath, attachmentName, smtpConfig });
  }
  
  // Intentar SMTP primero
  try {
    return await sendWithSMTP({ to, subject, message, html, replyTo, attachmentPath, attachmentName, smtpConfig });
  } catch (smtpError) {
    console.warn('SMTP falló, intentando con sendmail:', smtpError.message);
    // Fallback a sendmail
    return sendWithSendmail({ to, subject, message, html, replyTo, attachmentPath, attachmentName, smtpConfig });
  }
}

/**
 * Envía correo usando SMTP (nodemailer)
 */
async function sendWithSMTP({ to, subject, message, html, replyTo, attachmentPath, attachmentName, smtpConfig }) {
  const transporter = nodemailer.createTransport({
    host: smtpConfig.host,
    port: smtpConfig.port,
    secure: smtpConfig.encryption === 'ssl',
    auth: {
      user: smtpConfig.username,
      pass: smtpConfig.password
    },
    tls: {
      rejectUnauthorized: false
    }
  });
  
  const mailOptions = {
    from: `"${smtpConfig.from_name}" <${smtpConfig.from_email}>`,
    to: Array.isArray(to) ? to.join(', ') : to,
    subject: subject,
    html: html ? message : undefined,
    text: html ? undefined : message,
    replyTo: replyTo || smtpConfig.from_email,
    headers: {
      'X-Mailer': 'LegisAudit/1.0',
      'X-Auto-Response-Suppress': 'All',
      'Importance': 'Normal',
      'Return-Path': `<${smtpConfig.from_email}>`
    }
  };
  
  if (attachmentPath) {
    mailOptions.attachments = [{
      filename: attachmentName || path.basename(attachmentPath),
      path: attachmentPath
    }];
  }
  
  const info = await transporter.sendMail(mailOptions);
  return { success: true, messageId: info.messageId };
}

/**
 * Envía correo usando sendmail (fallback)
 */
async function sendWithSendmail({ to, subject, message, html, replyTo, attachmentPath, attachmentName, smtpConfig }) {
  // Usar nodemailer con sendmail transport
  const transporter = nodemailer.createTransport({
    sendmail: true,
    newline: 'unix',
    path: '/usr/sbin/sendmail'
  });
  
  const mailOptions = {
    from: `"${smtpConfig.from_name}" <${smtpConfig.from_email}>`,
    to: Array.isArray(to) ? to.join(', ') : to,
    subject: subject,
    html: html ? message : undefined,
    text: html ? undefined : message,
    replyTo: replyTo || smtpConfig.from_email,
    headers: {
      'X-Mailer': 'LegisAudit/1.0',
      'X-Auto-Response-Suppress': 'All',
      'Importance': 'Normal',
      'Return-Path': `<${smtpConfig.from_email}>`,
      'Message-ID': `<${Buffer.from(Date.now().toString()).toString('base64')}@legisaudit-abogados.cu.ma>`
    }
  };
  
  if (attachmentPath) {
    mailOptions.attachments = [{
      filename: attachmentName || path.basename(attachmentPath),
      path: attachmentPath
    }];
  }
  
  const info = await transporter.sendMail(mailOptions);
  return { success: true, messageId: info.messageId };
}

export default { sendEmail };

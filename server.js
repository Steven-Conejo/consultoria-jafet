/**
 * Servidor Principal - LegisAudit
 * Servidor Express.js que sirve el frontend Vue.js y las APIs
 */

import express from 'express';
import path from 'path';
import { fileURLToPath } from 'url';
import session from 'express-session';
import cors from 'cors';
import { config } from 'dotenv';

// Importar rutas
import contactRoutes from './server/routes/contact.js';
import authRoutes from './server/routes/auth.js';
import blogRoutes from './server/routes/blog.js';
import adminRoutes from './server/routes/admin.js';
import donationRoutes from './server/routes/donation.js';
import uploadRoutes from './server/routes/upload.js';

// Cargar variables de entorno
config();

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors({
  origin: process.env.CORS_ORIGIN || '*',
  credentials: true
}));

app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true, limit: '10mb' }));

// Configurar sesiones
app.use(session({
  secret: process.env.SESSION_SECRET || 'legisaudit-secret-key-change-in-production',
  resave: false,
  saveUninitialized: false,
  cookie: {
    secure: process.env.NODE_ENV === 'production',
    httpOnly: true,
    maxAge: 24 * 60 * 60 * 1000 // 24 horas
  }
}));

// Servir archivos estáticos del frontend compilado
const distPath = path.join(__dirname, 'dist');
app.use(express.static(distPath));

// APIs
app.use('/api/contact', contactRoutes);
app.use('/api/admin/auth', authRoutes);
app.use('/api/blog', blogRoutes);
app.use('/api/admin', adminRoutes);
app.use('/api/donation', donationRoutes);
app.use('/api/upload', uploadRoutes);

// Health check
app.get('/api/health', (req, res) => {
  res.json({ status: 'ok', timestamp: new Date().toISOString() });
});

// SPA fallback - todas las rutas no-API sirven index.html
app.get('*', (req, res) => {
  // No servir index.html para rutas de API
  if (req.path.startsWith('/api/')) {
    return res.status(404).json({ error: 'API endpoint not found' });
  }
  res.sendFile(path.join(distPath, 'index.html'));
});

// Manejo de errores
app.use((err, req, res, next) => {
  console.error('Error:', err);
  res.status(err.status || 500).json({
    success: false,
    message: err.message || 'Error interno del servidor'
  });
});

// Iniciar servidor
app.listen(PORT, () => {
  console.log(`🚀 Servidor LegisAudit corriendo en http://localhost:${PORT}`);
  console.log(`📁 Frontend servido desde: ${distPath}`);
  console.log(`🌍 Entorno: ${process.env.NODE_ENV || 'development'}`);
});

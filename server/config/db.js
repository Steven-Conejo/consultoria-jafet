/**
 * Configuración de Base de Datos
 * LegisAudit - Consultoría Jafet
 */

import mysql from 'mysql2/promise';
import { config } from 'dotenv';

config();

let pool = null;

/**
 * Obtiene la configuración de la base de datos
 */
async function getDbConfig() {
  // Intentar cargar desde variables de entorno primero
  if (process.env.DB_HOST) {
    return {
      host: process.env.DB_HOST,
      dbname: process.env.DB_NAME,
      username: process.env.DB_USER,
      password: process.env.DB_PASSWORD,
      charset: process.env.DB_CHARSET || 'utf8mb4'
    };
  }
  
  // Fallback a archivo de configuración (si existe como .js)
  try {
    const dbConfigModule = await import('../api/db_config.js');
    return dbConfigModule.default || dbConfigModule;
  } catch (error) {
    // Si no existe, usar valores por defecto
    return {
      host: 'localhost',
      dbname: 'zssrpjcp_consultoria_jafet',
      username: 'zssrpjcp_Dario',
      password: 'MasterYDario',
      charset: 'utf8mb4'
    };
  }
}

/**
 * Obtiene el pool de conexiones a la base de datos
 */
export async function getDb() {
  if (!pool) {
    const dbConfig = await getDbConfig();
    
    pool = mysql.createPool({
      host: dbConfig.host,
      database: dbConfig.dbname,
      user: dbConfig.username,
      password: dbConfig.password,
      charset: dbConfig.charset,
      waitForConnections: true,
      connectionLimit: 10,
      queueLimit: 0,
      enableKeepAlive: true,
      keepAliveInitialDelay: 0
    });
    
    // Probar la conexión
    try {
      const connection = await pool.getConnection();
      console.log('✅ Conexión a base de datos establecida');
      connection.release();
    } catch (error) {
      console.error('❌ Error al conectar con la base de datos:', error.message);
      throw new Error('Error al conectar con la base de datos');
    }
  }
  
  return pool;
}

/**
 * Cierra el pool de conexiones
 */
export async function closeDb() {
  if (pool) {
    await pool.end();
    pool = null;
  }
}

export default { getDb, closeDb, getDbConfig };

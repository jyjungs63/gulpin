import { readFile } from 'node:fs/promises';
import { openDatabase, reportDatabaseError } from './db.js';

if (!process.env.DATABASE_URL) {
  console.error('Set DATABASE_URL in .env before running the migration.');
  process.exit(1);
}

const pool = openDatabase(process.env.DATABASE_URL);
try {
  const schema = await readFile(new URL('../db/schema.sql', import.meta.url), 'utf8');
  await pool.query(`BEGIN;
    SELECT pg_advisory_xact_lock(807214609);
    ${schema}
    COMMIT;`);
  console.log('PostgreSQL schema is ready.');
} catch (error) {
  if (error.code === '28P01') {
    reportDatabaseError('DB 초기화 실패', error);
  } else {
    console.error('Migration failed:', error.code ?? error.name, error.message);
  }
  process.exitCode = 1;
} finally {
  await pool.end();
}

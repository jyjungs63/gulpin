import pg from 'pg';

export function openDatabase(connectionString) {
  const pool = new pg.Pool({ connectionString, connectionTimeoutMillis: 3000 });
  pool.on('error', (error) => reportDatabaseError('PostgreSQL 연결 오류', error));
  return pool;
}

export function reportDatabaseError(context, error) {
  if (error.code === '28P01') {
    console.error(`${context} [28P01] PostgreSQL 인증 실패. .env의 DATABASE_URL에서 DB 계정과 비밀번호를 확인하세요.`);
    return;
  }
  console.error(`${context} [${error.code ?? error.name}]`);
}

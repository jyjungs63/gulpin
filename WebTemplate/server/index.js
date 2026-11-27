import { randomBytes } from 'node:crypto';
import { fileURLToPath } from 'node:url';
import express from 'express';
import { lessons } from '../public/lessons.js';
import { hashPassword, verifyPassword, tokenHash, readSessionToken, limitAccountRequests } from './auth.js';
import { openDatabase, reportDatabaseError } from './db.js';

const app = express();
const host = process.env.HOST || '127.0.0.1';
const port = Number(process.env.PORT || 3000);
const pool = process.env.DATABASE_URL ? openDatabase(process.env.DATABASE_URL) : null;
const sessionDays = 7;
const secure = process.env.COOKIE_SECURE === 'true' || process.env.NODE_ENV === 'production';
const cookieName = secure ? '__Host-dotori_session' : 'dotori_session';
const cookieOptions = { httpOnly: true, sameSite: 'strict', secure, path: '/' };
const publicFolder = fileURLToPath(new URL('../public/', import.meta.url));
const noDatabase = '로그인 서버에 연결할 수 없어요. 잠시 후 다시 시도해 주세요.';

app.disable('x-powered-by');
app.use((_req, res, next) => {
  res.set({
    'Content-Security-Policy': "default-src 'self'; object-src 'none'; base-uri 'none'; frame-ancestors 'none'; form-action 'self'",
    'X-Content-Type-Options': 'nosniff',
    'Referrer-Policy': 'no-referrer'
  });
  next();
});

const api = express.Router();
app.use('/api', api);
api.use((req, res, next) => {
  res.set('Cache-Control', 'no-store');
  if (req.method === 'POST' && (!req.is('application/json') || req.get('sec-fetch-site') === 'cross-site')) {
    return res.status(415).json({ error: '지원하지 않는 요청 형식이에요.' });
  }
  next();
});
api.use(express.json({ limit: '4kb' }));

async function currentAccount(req) {
  const token = readSessionToken(req, cookieName);
  if (!pool || !token) return null;
  const { rows } = await pool.query(
    `SELECT guardian_accounts.id, guardian_accounts.email
     FROM guardian_sessions JOIN guardian_accounts ON guardian_id = guardian_accounts.id
     WHERE token_hash = $1 AND expires_at > now()`,
    [tokenHash(token)]
  );
  return rows[0] ?? null;
}

async function accountData(account) {
  if (!account) return { account: null, completed: [] };
  const { rows } = await pool.query(
    'SELECT lesson_id FROM lesson_progress WHERE guardian_id = $1 ORDER BY completed_at DESC',
    [account.id]
  );
  return { account: { email: account.email }, completed: rows.map((row) => row.lesson_id) };
}

async function issueSession(req, res, account) {
  const token = randomBytes(32).toString('hex');
  const previous = readSessionToken(req, cookieName);
  const data = await accountData(account);
  await pool.query(
    `WITH cleanup AS (
        DELETE FROM guardian_sessions WHERE token_hash = $4 OR expires_at <= now()
     )
     INSERT INTO guardian_sessions (token_hash, guardian_id, expires_at)
     VALUES ($1, $2, now() + $3 * interval '1 day')`,
    [tokenHash(token), account.id, sessionDays, previous ? tokenHash(previous) : null]
  );
  res.cookie(cookieName, token, { ...cookieOptions, maxAge: sessionDays * 86_400_000 }).json(data);
}

function credentials(req) {
  const { email, password } = req.body ?? {};
  if (typeof email !== 'string' || typeof password !== 'string') return null;
  const normalized = email.trim().toLowerCase();
  if (normalized.length > 254 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(normalized)) return null;
  if (password.length < 1 || password.length > 128) return null;
  return { email: normalized, password };
}

api.get('/session', async (req, res) => res.json(await accountData(await currentAccount(req))));

api.post('/register', limitAccountRequests, async (req, res) => {
  if (!pool) return res.status(503).json({ error: noDatabase });
  const input = credentials(req);
  if (!input || input.password.length < 10) {
    return res.status(400).json({ error: '올바른 이메일과 10자 이상의 비밀번호를 입력해 주세요.' });
  }
  const { rows } = await pool.query(
    `INSERT INTO guardian_accounts (email, password_hash) VALUES ($1, $2)
     ON CONFLICT (email) DO NOTHING RETURNING id, email`,
    [input.email, await hashPassword(input.password)]
  );
  if (!rows.length) return res.status(409).json({ error: '이미 등록된 이메일이에요. 로그인해 주세요.' });
  await issueSession(req, res, rows[0]);
});

api.post('/login', limitAccountRequests, async (req, res) => {
  if (!pool) return res.status(503).json({ error: noDatabase });
  const input = credentials(req);
  const invalid = { error: '이메일과 비밀번호를 다시 확인해 주세요.' };
  if (!input) return res.status(401).json(invalid);
  const { rows: [account] } = await pool.query(
    'SELECT id, email, password_hash FROM guardian_accounts WHERE email = $1',
    [input.email]
  );
  if (!await verifyPassword(input.password, account?.password_hash) || !account) {
    return res.status(401).json(invalid);
  }
  await issueSession(req, res, account);
});

api.post('/logout', async (req, res) => {
  const token = readSessionToken(req, cookieName);
  if (pool && token) await pool.query('DELETE FROM guardian_sessions WHERE token_hash = $1', [tokenHash(token)]);
  res.clearCookie(cookieName, cookieOptions).json({ account: null, completed: [] });
});

api.post('/progress', async (req, res) => {
  const account = await currentAccount(req);
  if (!account) return res.status(401).json({ error: '보호자 로그인 후 기록을 저장해 주세요.' });
  const { lessonId, answers } = req.body ?? {};
  const lesson = lessons.find((item) => item.id === lessonId);
  const correct = lesson && Array.isArray(answers) && answers.length === lesson.questions.length
    && lesson.questions.every((question, index) => answers[index] === question.answer);
  if (!correct) return res.status(400).json({ error: '활동의 모든 문제를 풀어 주세요.' });
  await pool.query(
    `INSERT INTO lesson_progress (guardian_id, lesson_id) VALUES ($1, $2)
     ON CONFLICT (guardian_id, lesson_id) DO NOTHING`,
    [account.id, lessonId]
  );
  res.json(await accountData(account));
});

api.use((_req, res) => res.status(404).json({ error: '요청한 기능을 찾을 수 없어요.' }));
api.use((error, _req, res, _next) => {
  if (error.status === 400 || error.status === 413) {
    return res.status(error.status).json({ error: '입력 형식이나 길이를 다시 확인해 주세요.' });
  }
  reportDatabaseError('API 오류', error);
  res.status(503).json({ error: noDatabase });
});

app.use(express.static(publicFolder));

const server = app.listen(port, host, (error) => {
  if (error) {
    console.error(`Could not listen on ${host}:${port} (${error.code ?? error.name}).`);
    process.exitCode = 1;
    return;
  }
  console.log(`도토리 배움터 · http://${host}:${server.address().port}`);
  if (!pool) console.log('DATABASE_URL is unset. Public activities are available.');
});

for (const signal of ['SIGINT', 'SIGTERM']) {
  process.once(signal, () => server.close(async () => {
    if (pool) await pool.end();
  }));
}

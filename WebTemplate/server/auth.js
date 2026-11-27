import { createHash, randomBytes, scrypt, timingSafeEqual } from 'node:crypto';
import { promisify } from 'node:util';

const deriveKey = promisify(scrypt);
const placeholder = `${'0'.repeat(32)}:${'0'.repeat(128)}`;
const windowMs = 60_000;
const attempts = new Map();

export async function hashPassword(password) {
  const salt = randomBytes(16).toString('hex');
  const key = await deriveKey(password, salt, 64);
  return `${salt}:${key.toString('hex')}`;
}

export async function verifyPassword(password, stored = placeholder) {
  const [salt, key] = stored.split(':');
  const actual = await deriveKey(password, salt, 64);
  return timingSafeEqual(actual, Buffer.from(key, 'hex'));
}

export function tokenHash(token) {
  return createHash('sha256').update(token).digest('hex');
}

export function readSessionToken(req, cookieName) {
  const prefix = `${cookieName}=`;
  const token = req.headers.cookie?.split(';').map((part) => part.trim())
    .find((part) => part.startsWith(prefix))?.slice(prefix.length);
  return /^[a-f0-9]{64}$/.test(token ?? '') ? token : null;
}

export function limitAccountRequests(req, res, next) {
  const bucket = Math.floor(Date.now() / windowMs);
  if (bucket !== limitAccountRequests.bucket) {
    attempts.clear();
    limitAccountRequests.bucket = bucket;
  }
  const count = attempts.get(req.ip) ?? 0;
  if (count >= 15 || (count === 0 && attempts.size >= 10_000)) {
    return res.set('Retry-After', '60').status(429).json({ error: '요청이 많아요. 잠시 후 다시 시도해 주세요.' });
  }
  attempts.set(req.ip, count + 1);
  next();
}

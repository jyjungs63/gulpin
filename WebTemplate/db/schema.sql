CREATE TABLE IF NOT EXISTS guardian_accounts (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    email text NOT NULL UNIQUE,
    password_hash text NOT NULL,
    created_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS guardian_sessions (
    token_hash text PRIMARY KEY,
    guardian_id bigint NOT NULL REFERENCES guardian_accounts (id) ON DELETE CASCADE,
    expires_at timestamptz NOT NULL
);

CREATE INDEX IF NOT EXISTS guardian_sessions_expiry_idx ON guardian_sessions (expires_at);

CREATE TABLE IF NOT EXISTS lesson_progress (
    guardian_id bigint NOT NULL REFERENCES guardian_accounts (id) ON DELETE CASCADE,
    lesson_id text NOT NULL,
    completed_at timestamptz NOT NULL DEFAULT now(),
    PRIMARY KEY (guardian_id, lesson_id)
);

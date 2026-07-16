CREATE TABLE IF NOT EXISTS chaitalk_user (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    owner TEXT DEFAULT '',
    password TEXT NOT NULL,
    mobile TEXT DEFAULT '',
    p_mobile TEXT DEFAULT '',
    phone TEXT DEFAULT '',
    addr TEXT DEFAULT '',
    zipcode TEXT DEFAULT '',
    role INTEGER NOT NULL DEFAULT 0,
    confirm INTEGER NOT NULL DEFAULT 1,
    status INTEGER NOT NULL DEFAULT 1,
    step TEXT DEFAULT '',
    etc TEXT DEFAULT '',
    tid TEXT DEFAULT '',
    mid TEXT DEFAULT '',
    classnm TEXT DEFAULT '',
    logstat INTEGER NOT NULL DEFAULT 0,
    logdate TEXT,
    rdate TEXT NOT NULL DEFAULT (datetime('now', 'localtime'))
);

CREATE INDEX IF NOT EXISTS idx_chaitalk_user_login
    ON chaitalk_user (id, status, confirm);

CREATE TABLE IF NOT EXISTS visitors (
    num INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id TEXT NOT NULL,
    name TEXT DEFAULT '',
    visit_time TEXT NOT NULL,
    ip TEXT DEFAULT '',
    uri TEXT DEFAULT '',
    user_agent TEXT DEFAULT '',
    referer TEXT DEFAULT '',
    country TEXT DEFAULT '',
    role INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS chaitalk_addrlist (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    owner TEXT DEFAULT '',
    password TEXT DEFAULT '',
    mobile TEXT DEFAULT '',
    phone TEXT DEFAULT '',
    addr TEXT DEFAULT '',
    zipcode TEXT DEFAULT '',
    rdate TEXT DEFAULT (datetime('now', 'localtime')),
    mid TEXT DEFAULT '',
    etc TEXT DEFAULT ''
);

CREATE TABLE IF NOT EXISTS chaitalk_parcel (
    id TEXT DEFAULT '',
    name TEXT DEFAULT '',
    date TEXT DEFAULT (datetime('now', 'localtime')),
    price INTEGER DEFAULT 0,
    UNIQUE (id, date)
);

CREATE TABLE IF NOT EXISTS chaitalk_porlist (
    por_id TEXT PRIMARY KEY,
    id TEXT NOT NULL,
    "order" TEXT DEFAULT '',
    addr TEXT DEFAULT '',
    mobile TEXT DEFAULT '',
    por_list TEXT NOT NULL,
    rdate TEXT NOT NULL DEFAULT (datetime('now', 'localtime')),
    confirm INTEGER DEFAULT 0,
    pdfname TEXT DEFAULT '',
    zip TEXT DEFAULT '',
    invoice TEXT DEFAULT '',
    refund TEXT DEFAULT ''
);

CREATE INDEX IF NOT EXISTS idx_chaitalk_porlist_id
    ON chaitalk_porlist (id);

CREATE INDEX IF NOT EXISTS idx_chaitalk_porlist_rdate
    ON chaitalk_porlist (rdate);

CREATE TABLE IF NOT EXISTS chaitalk_price (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    price TEXT DEFAULT '',
    rdate TEXT NOT NULL DEFAULT (datetime('now', 'localtime'))
);

CREATE TABLE IF NOT EXISTS chaitalk_repository (
    num INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    id TEXT NOT NULL,
    contents TEXT NOT NULL,
    rdate TEXT NOT NULL DEFAULT (datetime('now', 'localtime')),
    "desc" TEXT DEFAULT '',
    cate TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS study_record (
    id TEXT NOT NULL,
    step TEXT NOT NULL,
    volume TEXT NOT NULL,
    uid TEXT NOT NULL,
    rdate TEXT NOT NULL DEFAULT (datetime('now', 'localtime')),
    no INTEGER PRIMARY KEY AUTOINCREMENT
);

CREATE INDEX IF NOT EXISTS idx_study_record_id_rdate
    ON study_record (id, rdate);

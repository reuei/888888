CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    nickname TEXT DEFAULT '',
    email TEXT DEFAULT '',
    role TEXT DEFAULT 'subscriber',
    status INTEGER DEFAULT 1,
    reg_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME
);

CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    parent_id INTEGER DEFAULT 0,
    name TEXT NOT NULL,
    slug TEXT UNIQUE,
    type TEXT DEFAULT 'article',
    sort_order INTEGER DEFAULT 0,
    show_in_menu INTEGER DEFAULT 1,
    description TEXT DEFAULT ''
);

CREATE TABLE IF NOT EXISTS articles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id INTEGER DEFAULT 0,
    title TEXT NOT NULL,
    slug TEXT,
    summary TEXT DEFAULT '',
    content TEXT,
    cover_image TEXT DEFAULT '',
    author TEXT DEFAULT '',
    source TEXT DEFAULT '',
    views INTEGER DEFAULT 0,
    is_top INTEGER DEFAULT 0,
    is_hot INTEGER DEFAULT 0,
    status INTEGER DEFAULT 1,
    publish_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    create_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    update_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    `key` TEXT UNIQUE NOT NULL,
    value TEXT
);

CREATE TABLE IF NOT EXISTS messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type TEXT DEFAULT 'message',
    title TEXT DEFAULT '',
    content TEXT,
    name TEXT DEFAULT '',
    contact TEXT DEFAULT '',
    ip TEXT DEFAULT '',
    status INTEGER DEFAULT 0,
    create_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS pages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    slug TEXT UNIQUE,
    content TEXT,
    status INTEGER DEFAULT 1,
    create_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    update_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_articles_category ON articles(category_id);
CREATE INDEX IF NOT EXISTS idx_articles_status ON articles(status);
CREATE INDEX IF NOT EXISTS idx_categories_parent ON categories(parent_id);

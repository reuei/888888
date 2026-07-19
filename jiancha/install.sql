-- 人民检察 数据库结构
CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    key TEXT UNIQUE NOT NULL,
    value TEXT,
    update_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    email TEXT,
    nickname TEXT,
    role TEXT DEFAULT 'user',
    create_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT UNIQUE NOT NULL,
    description TEXT,
    parent_id INTEGER DEFAULT 0,
    sort_order INTEGER DEFAULT 0,
    show_in_menu INTEGER DEFAULT 1,
    create_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS articles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    content TEXT,
    category_id INTEGER DEFAULT 0,
    source TEXT,
    is_top INTEGER DEFAULT 0,
    status INTEGER DEFAULT 1,
    views INTEGER DEFAULT 0,
    publish_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    update_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS pages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    slug TEXT UNIQUE NOT NULL,
    content TEXT,
    status INTEGER DEFAULT 1,
    create_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS slides (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT,
    image TEXT,
    link TEXT,
    sort_order INTEGER DEFAULT 0,
    status INTEGER DEFAULT 1,
    create_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER DEFAULT 0,
    title TEXT,
    content TEXT,
    name TEXT,
    phone TEXT,
    status INTEGER DEFAULT 0,
    reply TEXT,
    create_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 默认数据
INSERT OR IGNORE INTO settings (key, value) VALUES ('site_name', '人民检察');
INSERT OR IGNORE INTO settings (key, value) VALUES ('footer_copyright', '© 2026 人民检察 版权所有');
INSERT OR IGNORE INTO settings (key, value) VALUES ('icp', '');

INSERT OR IGNORE INTO categories (name, slug, description, sort_order, show_in_menu) VALUES ('检察要闻', 'yaowen', '检察工作最新动态', 1, 1);
INSERT OR IGNORE INTO categories (name, slug, description, sort_order, show_in_menu) VALUES ('审查起诉', 'shencha', '审查逮捕与起诉信息', 2, 1);
INSERT OR IGNORE INTO categories (name, slug, description, sort_order, show_in_menu) VALUES ('公益诉讼', 'xunshi', '公益诉讼检察工作', 3, 1);
INSERT OR IGNORE INTO categories (name, slug, description, sort_order, show_in_menu) VALUES ('法律法规', 'fagui', '法律法规与司法解释', 4, 1);
INSERT OR IGNORE INTO categories (name, slug, description, sort_order, show_in_menu) VALUES ('反腐专题', 'fanfu', '检察反腐工作专题', 5, 1);
INSERT OR IGNORE INTO categories (name, slug, description, sort_order, show_in_menu) VALUES ('典型案例', 'anli', '指导性案例发布', 6, 1);
INSERT OR IGNORE INTO categories (name, slug, description, sort_order, show_in_menu) VALUES ('政策解读', 'zhengce', '检察政策解读', 7, 1);
INSERT OR IGNORE INTO categories (name, slug, description, sort_order, show_in_menu) VALUES ('专题专栏', 'zhuanti', '专题深度报道', 8, 1);
INSERT OR IGNORE INTO categories (name, slug, description, sort_order, show_in_menu) VALUES ('检察视频', 'shipin', '检察视听影像', 9, 0);
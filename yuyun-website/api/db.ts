import Database from 'better-sqlite3'
import path from 'path'
import { fileURLToPath } from 'url'
import fs from 'fs'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

const dbDir = path.join(__dirname, '..', 'data')
if (!fs.existsSync(dbDir)) {
  fs.mkdirSync(dbDir, { recursive: true })
}

const dbPath = path.join(dbDir, 'yuyun.db')
const db = new Database(dbPath)

db.exec(`
  CREATE TABLE IF NOT EXISTS admin (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
  );

  CREATE TABLE IF NOT EXISTS site_config (
    id INTEGER PRIMARY KEY CHECK (id = 1),
    logo TEXT DEFAULT '',
    site_name TEXT DEFAULT '语云科技',
    slogan TEXT DEFAULT '全球领先的云服务与数字化解决方案提供商',
    sales_phone TEXT DEFAULT '400-800-8451',
    marketing_phone TEXT DEFAULT '400-800-8541',
    address TEXT DEFAULT '中国北京市朝阳区建国路88号SOHO现代城A座1208室',
    email TEXT DEFAULT 'contact@yuyun.com',
    icp TEXT DEFAULT '京ICP备XXXXXXXX号',
    public_security_record TEXT DEFAULT '京公网安备XXXXXXXXXXX号',
    business_license TEXT DEFAULT '',
    copyright TEXT DEFAULT '语云科技® 是语云科技美国有限公司在中国的注册授权',
    qq_group TEXT DEFAULT '123456789',
    wechat_qr TEXT DEFAULT '',
    popup_enabled INTEGER DEFAULT 1,
    popup_title TEXT DEFAULT '欢迎访问语云科技',
    popup_content TEXT DEFAULT '我们提供全球领先的云服务与解决方案，立即咨询获取专属优惠。',
    popup_button_text TEXT DEFAULT '立即咨询',
    popup_button_link TEXT DEFAULT '/contact'
  );

  CREATE TABLE IF NOT EXISTS slides (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    subtitle TEXT DEFAULT '',
    button_text TEXT DEFAULT '',
    button_link TEXT DEFAULT '',
    image TEXT DEFAULT '',
    order_index INTEGER DEFAULT 0,
    enabled INTEGER DEFAULT 1
  );

  CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    category TEXT DEFAULT '',
    description TEXT DEFAULT '',
    features TEXT DEFAULT '[]',
    icon TEXT DEFAULT '',
    image TEXT DEFAULT '',
    order_index INTEGER DEFAULT 0,
    enabled INTEGER DEFAULT 1
  );

  CREATE TABLE IF NOT EXISTS partners (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    logo TEXT DEFAULT '',
    website TEXT DEFAULT '',
    order_index INTEGER DEFAULT 0,
    enabled INTEGER DEFAULT 1
  );

  CREATE TABLE IF NOT EXISTS friend_links (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    url TEXT NOT NULL,
      order_index INTEGER DEFAULT 0,
    enabled INTEGER DEFAULT 1
  );

  CREATE TABLE IF NOT EXISTS certificates (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    image TEXT DEFAULT '',
    order_index INTEGER DEFAULT 0,
    enabled INTEGER DEFAULT 1
  );

  CREATE TABLE IF NOT EXISTS testimonials (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    author TEXT NOT NULL,
    company TEXT DEFAULT '',
    content TEXT NOT NULL,
    avatar TEXT DEFAULT '',
    order_index INTEGER DEFAULT 0,
    enabled INTEGER DEFAULT 1
  );

  CREATE TABLE IF NOT EXISTS contact_messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    phone TEXT DEFAULT '',
    email TEXT DEFAULT '',
    message TEXT NOT NULL,
    read_status INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
  );

  INSERT OR IGNORE INTO admin (id, username, password_hash)
  VALUES (1, 'admin', '$2b$10$snqHFrv/C6XEFf/Tr02CEuLxyJQnby5WR.SIy.talFSBWTyf3QSTq');

  UPDATE admin SET password_hash = '$2b$10$snqHFrv/C6XEFf/Tr02CEuLxyJQnby5WR.SIy.talFSBWTyf3QSTq' WHERE id = 1;

  INSERT OR IGNORE INTO site_config (id) VALUES (1);
`)

export default db

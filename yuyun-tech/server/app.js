const express = require('express');
const path = require('path');
const fs = require('fs');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const cors = require('cors');

const app = express();
const PORT = process.env.PORT || 3000;
const JWT_SECRET = process.env.JWT_SECRET || 'yuyun-tech-secret-key-2024';
const DATA_DIR = path.join(__dirname, 'data');

app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// 静态文件托管
app.use(express.static(path.join(__dirname, '../public')));
app.use('/admin', express.static(path.join(__dirname, '../admin')));

// 确保数据目录存在
if (!fs.existsSync(DATA_DIR)) {
  fs.mkdirSync(DATA_DIR, { recursive: true });
}

// 读取JSON文件辅助函数
function readJsonFile(filename) {
  const filepath = path.join(DATA_DIR, filename);
  if (!fs.existsSync(filepath)) {
    return null;
  }
  try {
    const data = fs.readFileSync(filepath, 'utf8');
    return JSON.parse(data);
  } catch (e) {
    console.error(`读取 ${filename} 失败:`, e);
    return null;
  }
}

// 写入JSON文件辅助函数
function writeJsonFile(filename, data) {
  const filepath = path.join(DATA_DIR, filename);
  try {
    fs.writeFileSync(filepath, JSON.stringify(data, null, 2), 'utf8');
    return true;
  } catch (e) {
    console.error(`写入 ${filename} 失败:`, e);
    return false;
  }
}

// JWT验证中间件
function authMiddleware(req, res, next) {
  const authHeader = req.headers.authorization;
  if (!authHeader || !authHeader.startsWith('Bearer ')) {
    return res.status(401).json({ success: false, message: '未提供认证令牌' });
  }
  const token = authHeader.substring(7);
  try {
    const decoded = jwt.verify(token, JWT_SECRET);
    req.user = decoded;
    next();
  } catch (e) {
    return res.status(401).json({ success: false, message: '令牌无效或已过期' });
  }
}

// ===== API路由 =====

// 获取首页配置
app.get('/api/config/home', (req, res) => {
  const data = readJsonFile('home.json');
  if (data) {
    res.json({ success: true, data });
  } else {
    res.status(500).json({ success: false, message: '无法读取配置' });
  }
});

// 更新首页配置
app.post('/api/config/home', authMiddleware, (req, res) => {
  if (writeJsonFile('home.json', req.body)) {
    res.json({ success: true, message: '配置已更新' });
  } else {
    res.status(500).json({ success: false, message: '更新失败' });
  }
});

// 获取页脚配置
app.get('/api/config/footer', (req, res) => {
  const data = readJsonFile('footer.json');
  if (data) {
    res.json({ success: true, data });
  } else {
    res.status(500).json({ success: false, message: '无法读取配置' });
  }
});

// 更新页脚配置
app.post('/api/config/footer', authMiddleware, (req, res) => {
  if (writeJsonFile('footer.json', req.body)) {
    res.json({ success: true, message: '配置已更新' });
  } else {
    res.status(500).json({ success: false, message: '更新失败' });
  }
});

// 获取弹窗配置
app.get('/api/config/popup', (req, res) => {
  const data = readJsonFile('popup.json');
  if (data) {
    res.json({ success: true, data });
  } else {
    res.status(500).json({ success: false, message: '无法读取配置' });
  }
});

// 更新弹窗配置
app.post('/api/config/popup', authMiddleware, (req, res) => {
  if (writeJsonFile('popup.json', req.body)) {
    res.json({ success: true, message: '配置已更新' });
  } else {
    res.status(500).json({ success: false, message: '更新失败' });
  }
});

// 获取合作伙伴配置
app.get('/api/config/partners', (req, res) => {
  const data = readJsonFile('partners.json');
  if (data) {
    res.json({ success: true, data });
  } else {
    res.status(500).json({ success: false, message: '无法读取配置' });
  }
});

// 更新合作伙伴配置
app.post('/api/config/partners', authMiddleware, (req, res) => {
  if (writeJsonFile('partners.json', req.body)) {
    res.json({ success: true, message: '配置已更新' });
  } else {
    res.status(500).json({ success: false, message: '更新失败' });
  }
});

// 获取地图标记配置
app.get('/api/config/map', (req, res) => {
  const data = readJsonFile('map.json');
  if (data) {
    res.json({ success: true, data });
  } else {
    res.status(500).json({ success: false, message: '无法读取配置' });
  }
});

// 更新地图标记配置
app.post('/api/config/map', authMiddleware, (req, res) => {
  if (writeJsonFile('map.json', req.body)) {
    res.json({ success: true, message: '配置已更新' });
  } else {
    res.status(500).json({ success: false, message: '更新失败' });
  }
});

// 管理员登录
app.post('/api/admin/login', (req, res) => {
  const { username, password } = req.body;
  if (!username || !password) {
    return res.status(400).json({ success: false, message: '请提供用户名和密码' });
  }

  const adminData = readJsonFile('admin.json');
  if (!adminData) {
    return res.status(500).json({ success: false, message: '系统错误' });
  }

  if (username !== adminData.username) {
    return res.status(401).json({ success: false, message: '用户名或密码错误' });
  }

  const valid = bcrypt.compareSync(password, adminData.password);
  if (!valid) {
    return res.status(401).json({ success: false, message: '用户名或密码错误' });
  }

  const token = jwt.sign({ username }, JWT_SECRET, { expiresIn: '24h' });
  res.json({ success: true, token });
});

// 验证Token
app.get('/api/admin/verify', authMiddleware, (req, res) => {
  res.json({ success: true, valid: true });
});

// 修改密码
app.post('/api/admin/password', authMiddleware, (req, res) => {
  const { oldPassword, newPassword } = req.body;
  if (!oldPassword || !newPassword) {
    return res.status(400).json({ success: false, message: '请提供旧密码和新密码' });
  }

  const adminData = readJsonFile('admin.json');
  const valid = bcrypt.compareSync(oldPassword, adminData.password);
  if (!valid) {
    return res.status(401).json({ success: false, message: '旧密码错误' });
  }

  adminData.password = bcrypt.hashSync(newPassword, 10);
  if (writeJsonFile('admin.json', adminData)) {
    res.json({ success: true, message: '密码已修改' });
  } else {
    res.status(500).json({ success: false, message: '修改失败' });
  }
});

// 所有其他路由返回首页（支持前端路由）
app.get('*', (req, res) => {
  if (req.path.startsWith('/admin')) {
    res.sendFile(path.join(__dirname, '../admin/index.html'));
  } else {
    res.sendFile(path.join(__dirname, '../public/index.html'));
  }
});

app.listen(PORT, () => {
  console.log(`语云科技官网服务器运行在 http://localhost:${PORT}`);
});

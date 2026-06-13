const express = require('express');
const router = express.Router();
const fs = require('fs').promises;
const path = require('path');
const crypto = require('crypto');
const multer = require('multer');

const DATA_DIR = path.join(__dirname, '..', 'data');
const UPLOAD_DIR = path.join(__dirname, '..', '..', 'public', 'assets', 'images');

// Token存储（生产环境应使用Redis或数据库）
const activeTokens = new Map();

// 文件上传配置
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    const subDir = req.body.type || 'general';
    const dir = path.join(UPLOAD_DIR, subDir);
    fs.mkdir(dir, { recursive: true }).then(() => cb(null, dir)).catch(cb);
  },
  filename: (req, file, cb) => {
    const ext = path.extname(file.originalname);
    const name = Date.now() + '-' + Math.round(Math.random() * 1E9) + ext;
    cb(null, name);
  }
});
const upload = multer({
  storage,
  limits: { fileSize: 2 * 1024 * 1024 }, // 2MB限制
  fileFilter: (req, file, cb) => {
    const allowed = /jpeg|jpg|png|gif|svg|webp/i;
    if (allowed.test(path.extname(file.originalname))) {
      cb(null, true);
    } else {
      cb(new Error('不支持的文件格式'));
    }
  }
});

async function readJSON(filename) {
  const filePath = path.join(DATA_DIR, filename);
  const data = await fs.readFile(filePath, 'utf-8');
  return JSON.parse(data);
}

async function writeJSON(filename, data) {
  const filePath = path.join(DATA_DIR, filename);
  await fs.writeFile(filePath, JSON.stringify(data, null, 2), 'utf-8');
}

// 鉴权中间件
function authMiddleware(req, res, next) {
  const token = req.headers.authorization?.replace('Bearer ', '') || req.query.token;
  if (!token || !activeTokens.has(token)) {
    return res.status(401).json({ code: 401, error: '未授权，请先登录' });
  }
  // 检查token是否过期
  const tokenData = activeTokens.get(token);
  if (Date.now() > tokenData.expires) {
    activeTokens.delete(token);
    return res.status(401).json({ code: 401, error: '登录已过期，请重新登录' });
  }
  next();
}

// 管理员登录
router.post('/admin/login', async (req, res) => {
  try {
    const { username, password } = req.body;
    if (!username || !password) {
      return res.status(400).json({ code: 400, error: '用户名和密码不能为空' });
    }

    const authData = await readJSON('auth.json');
    if (username === authData.username && password === authData.password) {
      // 生成token（24小时有效）
      const token = crypto.randomBytes(32).toString('hex');
      activeTokens.set(token, {
        username,
        expires: Date.now() + 24 * 60 * 60 * 1000
      });

      res.json({
        code: 200,
        data: { token, expiresIn: 24 * 60 * 60 },
        message: '登录成功'
      });
    } else {
      res.status(401).json({ code: 401, error: '用户名或密码错误' });
    }
  } catch (error) {
    res.status(500).json({ code: 500, error: '登录失败' });
  }
});

// 登出
router.post('/admin/logout', authMiddleware, (req, res) => {
  const token = req.headers.authorization?.replace('Bearer ', '');
  if (token) activeTokens.delete(token);
  res.json({ code: 200, message: '已退出登录' });
});

// 更新首页轮播图配置
router.put('/admin/home/carousel', authMiddleware, async (req, res) => {
  try {
    const { carousel } = req.body;
    if (!Array.isArray(carousel)) {
      return res.status(400).json({ code: 400, error: '参数格式错误' });
    }
    const homeData = await readJSON('home.json');
    homeData.carousel = carousel;
    await writeJSON('home.json', homeData);
    res.json({ code: 200, message: '轮播图配置更新成功' });
  } catch (error) {
    res.status(500).json({ code: 500, error: '更新失败' });
  }
});

// 更新产品数据
router.put('/admin/products', authMiddleware, async (req, res) => {
  try {
    const { categories, products } = req.body;
    const productsData = await readJSON('products.json');
    if (categories) productsData.categories = categories;
    if (products && Array.isArray(products)) productsData.products = products;
    await writeJSON('products.json', productsData);
    res.json({ code: 200, message: '产品数据更新成功' });
  } catch (error) {
    res.status(500).json({ code: 500, error: '更新失败' });
  }
});

// 更新合作伙伴
router.put('/admin/partners', authMiddleware, async (req, res) => {
  try {
    const { partners } = req.body;
    if (!Array.isArray(partners)) {
      return res.status(400).json({ code: 400, error: '参数格式错误' });
    }
    await writeJSON('partners.json', { partners });
    res.json({ code: 200, message: '合作伙伴数据更新成功' });
  } catch (error) {
    res.status(500).json({ code: 500, error: '更新失败' });
  }
});

// 更新公司信息
router.put('/admin/company', authMiddleware, async (req, res) => {
  try {
    const { company } = req.body;
    if (!company || typeof company !== 'object') {
      return res.status(400).json({ code: 400, error: '参数格式错误' });
    }
    await writeJSON('company.json', company);
    res.json({ code: 200, message: '公司信息更新成功' });
  } catch (error) {
    res.status(500).json({ code: 500, error: '更新失败' });
  }
});

// 更新全局设置
router.put('/admin/settings', authMiddleware, async (req, res) => {
  try {
    const { settings } = req.body;
    if (!settings || typeof settings !== 'object') {
      return res.status(400).json({ code: 400, error: '参数格式错误' });
    }
    const currentSettings = await readJSON('settings.json');
    Object.assign(currentSettings, settings);
    await writeJSON('settings.json', currentSettings);
    res.json({ code: 200, message: '全局设置更新成功' });
  } catch (error) {
    res.status(500).json({ code: 500, error: '更新失败' });
  }
});

// 图片上传
router.post('/upload', authMiddleware, upload.single('file'), (req, res) => {
  if (!req.file) {
    return res.status(400).json({ code: 400, error: '请选择要上传的文件' });
  }
  const url = `/assets/images/${req.body.type || 'general'}/${req.file.filename}`;
  res.json({
    code: 200,
    data: { url, filename: req.file.filename },
    message: '上传成功'
  });
});

// 获取当前管理员信息
router.get('/admin/info', authMiddleware, (req, res) => {
  const token = req.headers.authorization?.replace('Bearer ', '');
  const tokenData = activeTokens.get(token);
  res.json({ code: 200, data: { username: tokenData.username } });
});

module.exports = router;

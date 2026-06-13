const express = require('express');
const router = express.Router();
const fs = require('fs').promises;
const path = require('path');

const DATA_DIR = path.join(__dirname, '..', 'data');

async function readJSON(filename) {
  const filePath = path.join(DATA_DIR, filename);
  const data = await fs.readFile(filePath, 'utf-8');
  return JSON.parse(data);
}

// 获取首页全部配置
router.get('/home', async (req, res) => {
  try {
    const homeData = await readJSON('home.json');
    res.json({ code: 200, data: homeData });
  } catch (error) {
    res.status(500).json({ code: 500, error: '读取首页数据失败' });
  }
});

// 获取产品列表
router.get('/products', async (req, res) => {
  try {
    const productsData = await readJSON('products.json');
    res.json({ code: 200, data: productsData });
  } catch (error) {
    res.status(500).json({ code: 500, error: '读取产品数据失败' });
  }
});

// 获取合作伙伴
router.get('/partners', async (req, res) => {
  try {
    const partnersData = await readJSON('partners.json');
    res.json({ code: 200, data: partnersData });
  } catch (error) {
    res.status(500).json({ code: 500, error: '读取合作伙伴数据失败' });
  }
});

// 获取公司信息
router.get('/company', async (req, res) => {
  try {
    const companyData = await readJSON('company.json');
    res.json({ code: 200, data: companyData });
  } catch (error) {
    res.status(500).json({ code: 500, error: '读取公司信息失败' });
  }
});

// 获取全局设置（含公告、页脚、导航等）
router.get('/settings', async (req, res) => {
  try {
    const settingsData = await readJSON('settings.json');
    res.json({ code: 200, data: settingsData });
  } catch (error) {
    res.status(500).json({ code: 500, error: '读取设置数据失败' });
  }
});

// 获取所有前端所需数据的聚合接口
router.get('/all', async (req, res) => {
  try {
    const [home, products, partners, company, settings] = await Promise.all([
      readJSON('home.json'),
      readJSON('products.json'),
      readJSON('partners.json'),
      readJSON('company.json'),
      readJSON('settings.json')
    ]);
    res.json({
      code: 200,
      data: { home, products, partners, company, settings }
    });
  } catch (error) {
    res.status(500).json({ code: 500, error: '读取数据失败' });
  }
});

module.exports = router;

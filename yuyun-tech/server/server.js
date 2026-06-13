const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const path = require('path');
const fs = require('fs');

const app = express();
const PORT = process.env.PORT || 3000;
const DATA_DIR = path.join(__dirname, 'data');

app.use(cors());
app.use(bodyParser.json({ limit: '50mb' }));
app.use(bodyParser.urlencoded({ extended: true }));
app.use(express.static(path.join(__dirname, '..', 'public')));
app.use('/admin', express.static(path.join(__dirname, '..', 'admin')));

if (!fs.existsSync(DATA_DIR)) {
  fs.mkdirSync(DATA_DIR, { recursive: true });
}

const DEFAULT_ADMIN = { username: 'admin', password: 'admin123' };
const sessions = {};

const dataConfig = {
  site: {
    companyName: '语云科技',
    companyNameEn: 'YuYun Technology',
    slogan: '智能连接世界 · 云端赋能未来',
    description: '语云科技是一家专注于云计算、人工智能与全球基础设施服务的创新型企业，致力于为客户提供安全、稳定、高效的数字化解决方案。',
    intlUrl: 'https://cloud.loveym.cloud',
    keywords: '云计算,人工智能,CDN,云服务器,企业数字化,语云科技'
  },
  home: {
    heroTitle: '全球智能云服务领导者',
    heroSubtitle: '覆盖全球 30+ 节点 · 7×24 小时专业服务',
    certificates: [
      { title: '营业执照', subtitle: 'Business License', image: 'https://trae-api-cn.mchost.guru/api/ide/v1/text_to_image?prompt=chinese%20business%20license%20certificate%20official&image_size=square', desc: '正规工商注册企业，合法合规经营。' },
      { title: '电子增值服务产业证', subtitle: 'Value-added Telecom License', image: 'https://trae-api-cn.mchost.guru/api/ide/v1/text_to_image?prompt=chinese%20value%20added%20telecom%20license&image_size=square', desc: '工信部颁发的增值电信业务经营许可证。' },
      { title: 'ISO 27001认证', subtitle: 'Information Security', image: 'https://trae-api-cn.mchost.guru/api/ide/v1/text_to_image?prompt=ISO%2027001%20certification%20information%20security&image_size=square', desc: '国际信息安全管理体系认证。' }
    ]
  },
  carousel: [
    { id: 1, title: '智能云计算平台', subtitle: 'AI驱动的下一代云基础设施', description: '弹性伸缩 · 全球加速 · 安全防护', image: '', bgColor: 'linear-gradient(135deg, #0a1f44 0%, #1e3a8a 50%, #3b82f6 100%)', btnText: '立即体验', btnLink: 'products.html' },
    { id: 2, title: '全球加速网络', subtitle: '覆盖六大洲 30+ 核心节点', description: '毫秒级响应 · 智能路由 · 容灾备份', image: '', bgColor: 'linear-gradient(135deg, #134e4a 0%, #0f766e 50%, #14b8a6 100%)', btnText: '查看节点', btnLink: 'partners.html' },
    { id: 3, title: '企业级安全防护', subtitle: '为您的业务保驾护航', description: 'DDoS防御 · WAF防火墙 · 数据加密', image: '', bgColor: 'linear-gradient(135deg, #451a03 0%, #9a3412 50%, #ea580c 100%)', btnText: '了解方案', btnLink: 'products.html' },
    { id: 4, title: 'AI 智能服务', subtitle: '让智能成为生产力', description: '大模型应用 · 智能客服 · 数据分析', image: '', bgColor: 'linear-gradient(135deg, #2e1065 0%, #6d28d9 50%, #a855f7 100%)', btnText: '开启智能', btnLink: 'products.html' }
  ],
  products: [
    { id: 1, title: '云服务器 ECS', icon: '☁️', description: '弹性计算资源，按需扩展，支持多种操作系统实例，秒级部署。', features: ['弹性伸缩', '秒级启动', '全球节点', '高可用'], price: '¥ 99/月起', link: 'products.html' },
    { id: 2, title: 'CDN 加速', icon: '🚀', description: '全球 30+ 节点智能分发，静态/动态资源加速，提升用户访问体验。', features: ['智能调度', '动态加速', 'HTTPS支持', '实时监控'], price: '¥ 0.15/GB', link: 'products.html' },
    { id: 3, title: '对象存储 OSS', icon: '💾', description: '海量、安全、低成本、高可靠的云存储服务，99.9999999999%持久性。', features: ['海量存储', '高可靠性', '多地域', '生命周期'], price: '¥ 0.12/GB/月', link: 'products.html' },
    { id: 4, title: '数据库服务', icon: '🗄️', description: '支持 MySQL、PostgreSQL、MongoDB、Redis 等主流数据库的托管服务。', features: ['高可用架构', '自动备份', '性能监控', '弹性扩容'], price: '¥ 199/月起', link: 'products.html' },
    { id: 5, title: '安全防护', icon: '🛡️', description: 'DDoS防御、WAF防火墙、主机安全一体化解决方案，保障业务安全。', features: ['DDoS防护', 'Web防火墙', '漏洞扫描', '安全审计'], price: '¥ 299/月起', link: 'products.html' },
    { id: 6, title: 'AI 智能中台', icon: '🤖', description: '提供大模型接入、智能客服、图像识别、自然语言处理等AI能力。', features: ['大模型API', '智能对话', '图像识别', '数据分析'], price: '¥ 499/月起', link: 'products.html' },
    { id: 7, title: '企业邮箱', icon: '📧', description: '专业企业级邮件服务，反垃圾反病毒，多端同步，稳定可靠。', features: ['无限容量', '多端同步', '反垃圾', 'SSL加密'], price: '¥ 99/账号/年', link: 'products.html' },
    { id: 8, title: '域名与网站', icon: '🌐', description: '全球顶级域名注册、SSL证书申请、网站托管一站式服务。', features: ['域名注册', 'SSL证书', '网站托管', 'DNS解析'], price: '¥ 59/年起', link: 'products.html' }
  ],
  partners: {
    title: '我们与以下企业携手共进',
    subtitle: 'Global Partners & Trusted By',
    partners: [
      { name: '阿里云', logo: '' }, { name: '腾讯云', logo: '' }, { name: '华为云', logo: '' },
      { name: '百度云', logo: '' }, { name: 'AWS', logo: '' }, { name: 'Cloudflare', logo: '' },
      { name: 'Microsoft Azure', logo: '' }, { name: 'Google Cloud', logo: '' }, { name: 'DigitalOcean', logo: '' },
      { name: 'Vultr', logo: '' }, { name: 'Oracle', logo: '' }, { name: 'IBM Cloud', logo: '' }
    ]
  },
  locations: {
    title: '全球服务网络',
    subtitle: 'Global Network',
    description: '语云科技在全球主要区域部署核心节点，为您的业务提供就近服务与超低延迟。',
    locations: [
      { name: '北京', region: '华北', lat: 39.9042, lng: 116.4074, desc: '中国区总部' },
      { name: '首尔', region: '亚太', lat: 37.5665, lng: 126.978, desc: '亚太枢纽' },
      { name: '新加坡', region: '东南亚', lat: 1.3521, lng: 103.8198, desc: '东南亚总部' },
      { name: '悉尼', region: '大洋洲', lat: -33.8688, lng: 151.2093, desc: '澳洲节点' },
      { name: '迪拜', region: '中东', lat: 25.2048, lng: 55.2708, desc: '中东核心节点' },
      { name: '莫斯科', region: '欧洲', lat: 55.7558, lng: 37.6173, desc: '东欧枢纽' },
      { name: '法兰克福', region: '欧洲', lat: 50.1109, lng: 8.6821, desc: '欧洲总部' },
      { name: '伦敦', region: '欧洲', lat: 51.5074, lng: -0.1278, desc: '英国节点' },
      { name: '纽约', region: '北美', lat: 40.7128, lng: -74.006, desc: '东海岸节点' },
      { name: '旧金山', region: '北美', lat: 37.7749, lng: -122.4194, desc: '全球总部' }
    ]
  },
  popup: {
    announcement: { enabled: true, title: '🎉 语云科技全新服务上线', topBarColor: '#ff6a00', buttonColor: '#ff6a00', content: '全球加速网络现已全面升级，新增中东迪拜节点，更多区域支持IPv6网络。注册新用户立享首月免费试用！咨询热线：400-800-8541', showOnce: true },
    contactConfirm: { title: '📞 即将发起咨询', content: '确认联系我们的销售顾问为您提供专属方案？工作日 9:00-21:00，节假日值班服务。', okText: '确认联系', cancelText: '再看看' }
  },
  footer: {
    salesPhone: '400-800-8541',
    salesEmail: 'sales@loveym.cloud',
    icpNumber: '京ICP备2024000000号-1',
    icpUrl: 'https://beian.miit.gov.cn',
    policeNumber: '京公网安备 11010802000000号',
    telecomLicense: 'B1-20240000 增值电信业务经营许可证',
    copyright: '© 2024 语云科技（美国）有限公司 版权所有',
    authorization: '语云科技®等是我们（语云科技美国有限公司）在中国的注册授权'
  },
  about: {
    title: '关于语云科技',
    description: ['语云科技（美国）有限公司是一家专注于云计算、人工智能、全球网络基础设施与企业数字化转型的国际化科技公司。',
      '公司总部位于美国，并在中国北京、青岛设有运营与客户服务中心。我们在中东迪拜、欧洲法兰克福、俄罗斯莫斯科、亚太新加坡、北美纽约等全球主要城市部署核心服务节点，覆盖六大洲 30+ 个区域。',
      '秉承「智能连接世界 · 云端赋能未来」的企业愿景，语云科技为全球超过 10,000+ 家企业客户提供稳定、安全、高效的云计算与数字化服务。',
      '我们是一家持正规工商营业执照、增值电信业务经营许可证（ICP）、通过ISO 27001国际信息安全体系认证的企业。']
  },
  contact: {
    title: '联系我们',
    company: '语云科技（美国）有限公司',
    cnCompany: '语云科技（北京）运营中心',
    address: '北京市海淀区中关村科技园',
    phone: '400-800-8541',
    email: 'sales@loveym.cloud',
    workTime: '工作日 9:00 - 21:00',
    supportTime: '7×24 小时技术支持'
  }
};

function getData(key) {
  const filePath = path.join(DATA_DIR, key + '.json');
  if (fs.existsSync(filePath)) {
    try {
      return JSON.parse(fs.readFileSync(filePath, 'utf8'));
    } catch (e) {
      return dataConfig[key] || {};
    }
  }
  if (dataConfig[key]) {
    try {
      fs.writeFileSync(filePath, JSON.stringify(dataConfig[key], null, 2), 'utf8');
      return dataConfig[key];
    } catch (e) {
      return dataConfig[key];
    }
  }
  return {};
}

function saveData(key, data) {
  const filePath = path.join(DATA_DIR, key + '.json');
  try {
    fs.writeFileSync(filePath, JSON.stringify(data, null, 2), 'utf8');
    return true;
  } catch (e) {
    return false;
  }
}

app.post('/api/admin/login', (req, res) => {
  const { username, password } = req.body;
  if (username === DEFAULT_ADMIN.username && password === DEFAULT_ADMIN.password) {
    const token = 'token_' + Date.now() + '_' + Math.random().toString(36).substr(2);
    sessions[token] = { username, time: Date.now() };
    res.json({ success: true, token });
  } else {
    res.status(401).json({ success: false, message: '账号或密码错误' });
  }
});

function authMiddleware(req, res, next) {
  const token = req.headers['x-admin-token'] || req.query.token;
  if (token && sessions[token]) {
    next();
  } else {
    res.status(401).json({ success: false, message: '未授权访问' });
  }
}

app.get('/api/config/:key', (req, res) => {
  const data = getData(req.params.key);
  res.json({ success: true, data });
});

app.post('/api/config/:key', authMiddleware, (req, res) => {
  if (saveData(req.params.key, req.body)) {
    res.json({ success: true, message: '保存成功' });
  } else {
    res.status(500).json({ success: false, message: '保存失败' });
  }
});

app.get('/api/admin/check', authMiddleware, (req, res) => {
  res.json({ success: true, time: Date.now() });
});

app.get('/api/health', (req, res) => {
  res.json({ success: true, time: Date.now(), service: 'yuyun-tech' });
});

app.listen(PORT, () => {
  console.log('============================================');
  console.log('  语云科技企业官网已启动');
  console.log('============================================');
  console.log('  访问地址: http://localhost:' + PORT);
  console.log('  后台管理: http://localhost:' + PORT + '/admin');
  console.log('  默认账号: admin / admin123');
  console.log('  数据目录: ' + DATA_DIR);
  console.log('============================================');
});

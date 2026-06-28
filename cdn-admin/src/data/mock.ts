import type { Site, Merchant, Product, Order, Package, WhitelistRecord, FinanceRecord, UserProfile } from '../types';

export const sProfile: UserProfile = {
  name: '总站长',
  avatar: 'S',
  balance: 0,
};

export const bProfile: UserProfile = {
  name: '商户_阿明',
  avatar: 'B',
  balance: 12850.65,
  shopName: '阿明网络',
};

export const sStats = [
  { title: '总交易额', value: '2,847,391.50', unit: '元', color: 'primary' as const },
  { title: '总订单量', value: '38,492', unit: '笔', color: 'success' as const },
  { title: '平台抽成收入', value: '186,472.80', unit: '元', color: 'warning' as const },
  { title: '商户数量', value: '1,286', sub: '待审核 32', color: 'danger' as const },
  { title: '用户数量', value: '56,832', unit: '人', color: 'primary' as const },
];

export const bStats = [
  { title: '访问量', value: '12,847', sub: '今日 / 7天 89K / 30天 410K', color: 'primary' as const },
  { title: 'QFS 量', value: '3,291,044', sub: '今日 82K / 累计 3.2M', color: 'success' as const },
  { title: '流量消耗', value: '1,204.5', unit: 'GB', sub: '今日 45GB / 累计 1.2TB', color: 'warning' as const },
  { title: '运行站点 / 证书', value: '8 / 8', sub: '全部正常运行', color: 'danger' as const },
];

export const trendLabels = ['06-22', '06-23', '06-24', '06-25', '06-26', '06-27', '06-28'];
export const trendValues = [120, 132, 101, 134, 90, 230, 210];
export const bTrendValues1 = [820, 932, 901, 934, 1290, 1330, 1320];
export const bTrendValues2 = [220, 282, 201, 234, 290, 330, 310];
export const bTrendValues3 = [120, 132, 101, 134, 90, 230, 210];

export const merchantRank = [
  { name: '极速云', amount: 482900 },
  { name: '蓝海防护', amount: 391200 },
  { name: '站点卫士', amount: 284500 },
  { name: '云盾科技', amount: 198300 },
  { name: '安全链', amount: 165000 },
  { name: '快网互联', amount: 142800 },
  { name: '防御大师', amount: 128000 },
  { name: '烽火CDN', amount: 105600 },
  { name: '蜂巢加速', amount: 93200 },
  { name: '北极星', amount: 78000 },
];

export const sites: Site[] = [
  { id: '1', name: '官网加速', domain: 'www.example.com', template: 'PC-01', products: 4, nodes: 12, status: 'running', createdAt: '2026-01-15' },
  { id: '2', name: '商城防护', domain: 'shop.example.com', template: 'PC-02', products: 6, nodes: 8, status: 'running', createdAt: '2026-02-20' },
  { id: '3', name: '博客加速', domain: 'blog.demo.com', template: 'M-01', products: 2, nodes: 4, status: 'pending', createdAt: '2026-03-10' },
  { id: '4', name: '游戏盾', domain: 'game.xxx.com', template: 'PC-03', products: 3, nodes: 16, status: 'stopped', createdAt: '2026-04-05' },
  { id: '5', name: 'API网关', domain: 'api.xxx.com', template: 'M-02', products: 1, nodes: 6, status: 'running', createdAt: '2026-05-18' },
];

export const merchants: Merchant[] = [
  { id: 'M001', avatar: '速', shopName: '极速云', phone: '138****1234', registerAt: '2026-01-10', deposit: 5000, status: 'normal' },
  { id: 'M002', avatar: '蓝', shopName: '蓝海防护', phone: '139****5678', registerAt: '2026-02-15', deposit: 3000, status: 'normal' },
  { id: 'M003', avatar: '店', shopName: '站点卫士', phone: '137****9012', registerAt: '2026-03-20', deposit: 0, status: 'pending' },
  { id: 'M004', avatar: '云', shopName: '云盾科技', phone: '136****3456', registerAt: '2026-04-12', deposit: 2000, status: 'banned' },
];

export const products: Product[] = [
  { id: 'P001', name: '基础CDN加速', type: 'CDN', nodePool: '公开节点池A', priceRange: '¥9.90 - ¥99.00', status: 'on' },
  { id: 'P002', name: '企业高防CDN', type: '高防CDN', nodePool: '高防节点池B', priceRange: '¥299.00 - ¥2999.00', status: 'on' },
  { id: 'P003', name: '游戏盾专业版', type: '游戏盾', nodePool: '游戏专用池C', priceRange: '¥599.00 - ¥5999.00', status: 'off' },
  { id: 'P004', name: '全球加速Pro', type: '全球加速', nodePool: 'Cloudflare池', priceRange: '¥199.00 - ¥1999.00', status: 'on' },
];

export const orders: Order[] = [
  { id: 'O202606280001', buyer: 'user_9527', merchant: '极速云', product: '企业高防CDN', amount: 299.00, status: 'paid', createdAt: '2026-06-28 10:23' },
  { id: 'O202606280002', buyer: 'user_3344', merchant: '蓝海防护', product: '基础CDN加速', amount: 59.00, status: 'pending', createdAt: '2026-06-28 09:45' },
  { id: 'O202606280003', buyer: 'user_7788', merchant: '站点卫士', product: '全球加速Pro', amount: 199.00, status: 'refunded', createdAt: '2026-06-27 22:10' },
  { id: 'O202606280004', buyer: 'user_1122', merchant: '云盾科技', product: '游戏盾专业版', amount: 599.00, status: 'closed', createdAt: '2026-06-27 18:33' },
];

export const packages: Package[] = [
  { id: 'PKG01', name: '入门版', flow: '100GB', bandwidth: '10Mbps', domains: 1, price: 9.90, period: '月' },
  { id: 'PKG02', name: '标准版', flow: '500GB', bandwidth: '50Mbps', domains: 3, price: 49.00, period: '月' },
  { id: 'PKG03', name: '专业版', flow: '2TB', bandwidth: '200Mbps', domains: 10, price: 199.00, period: '月' },
  { id: 'PKG04', name: '企业版', flow: '10TB', bandwidth: '1Gbps', domains: 50, price: 999.00, period: '月' },
];

export const whitelistRecords: WhitelistRecord[] = [
  { id: 'W001', domain: 'www.shop-a.com', purpose: '电商站点加速', icp: '京ICP备123456号', status: 'approved', createdAt: '2026-06-25' },
  { id: 'W002', domain: 'blog.demo-b.com', purpose: '个人博客', icp: '-', status: 'pending', createdAt: '2026-06-27' },
  { id: 'W003', domain: 'api.game-c.com', purpose: '游戏API', icp: '沪ICP备654321号', status: 'rejected', createdAt: '2026-06-26', reason: '资料不完整' },
];

export const financeRecords: FinanceRecord[] = [
  { id: 'F001', type: 'income', amount: 1299.00, balance: 12850.65, desc: '订单 O202606280001 分润', createdAt: '2026-06-28 10:25' },
  { id: 'F002', type: 'withdraw', amount: -5000.00, balance: 11551.65, desc: '提现至支付宝', createdAt: '2026-06-27 16:00' },
  { id: 'F003', type: 'expense', amount: -299.00, balance: 16551.65, desc: '套餐续费', createdAt: '2026-06-26 09:12' },
  { id: 'F004', type: 'frozen', amount: -200.00, balance: 16850.65, desc: '异常订单冻结', createdAt: '2026-06-25 14:33' },
];

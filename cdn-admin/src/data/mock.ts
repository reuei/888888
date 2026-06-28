import type { Site, Merchant, Product, Order, Package, WhitelistRecord, FinanceRecord, UserProfile, Category, Node, Sku, Complaint, InviteCode, Article, AdSlot, Coupon, User, UserGroup, UserLevel, LuckyNumber, RealnameRecord, Agent, AgentProduct, CommissionRecord, Gateway, MyPackage, Notification } from '../types';

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

export const categories: Category[] = [
  { id: 'C1', name: 'CDN 加速', parentId: null, sort: 1 },
  { id: 'C2', name: '高防 CDN', parentId: null, sort: 2 },
  { id: 'C3', name: '游戏盾', parentId: null, sort: 3 },
  { id: 'C4', name: '全球加速', parentId: null, sort: 4 },
  { id: 'C5', name: '企业级', parentId: 'C1', sort: 1 },
];

export const nodes: Node[] = [
  { id: 'N001', name: '北京电信-01', ip: '1.1.1.1', region: '华北', isp: '电信', type: '自建', health: 'healthy', enabled: true, latency: 12, uptime: '99.99%' },
  { id: 'N002', name: '上海联通-01', ip: '2.2.2.2', region: '华东', isp: '联通', type: '自建', health: 'healthy', enabled: true, latency: 15, uptime: '99.95%' },
  { id: 'N003', name: '广州移动-01', ip: '3.3.3.3', region: '华南', isp: '移动', type: '公开节点', health: 'warning', enabled: true, latency: 45, uptime: '98.50%' },
  { id: 'N004', name: 'Cloudflare-HK', ip: 'cf-hk.example.com', region: '海外', isp: 'BGP', type: 'Cloudflare', health: 'healthy', enabled: true, latency: 28, uptime: '99.90%' },
  { id: 'N005', name: '成都电信-01', ip: '4.4.4.4', region: '西南', isp: '电信', type: '自建', health: 'offline', enabled: false, latency: 0, uptime: '0.00%' },
];

export const skus: Sku[] = [
  { id: 'S001', name: '入门版', bandwidth: '10Mbps', flow: '100GB/月', domains: 1, ccLevel: '基础', price: 9.90 },
  { id: 'S002', name: '标准版', bandwidth: '50Mbps', flow: '500GB/月', domains: 3, ccLevel: '标准', price: 49.00 },
  { id: 'S003', name: '专业版', bandwidth: '200Mbps', flow: '2TB/月', domains: 10, ccLevel: '高级', price: 199.00 },
  { id: 'S004', name: '企业版', bandwidth: '1Gbps', flow: '10TB/月', domains: 50, ccLevel: '企业', price: 999.00 },
];

export const complaints: Complaint[] = [
  { id: 'CP001', orderId: 'O202606280003', plaintiff: 'user_7788', defendant: '站点卫士', reason: '未到账', status: 'pending', createdAt: '2026-06-28 09:00' },
  { id: 'CP002', orderId: 'O202606270056', plaintiff: 'user_3344', defendant: '蓝海防护', reason: '商品与描述不符', status: 'resolved', createdAt: '2026-06-27 14:20' },
  { id: 'CP003', orderId: 'O202606260012', plaintiff: 'user_9527', defendant: '极速云', reason: '无法访问', status: 'rejected', createdAt: '2026-06-26 11:30' },
];

export const inviteCodes: InviteCode[] = [
  { id: 'I001', code: 'INVITE2026A', maxUses: 100, usedCount: 32, expiry: '2026-12-31', status: 'active' },
  { id: 'I002', code: 'VIP888', maxUses: 50, usedCount: 50, expiry: '2026-06-01', status: 'expired' },
  { id: 'I003', code: 'TEST001', maxUses: 10, usedCount: 2, expiry: '2026-12-31', status: 'disabled' },
];

export const articles: Article[] = [
  { id: 'A001', title: '平台六一八活动公告', category: '平台公告', isTop: true, status: 'published', publishAt: '2026-06-18 10:00' },
  { id: 'A002', title: '关于调整结算周期的通知', category: '结算公告', isTop: false, status: 'published', publishAt: '2026-06-20 16:00' },
  { id: 'A003', title: '新手入驻指南', category: '帮助文档', isTop: false, status: 'draft', publishAt: '-' },
];

export const adSlots: AdSlot[] = [
  { id: 'AD001', name: 'PC 首页轮播', position: '电脑端首页顶部', size: '1920x400', status: 'on' },
  { id: 'AD002', name: '购卡页横幅', position: '购卡页中部', size: '1200x200', status: 'on' },
  { id: 'AD003', name: 'APP 启动页', position: 'APP 启动页', size: '750x1334', status: 'off' },
];

export const coupons: Coupon[] = [
  { id: 'CO001', batch: 'BATCH0618', type: 'fixed', value: 10, threshold: 50, total: 1000, received: 856, status: 'active' },
  { id: 'CO002', batch: 'BATCHNEW', type: 'percent', value: 20, threshold: 100, total: 500, received: 500, status: 'expired' },
];

export const settlementRecords = [
  { id: 'SET001', merchant: '极速云', cycle: 'T+1', amount: 4820.00, fee: 48.20, status: 'settled', time: '2026-06-28 10:00' },
  { id: 'SET002', merchant: '蓝海防护', cycle: 'T+1', amount: 3150.50, fee: 31.51, status: 'settled', time: '2026-06-27 10:00' },
  { id: 'SET003', merchant: '站点卫士', cycle: 'T+7', amount: 1200.00, fee: 12.00, status: 'pending', time: '2026-06-28 09:00' },
];

export const users: User[] = [
  { id: 'U001', nickname: 'user_9527', phone: '138****1234', level: 'VIP1', group: '默认分组', registerAt: '2026-01-10', status: 'normal' },
  { id: 'U002', nickname: 'user_3344', phone: '139****5678', level: '普通会员', group: '默认分组', registerAt: '2026-02-15', status: 'normal' },
  { id: 'U003', nickname: 'user_7788', phone: '137****9012', level: 'VIP2', group: '高价值用户', registerAt: '2026-03-20', status: 'banned' },
  { id: 'U004', nickname: 'user_1122', phone: '136****3456', level: '普通会员', group: '默认分组', registerAt: '2026-04-12', status: 'normal' },
];

export const userGroups: UserGroup[] = [
  { id: 'G1', name: '默认分组', userCount: 4820 },
  { id: 'G2', name: '高价值用户', userCount: 356 },
  { id: 'G3', name: '渠道引流用户', userCount: 128 },
];

export const userLevels: UserLevel[] = [
  { id: 'L1', name: '普通会员', minAmount: 0, discount: 1.0 },
  { id: 'L2', name: 'VIP1', minAmount: 500, discount: 0.95 },
  { id: 'L3', name: 'VIP2', minAmount: 2000, discount: 0.88 },
  { id: 'L4', name: 'VIP3', minAmount: 5000, discount: 0.8 },
];

export const luckyNumbers: LuckyNumber[] = [
  { id: 'N001', number: '888888', price: 88.00, sold: false },
  { id: 'N002', number: '666666', price: 66.00, sold: true },
  { id: 'N003', number: '168888', price: 48.00, sold: false },
  { id: 'N004', number: '5201314', price: 128.00, sold: false },
  { id: 'N005', number: '999999', price: 99.00, sold: false },
];

export const realnameRecords: RealnameRecord[] = [
  { id: 'R001', userId: 'U001', name: '张三', idCard: '11010119900101****', phone: '138****1234', status: 'approved', submittedAt: '2026-06-20 10:00' },
  { id: 'R002', userId: 'U002', name: '李四', idCard: '31010119900202****', phone: '139****5678', status: 'pending', submittedAt: '2026-06-27 14:30' },
  { id: 'R003', userId: 'U003', name: '王五', idCard: '44010119900303****', phone: '137****9012', status: 'rejected', submittedAt: '2026-06-25 09:15' },
];

export const agents: Agent[] = [
  { id: 'A001', name: '总代理-老李', parent: null, level: 1, commission: 15 },
  { id: 'A002', name: '一级代理-小张', parent: 'A001', level: 2, commission: 10 },
  { id: 'A003', name: '二级代理-阿强', parent: 'A002', level: 3, commission: 8 },
  { id: 'A004', name: '一级代理-小美', parent: 'A001', level: 2, commission: 10 },
];

export const agentProducts: AgentProduct[] = [
  { id: 'AP001', name: '基础CDN加速-代理版', source: '极速云', costPrice: 8.00, retailPrice: 12.00, status: 'on' },
  { id: 'AP002', name: '企业高防CDN-代理版', source: '蓝海防护', costPrice: 250.00, retailPrice: 299.00, status: 'on' },
  { id: 'AP003', name: '全球加速Pro-代理版', source: '站点卫士', costPrice: 180.00, retailPrice: 199.00, status: 'pending' },
];

export const commissionRecords: CommissionRecord[] = [
  { id: 'C001', agent: '一级代理-小张', orderId: 'O202606280001', amount: 29.90, status: 'settled', createdAt: '2026-06-28 10:30' },
  { id: 'C002', agent: '二级代理-阿强', orderId: 'O202606280002', amount: 5.90, status: 'pending', createdAt: '2026-06-28 09:50' },
  { id: 'C003', agent: '一级代理-小美', orderId: 'O202606270056', amount: 19.90, status: 'settled', createdAt: '2026-06-27 15:00' },
];

export const gateways: Gateway[] = [
  { id: 'GW001', name: '支付宝官方', channel: 'alipay', fee: 0.6, enabled: true, isDefault: true },
  { id: 'GW002', name: '微信支付商户号', channel: 'wxpay', fee: 0.6, enabled: true, isDefault: false },
  { id: 'GW003', name: '易支付-通道A', channel: 'epay', fee: 2.0, enabled: true, isDefault: false },
  { id: 'GW004', name: 'USDT-TRC20', channel: 'usdt', fee: 1.0, enabled: false, isDefault: false },
];

export const myPackages: MyPackage[] = [
  { id: 'MP001', name: '标准版', flow: '500GB', bandwidth: '50Mbps', domains: 3, expireAt: '2026-12-31', status: 'active' },
  { id: 'MP002', name: '专业版', flow: '2TB', bandwidth: '200Mbps', domains: 10, expireAt: '2027-01-15', status: 'active' },
  { id: 'MP003', name: '入门版', flow: '100GB', bandwidth: '10Mbps', domains: 1, expireAt: '2026-05-31', status: 'expired' },
];

export const notifications: Notification[] = [
  { id: 'NT001', title: '新商户入驻待审核', content: '商户「云盾科技」提交入驻申请，请及时处理。', type: 'system', read: false, createdAt: '2026-06-28 11:20', link: '/s/merchant-audit' },
  { id: 'NT002', title: '域名过白申请被驳回', content: '您提交的 api.game-c.com 过白申请已被驳回，原因：资料不完整。', type: 'alert', read: false, createdAt: '2026-06-28 10:05', link: '/b/whitelist' },
  { id: 'NT003', title: '收到新订单 O202606280001', content: '用户 user_9527 购买了企业高防CDN，金额 ¥299.00。', type: 'order', read: true, createdAt: '2026-06-28 10:23', link: '/s/orders' },
  { id: 'NT004', title: '结算单 SET001 已打款', content: '您的 T+1 结算单 ¥4820.00 已通过支付宝打款。', type: 'finance', read: true, createdAt: '2026-06-28 09:30', link: '/b/finance' },
  { id: 'NT005', title: '节点池高防B 延迟告警', content: '节点池高防B 平均延迟超过 200ms，请关注。', type: 'alert', read: true, createdAt: '2026-06-27 22:15', link: '/s/nodes' },
];


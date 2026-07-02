export interface MenuItem {
  key: string;
  label: string;
  icon?: string;
  children?: MenuItem[];
}

export const sMenu: MenuItem[] = [
  { key: '/s/dashboard', label: '仪表盘', icon: 'LayoutDashboard' },
  {
    key: '/s/transaction-stats',
    label: '数据报表',
    icon: 'BarChart3',
    children: [
      { key: '/s/transaction-stats', label: '交易统计' },
      { key: '/s/user-growth', label: '用户增长' },
      { key: '/s/merchant-analysis', label: '商户分析' },
    ],
  },
  {
    key: '/s/sites',
    label: '站点管理',
    icon: 'Globe',
    children: [
      { key: '/s/sites', label: '站点列表' },
      { key: '/s/site-config', label: '站点配置' },
    ],
  },
  {
    key: '/s/merchants',
    label: '商户管理',
    icon: 'Users',
    children: [
      { key: '/s/merchants', label: '商户列表' },
      { key: '/s/merchant-audit', label: '商户审核' },
      { key: '/s/invites', label: '邀请码管理' },
    ],
  },
  {
    key: '/s/products',
    label: '商品管理',
    icon: 'Package',
    children: [
      { key: '/s/products', label: 'CDN产品列表' },
      { key: '/s/categories', label: '产品分类' },
      { key: '/s/nodes', label: 'CDN节点管理' },
      { key: '/s/skus', label: '套餐规格管理' },
    ],
  },
  {
    key: '/s/orders',
    label: '订单管理',
    icon: 'ShoppingCart',
    children: [
      { key: '/s/orders', label: '全部订单' },
      { key: '/s/complaints', label: '投诉管理' },
      { key: '/s/abnormal-orders', label: '异常订单处理' },
    ],
  },
  {
    key: '/s/users',
    label: '会员/用户管理',
    icon: 'UserCircle',
    children: [
      { key: '/s/users', label: '用户列表' },
      { key: '/s/user-groups', label: '用户分组' },
      { key: '/s/user-levels', label: '用户等级' },
      { key: '/s/lucky-numbers', label: '自助选号' },
      { key: '/s/user-realname', label: '用户实名审核' },
      { key: '/s/user-rank', label: '用户流水排行' },
    ],
  },
  {
    key: '/s/agents',
    label: '代理/分销管理',
    icon: 'GitBranch',
    children: [
      { key: '/s/agent-dock', label: '代理商品对接' },
      { key: '/s/agent-products', label: '下级代理商品' },
      { key: '/s/agent-tree', label: '代理关系树' },
      { key: '/s/agent-commission', label: '佣金结算' },
      { key: '/s/agent-audit', label: '代理商品审核' },
    ],
  },
  {
    key: '/s/finance',
    label: '财务管理',
    icon: 'Wallet',
    children: [
      { key: '/s/finance', label: '资金流水总览' },
      { key: '/s/settlement-manual', label: '手动结算' },
      { key: '/s/settlement-auto', label: '自动结算' },
      { key: '/s/alipay-export', label: '支付宝打款导出' },
      { key: '/s/gateway-config', label: '网关配置' },
    ],
  },
  { key: '/s/payments', label: '支付网关管理', icon: 'CreditCard' },
  { key: '/s/templates', label: '模板与前端管理', icon: 'Monitor' },
  { key: '/s/articles', label: '文章/公告管理', icon: 'FileText' },
  { key: '/s/ads', label: '广告位管理', icon: 'Image' },
  { key: '/s/coupons', label: '优惠券/营销管理', icon: 'Ticket' },
  {
    key: '/s/operation-logs',
    label: '系统运维',
    icon: 'ClipboardList',
    children: [
      { key: '/s/operation-logs', label: '操作日志' },
      { key: '/s/api-docs', label: 'API 文档' },
      { key: '/s/roles', label: '权限角色管理' },
      { key: '/s/backup', label: '数据备份' },
    ],
  },
  { key: '/s/system', label: '系统设置', icon: 'Settings' },
];

export const bMenu: MenuItem[] = [
  { key: '/b/dashboard', label: '仪表盘', icon: 'LayoutDashboard' },
  {
    key: '/b/sites',
    label: '站点管理',
    icon: 'Globe',
    children: [
      { key: '/b/sites', label: '我的站点' },
      { key: '/b/add-site', label: '添加站点' },
    ],
  },
  {
    key: '/b/packages',
    label: '套餐管理',
    icon: 'Package',
    children: [
      { key: '/b/packages', label: '在线订购套餐' },
      { key: '/b/my-packages', label: '我的套餐' },
      { key: '/b/renew', label: '套餐续费' },
    ],
  },
  {
    key: '/b/orders',
    label: '订单管理',
    icon: 'ShoppingCart',
    children: [
      { key: '/b/orders', label: '我的订单' },
      { key: '/b/invoice', label: '发票申请' },
    ],
  },
  { key: '/b/whitelist', label: '域名过白管理', icon: 'Shield' },
  { key: '/b/finance', label: '财务管理', icon: 'Wallet' },
  { key: '/b/settings', label: '个人设置', icon: 'UserCog' },
];

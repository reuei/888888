export interface MenuItem {
  key: string;
  label: string;
  icon?: string;
  children?: MenuItem[];
}

// S端 (super admin) primary color: #2F6BFF (blue)
export const sPrimaryColor = '#2F6BFF';

export const sMenu: MenuItem[] = [
  { key: '/s/dashboard', label: '仪表盘', icon: 'LayoutDashboard' },
  {
    key: '/s/stations',
    label: '分站管理',
    icon: 'Network',
    children: [
      { key: '/s/stations', label: '分站列表' },
      { key: '/s/station-create', label: '新建分站' },
      { key: '/s/station-monitor', label: '分站运营监控' },
    ],
  },
  {
    key: '/s/merchants',
    label: '商户管理',
    icon: 'Store',
    children: [
      { key: '/s/merchants', label: '入驻店铺列表' },
      { key: '/s/merchant-audit', label: '商户审核' },
      { key: '/s/merchant-mode', label: '入驻模式设置' },
      { key: '/s/merchant-ban', label: '封禁/解禁管理' },
      { key: '/s/merchant-realname', label: '实名认证管理' },
    ],
  },
  {
    key: '/s/products',
    label: '商品管理',
    icon: 'Package',
    children: [
      { key: '/s/products', label: '全平台商品列表' },
      { key: '/s/categories', label: '商品分类管理' },
      { key: '/s/banned-products', label: '禁售目录设置' },
      { key: '/s/card-secrets', label: '卡密管理' },
      { key: '/s/stock-alert', label: '库存预警设置' },
    ],
  },
  {
    key: '/s/orders',
    label: '订单管理',
    icon: 'ShoppingCart',
    children: [
      { key: '/s/orders', label: '全平台订单列表' },
      { key: '/s/complaints', label: '投诉管理' },
      { key: '/s/batch-payment', label: '批量付款通知' },
      { key: '/s/abnormal-orders', label: '异常订单标记' },
    ],
  },
  {
    key: '/s/users',
    label: '会员/用户管理',
    icon: 'UsersRound',
    children: [
      { key: '/s/users', label: '全平台用户列表' },
      { key: '/s/user-groups', label: '用户分组与等级' },
      { key: '/s/lucky-numbers', label: '自助选号' },
      { key: '/s/user-realname', label: '实名认证审核' },
      { key: '/s/user-rank', label: '用户流水排行' },
    ],
  },
  {
    key: '/s/agent-dock',
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
      { key: '/s/fee-groups', label: '费率分组管理' },
      { key: '/s/merchant-fee', label: '单商户费率' },
      { key: '/s/settlement-cycle', label: '结算周期设置' },
      { key: '/s/settlement-manual', label: '结算打款' },
      { key: '/s/alipay-export', label: '支付宝打款导出' },
    ],
  },
  { key: '/s/payments', label: '支付网关管理', icon: 'CreditCard' },
  { key: '/s/templates', label: '模板与前端管理', icon: 'LayoutTemplate' },
  { key: '/s/articles', label: '文章/公告管理', icon: 'FileText' },
  { key: '/s/ads', label: '广告位管理', icon: 'Image' },
  { key: '/s/coupons', label: '优惠券/营销管理', icon: 'Ticket' },
  {
    key: '/s/transaction-stats',
    label: '数据统计与日志',
    icon: 'BarChart3',
    children: [
      { key: '/s/transaction-stats', label: '经营报表' },
      { key: '/s/merchant-analysis', label: '商户流水排行' },
      { key: '/s/risk-monitor', label: '风控大屏' },
      { key: '/s/operation-logs', label: '操作日志' },
    ],
  },
  { key: '/s/system', label: '系统设置', icon: 'Settings' },
];

// B端 (merchant) primary color: #06B6D4 (cyan)
export const bPrimaryColor = '#06B6D4';

export const bMenu: MenuItem[] = [
  { key: '/b/dashboard', label: '仪表盘', icon: 'LayoutDashboard' },
  {
    key: '/b/products',
    label: '商品管理',
    icon: 'Package',
    children: [
      { key: '/b/products', label: '商品列表' },
      { key: '/b/card-import', label: '卡密导入' },
      { key: '/b/source-pickup', label: '货源采集' },
      { key: '/b/agent-dock', label: '代理对接' },
    ],
  },
  {
    key: '/b/orders',
    label: '订单管理',
    icon: 'ShoppingCart',
    children: [
      { key: '/b/orders', label: '发货/退款' },
      { key: '/b/order-search', label: '查单' },
      { key: '/b/complaints', label: '投诉处理' },
    ],
  },
  { key: '/b/customer-service', label: '客服管理', icon: 'Headset' },
  {
    key: '/b/finance',
    label: '资金管理',
    icon: 'Wallet',
    children: [
      { key: '/b/finance', label: '结算记录' },
      { key: '/b/settlement-pending', label: '待结算' },
      { key: '/b/flow-rank', label: '流水排行' },
    ],
  },
  {
    key: '/b/settings',
    label: '店铺设置',
    icon: 'Store',
    children: [
      { key: '/b/settings', label: '店铺信息' },
      { key: '/b/realname-apply', label: '实名申请' },
      { key: '/b/custom-payment', label: '自定义支付' },
      { key: '/b/subdomain', label: '引导页子域名' },
    ],
  },
];

export type Role = 's' | 'b' | 'c';
export type SalesRole = 'user';

export interface MenuItem {
  key: string;
  label: string;
  icon?: string;
  children?: MenuItem[];
}

export interface StatCardData {
  title: string;
  value: string;
  unit?: string;
  sub?: string;
  color?: 'primary' | 'success' | 'warning' | 'danger';
}

export interface Site {
  id: string;
  name: string;
  domain: string;
  template: string;
  products: number;
  nodes: number;
  status: 'running' | 'stopped' | 'pending';
  createdAt: string;
}

export interface Merchant {
  id: string;
  avatar: string;
  shopName: string;
  phone: string;
  registerAt: string;
  deposit: number;
  status: 'normal' | 'pending' | 'banned';
}

export interface Product {
  id: string;
  name: string;
  type: string;
  nodePool: string;
  priceRange: string;
  status: 'on' | 'off';
}

export interface Order {
  id: string;
  buyer: string;
  merchant: string;
  product: string;
  amount: number;
  status: 'paid' | 'pending' | 'refunded' | 'closed';
  createdAt: string;
}

export interface Package {
  id: string;
  name: string;
  flow: string;
  bandwidth: string;
  domains: number;
  price: number;
  period: string;
}

export interface WhitelistRecord {
  id: string;
  domain: string;
  purpose: string;
  icp: string;
  status: 'pending' | 'approved' | 'rejected';
  createdAt: string;
  reason?: string;
}

export interface FinanceRecord {
  id: string;
  type: 'income' | 'expense' | 'frozen' | 'withdraw';
  amount: number;
  balance: number;
  desc: string;
  createdAt: string;
}

export interface UserProfile {
  name: string;
  avatar: string;
  balance: number;
  shopName?: string;
}

export interface Category {
  id: string;
  name: string;
  parentId: string | null;
  sort: number;
}

export interface Node {
  id: string;
  name: string;
  ip: string;
  region: string;
  isp: string;
  type: 'Cloudflare' | '自建' | '公开节点';
  health: 'healthy' | 'warning' | 'offline';
  enabled: boolean;
  latency: number;
  uptime: string;
}

export interface Sku {
  id: string;
  name: string;
  bandwidth: string;
  flow: string;
  domains: number;
  ccLevel: string;
  price: number;
}

export interface Complaint {
  id: string;
  orderId: string;
  plaintiff: string;
  defendant: string;
  reason: string;
  status: 'pending' | 'resolved' | 'rejected';
  createdAt: string;
}

export interface InviteCode {
  id: string;
  code: string;
  maxUses: number;
  usedCount: number;
  expiry: string;
  status: 'active' | 'expired' | 'disabled';
}

export interface Article {
  id: string;
  title: string;
  category: string;
  isTop: boolean;
  status: 'published' | 'draft';
  publishAt: string;
}

export interface AdSlot {
  id: string;
  name: string;
  position: string;
  size: string;
  status: 'on' | 'off';
}

export interface Coupon {
  id: string;
  batch: string;
  type: 'fixed' | 'percent';
  value: number;
  threshold: number;
  total: number;
  received: number;
  status: 'active' | 'expired';
}

export interface CouponRecord {
  id: string;
  code: string;
  batch: string;
  user: string;
  order: string;
  usedAt: string;
}

export interface TemplateItem {
  id: string;
  name: string;
  type: 'pc' | 'mobile' | 'card';
}

export interface User {
  id: string;
  nickname: string;
  phone: string;
  level: string;
  group: string;
  registerAt: string;
  status: 'normal' | 'banned';
}

export interface UserGroup {
  id: string;
  name: string;
  userCount: number;
}

export interface UserLevel {
  id: string;
  name: string;
  minAmount: number;
  discount: number;
}

export interface LuckyNumber {
  id: string;
  number: string;
  price: number;
  sold: boolean;
}

export interface Agent {
  id: string;
  name: string;
  parent: string | null;
  level: number;
  commission: number;
}

// Agent product (代理对接商品)
export interface AgentProduct {
  id: string;
  sourceProductId: string;
  agentMerchantId: string;
  costPrice: number;
  salePrice: number;
  auditStatus: 'pending' | 'approved' | 'rejected';
  status: 'on' | 'off';
}

export interface CommissionRecord {
  id: string;
  agent: string;
  orderId: string;
  amount: number;
  status: 'settled' | 'pending';
  createdAt: string;
}

export interface RealnameRecord {
  id: string;
  userId: string;
  name: string;
  idCard: string;
  phone: string;
  status: 'pending' | 'approved' | 'rejected';
  submittedAt: string;
}

export interface Gateway {
  id: string;
  name: string;
  channel: string;
  fee: number;
  enabled: boolean;
  isDefault: boolean;
}

export interface MyPackage {
  id: string;
  name: string;
  flow: string;
  bandwidth: string;
  domains: number;
  expireAt: string;
  status: 'active' | 'expired' | 'pending';
}

export interface BOrder {
  id: string;
  product: string;
  amount: number;
  status: 'pending' | 'paid' | 'cancelled' | 'refunded';
  createdAt: string;
  paidAt?: string;
  packageId: string;
  period: string;
}

export interface InvoiceItem {
  name: string;
  quantity: number;
  unitPrice: number;
  amount: number;
}

export interface Invoice {
  id: string;
  orderId: string;
  amount: number;
  status: 'pending' | 'issued' | 'cancelled';
  type: 'personal' | 'company';
  title: string;
  taxId?: string;
  items: InvoiceItem[];
  createdAt: string;
  issuedAt?: string;
}

export interface Notification {
  id: string;
  title: string;
  content: string;
  type: 'system' | 'order' | 'alert' | 'finance';
  read: boolean;
  createdAt: string;
  link?: string;
}

export interface OperationLog {
  id: string;
  operator: string;
  module: string;
  action: string;
  detail: string;
  ip: string;
  createdAt: string;
}

export interface RolePermission {
  id: string;
  name: string;
  description: string;
  permissions: string[];
  userCount: number;
}

export interface ApiDoc {
  id: string;
  method: 'GET' | 'POST' | 'PUT' | 'DELETE';
  path: string;
  name: string;
  desc: string;
  group: string;
}

// Settlement record
export interface SettlementRecord {
  id: string;
  merchant: string;
  cycle: string;
  amount: number;
  fee: number;
  netAmount: number;
  status: 'settled' | 'pending';
  time: string;
}

export interface BackupRecord {
  id: string;
  name: string;
  size: string;
  type: 'manual' | 'auto';
  status: 'success' | 'running' | 'failed';
  createdAt: string;
}

export interface DailyStat {
  date: string;
  revenue: number;
  orders: number;
  users: number;
  merchants: number;
}

export interface MerchantStat {
  merchant: string;
  revenue: number;
  orders: number;
  avgOrderValue: number;
  growth: number;
}

export interface UserGrowthStat {
  date: string;
  newUsers: number;
  activeUsers: number;
  paidUsers: number;
}

// ============ Card platform domain (发卡平台) ============

// Multi-tenant station (分站)
export interface Station {
  id: string;
  name: string;
  domain: string;
  themeColor: string;
  superAdmin: string;
  settleMode: 't0' | 't1' | 't7';
  merchantCount: number;
  orderCount: number;
  revenue: number;
  status: 'active' | 'suspended';
  createdAt: string;
}

// Card secret (卡密)
export interface CardSecret {
  id: string;
  productId: string;
  merchantId: string;
  content: string;
  status: 'unsold' | 'sold' | 'locked' | 'voided';
  orderId?: string;
  importBatchId: string;
  createdAt: string;
}

// Import batch (导入批次)
export interface ImportBatch {
  id: string;
  operatorId: string;
  operatorRole: 's' | 'b';
  total: number;
  success: number;
  fail: number;
  deliverMode: 'sequential' | 'random';
  errorFileUrl?: string;
  createdAt: string;
}

// Agent relation (代理关系树)
export interface AgentRelation {
  id: string;
  parentMerchantId: string;
  childMerchantId: string;
  level: number;
  path: string;
  commissionRate: number;
}

// Profit share (分润明细)
export interface ProfitShare {
  id: string;
  orderId: string;
  merchantId: string;
  role: 'platform' | 'parent' | 'agent';
  amount: number;
  rate: number;
  settleStatus: 'settled' | 'pending';
}

// Payment risk rule (风控策略)
export interface PayRiskRule {
  id: string;
  scopeType: 'group' | 'merchant';
  scopeId: string;
  gatewayCode: string;
  minAmount: number;
  maxAmount: number;
  randomEnabled: boolean;
  randomRange: string;
}

// Payment channel (支付通道)
export interface PayChannel {
  id: string;
  gatewayCode: string;
  gatewayName: string;
  scopeType: 'group' | 'merchant' | 'global';
  scopeId: string;
  enabled: boolean;
  rate: number;
  capFee: number;
  costFee: number;
  sort: number;
}

// Payment lock (随机金额占用)
export interface PaymentLock {
  id: string;
  realAmount: number;
  orderId: string;
  gatewayCode: string;
  expireAt: string;
}

// Fee group (费率分组)
export interface FeeGroup {
  id: string;
  name: string;
  rate: number;
  merchantCount: number;
  description: string;
}

// Banned product (禁售目录)
export interface BannedProduct {
  id: string;
  keyword: string;
  category: string;
  matchType: 'keyword' | 'category';
  createdAt: string;
}

// Stock alert (库存预警)
export interface StockAlert {
  id: string;
  productId: string;
  productName: string;
  threshold: number;
  currentStock: number;
  merchantName: string;
  status: 'alerting' | 'resolved';
}

// Announcement with popup
export interface Announcement {
  id: string;
  title: string;
  content: string;
  type: 'popup' | 'notice' | 'article';
  isPopup: boolean;
  countdown: number;
  publishAt: string;
  status: 'published' | 'draft';
}

// Customer service message
export interface CSMessage {
  id: string;
  userId: string;
  merchantId: string;
  content: string;
  direction: 'in' | 'out';
  status: 'read' | 'unread';
  createdAt: string;
}

// Card product (发卡商品)
export interface CardProduct {
  id: string;
  name: string;
  categoryId: string;
  merchantId: string;
  merchantName: string;
  price: number;
  stock: number;
  soldCount: number;
  deliveryMode: 'auto' | 'manual';
  status: 'on' | 'off';
  template: string;
}

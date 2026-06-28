export type Role = 's' | 'b';

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

export interface AgentProduct {
  id: string;
  name: string;
  source: string;
  costPrice: number;
  retailPrice: number;
  status: 'on' | 'off' | 'pending';
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

export interface Notification {
  id: string;
  title: string;
  content: string;
  type: 'system' | 'order' | 'alert' | 'finance';
  read: boolean;
  createdAt: string;
  link?: string;
}

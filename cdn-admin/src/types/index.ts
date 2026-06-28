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

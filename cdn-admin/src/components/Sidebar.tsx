import { useState } from 'react';
import { NavLink, useLocation } from 'react-router-dom';
import type { Role } from '../types';
import {
  LayoutDashboard,
  Globe,
  Users,
  Package,
  ShoppingCart,
  CreditCard,
  FileText,
  Settings,
  Image,
  Ticket,
  Server,
  Shield,
  Monitor,
  Smartphone,
  BarChart3,
  Wallet,
  UserCog,
  UserCircle,
  GitBranch,
  ClipboardList,
  FileCode,
  ShieldCheck,
  Database,
} from 'lucide-react';

const iconMap: Record<string, React.ReactNode> = {
  LayoutDashboard: <LayoutDashboard size={16} />,
  Globe: <Globe size={16} />,
  Users: <Users size={16} />,
  Package: <Package size={16} />,
  ShoppingCart: <ShoppingCart size={16} />,
  CreditCard: <CreditCard size={16} />,
  FileText: <FileText size={16} />,
  Settings: <Settings size={16} />,
  Image: <Image size={16} />,
  Ticket: <Ticket size={16} />,
  Server: <Server size={16} />,
  Shield: <Shield size={16} />,
  Monitor: <Monitor size={16} />,
  Smartphone: <Smartphone size={16} />,
  BarChart3: <BarChart3 size={16} />,
  Wallet: <Wallet size={16} />,
  UserCog: <UserCog size={16} />,
  UserCircle: <UserCircle size={16} />,
  GitBranch: <GitBranch size={16} />,
  ClipboardList: <ClipboardList size={16} />,
  FileCode: <FileCode size={16} />,
  ShieldCheck: <ShieldCheck size={16} />,
  Database: <Database size={16} />,
};

export const sMenu = [
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

export const bMenu = [
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

interface SidebarProps {
  role: Role;
  collapsed: boolean;
  onNavigate?: () => void;
}

export default function Sidebar({ role, collapsed, onNavigate }: SidebarProps) {
  const menu = role === 's' ? sMenu : bMenu;
  const location = useLocation();
  const [openKeys, setOpenKeys] = useState<string[]>(() =>
    menu.filter((m) => m.children && m.children.some((c) => c.key === location.pathname)).map((m) => m.key)
  );

  const toggle = (key: string) => {
    setOpenKeys((prev) => (prev.includes(key) ? prev.filter((k) => k !== key) : [...prev, key]));
  };

  return (
    <aside
      className={`${collapsed ? 'w-16' : 'w-56'} bg-card border-r border-border flex flex-col shrink-0 transition-all duration-200 h-full`}
    >
      <div className="h-12 flex items-center justify-center border-b border-border font-bold text-primary">
        {collapsed ? 'C' : 'CDN 平台'}
      </div>
      <nav className="flex-1 overflow-y-auto py-3">
        {menu.map((item) => {
          const hasChildren = !!item.children;
          const isOpen = openKeys.includes(item.key);
          const isActive = location.pathname === item.key || (hasChildren && item.children!.some((c) => c.key === location.pathname));

          if (collapsed) {
            return (
              <NavLink
                key={item.key}
                to={item.key}
                onClick={onNavigate}
                className={({ isActive }) =>
                  `flex items-center justify-center h-10 mx-2 rounded mb-1 transition-colors ${
                    isActive ? 'bg-primary text-white' : 'text-text-secondary hover:bg-black/5 dark:hover:bg-white/10'
                  }`
                }
                title={item.label}
              >
                {iconMap[item.icon || '']}
              </NavLink>
            );
          }

          return (
            <div key={item.key} className="mb-1">
              {hasChildren ? (
                <>
                  <button
                    onClick={() => toggle(item.key)}
                    className={`w-full flex items-center justify-between px-4 h-9 text-sm transition-colors ${
                      isActive ? 'text-primary font-medium' : 'text-text-secondary hover:text-text hover:bg-black/5 dark:hover:bg-white/10'
                    }`}
                  >
                    <span className="flex items-center gap-3">
                      {iconMap[item.icon || '']}
                      {item.label}
                    </span>
                    <span className={`transform transition-transform ${isOpen ? 'rotate-90' : ''}`}>›</span>
                  </button>
                  {isOpen && (
                    <div className="pl-10 pr-2">
                      {item.children!.map((child) => (
                        <NavLink
                          key={child.key}
                          to={child.key}
                          onClick={onNavigate}
                          className={({ isActive }) =>
                            `block py-2 px-3 text-sm rounded mb-0.5 transition-colors ${
                              isActive ? 'bg-primary/10 text-primary font-medium' : 'text-text-secondary hover:bg-black/5 dark:hover:bg-white/10'
                            }`
                          }
                        >
                          {child.label}
                        </NavLink>
                      ))}
                    </div>
                  )}
                </>
              ) : (
                <NavLink
                  to={item.key}
                  onClick={onNavigate}
                  className={({ isActive }) =>
                    `flex items-center gap-3 px-4 h-9 text-sm transition-colors ${
                      isActive ? 'bg-primary text-white rounded mx-2' : 'text-text-secondary hover:text-text hover:bg-black/5 dark:hover:bg-white/10 rounded mx-2'
                    }`
                  }
                >
                  {iconMap[item.icon || '']}
                  {item.label}
                </NavLink>
              )}
            </div>
          );
        })}
      </nav>
    </aside>
  );
}

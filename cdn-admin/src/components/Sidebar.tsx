import { useState } from 'react';
import { NavLink, useLocation } from 'react-router-dom';
import type { Role } from '../types';
import { sMenu, bMenu } from './menu';
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

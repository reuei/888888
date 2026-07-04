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
  ChevronRight,
} from 'lucide-react';

const iconMap: Record<string, React.ReactNode> = {
  LayoutDashboard: <LayoutDashboard size={17} />,
  Globe: <Globe size={17} />,
  Users: <Users size={17} />,
  Package: <Package size={17} />,
  ShoppingCart: <ShoppingCart size={17} />,
  CreditCard: <CreditCard size={17} />,
  FileText: <FileText size={17} />,
  Settings: <Settings size={17} />,
  Image: <Image size={17} />,
  Ticket: <Ticket size={17} />,
  Server: <Server size={17} />,
  Shield: <Shield size={17} />,
  Monitor: <Monitor size={17} />,
  Smartphone: <Smartphone size={17} />,
  BarChart3: <BarChart3 size={17} />,
  Wallet: <Wallet size={17} />,
  UserCog: <UserCog size={17} />,
  UserCircle: <UserCircle size={17} />,
  GitBranch: <GitBranch size={17} />,
  ClipboardList: <ClipboardList size={17} />,
  FileCode: <FileCode size={17} />,
  ShieldCheck: <ShieldCheck size={17} />,
  Database: <Database size={17} />,
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
      <div className="h-14 flex items-center justify-center border-b border-border">
        {collapsed ? (
          <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-primary to-primary-dark text-white flex items-center justify-center">
            <Shield size={16} />
          </div>
        ) : (
          <div className="flex items-center gap-2">
            <div className="w-7 h-7 rounded-lg bg-gradient-to-br from-primary to-primary-dark text-white flex items-center justify-center">
              <Shield size={15} />
            </div>
            <span className="font-bold text-text">EdgeOne</span>
          </div>
        )}
      </div>
      <nav className="flex-1 overflow-y-auto py-3 px-2">
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
                  `flex items-center justify-center h-10 mx-1 rounded-lg mb-1 transition-all ${
                    isActive
                      ? 'bg-primary text-white shadow-md shadow-primary/25'
                      : 'text-text-secondary hover:bg-hover-bg hover:text-text'
                  }`
                }
                title={item.label}
              >
                {iconMap[item.icon || '']}
              </NavLink>
            );
          }

          return (
            <div key={item.key} className="mb-0.5">
              {hasChildren ? (
                <>
                  <button
                    onClick={() => toggle(item.key)}
                    className={`w-full flex items-center justify-between px-3 h-10 text-sm rounded-lg transition-all ${
                      isActive
                        ? 'text-primary font-medium bg-primary/5'
                        : 'text-text-secondary hover:text-text hover:bg-hover-bg'
                    }`}
                  >
                    <span className="flex items-center gap-3">
                      {iconMap[item.icon || '']}
                      {item.label}
                    </span>
                    <ChevronRight size={14} className={`transform transition-transform ${isOpen ? 'rotate-90' : ''}`} />
                  </button>
                  {isOpen && (
                    <div className="pl-10 pr-2 mt-1 space-y-1">
                      {item.children!.map((child) => (
                        <NavLink
                          key={child.key}
                          to={child.key}
                          onClick={onNavigate}
                          className={({ isActive }) =>
                            `block py-2 px-3 text-sm rounded-md transition-all border-l-2 ${
                              isActive
                                ? 'border-primary bg-primary/5 text-primary font-medium'
                                : 'border-transparent text-text-secondary hover:bg-hover-bg hover:text-text'
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
                    `flex items-center gap-3 px-3 h-10 text-sm rounded-lg transition-all ${
                      isActive
                        ? 'bg-primary text-white shadow-md shadow-primary/25 font-medium'
                        : 'text-text-secondary hover:bg-hover-bg hover:text-text'
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

import { useState } from 'react';
import { NavLink, useNavigate } from 'react-router-dom';
import { ShoppingBag, Server, Download, User, Bell, Menu, X, LogOut, Home } from 'lucide-react';

interface SalesHeaderProps {
  loggedIn?: boolean;
  onLogout?: () => void;
}

const navItems = [
  { key: '/', label: '首页', icon: Home },
  { key: '/buy-source', label: '源码购买', icon: ShoppingBag },
  { key: '/buy-nodes', label: '节点购买', icon: Server },
  { key: '/updates', label: '在线更新', icon: Download },
  { key: '/announcements', label: '公告', icon: Bell },
];

export default function SalesHeader({ loggedIn = false, onLogout }: SalesHeaderProps) {
  const navigate = useNavigate();
  const [mobileOpen, setMobileOpen] = useState(false);

  return (
    <header className="sticky top-0 z-50 border-b border-[var(--sales-border)] bg-[var(--sales-card)]/80 backdrop-blur">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <button
          onClick={() => navigate('/')}
          className="flex items-center gap-2 group"
        >
          <div className="w-9 h-9 rounded-xl bg-gradient-to-br from-[var(--sales-primary)] to-[var(--sales-accent)] text-white flex items-center justify-center shadow-lg shadow-[var(--sales-primary)]/30 group-hover:scale-105 transition-transform">
            <ShoppingBag size={18} />
          </div>
          <span className="text-lg font-bold bg-clip-text text-transparent bg-gradient-to-r from-[var(--sales-primary)] to-[var(--sales-accent)]">
            CloudShield Store
          </span>
        </button>

        <nav className="hidden md:flex items-center gap-1">
          {navItems.map((item) => (
            <NavLink
              key={item.key}
              to={item.key}
              className={({ isActive }) =>
                `flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-all ${
                  isActive
                    ? 'text-[var(--sales-primary)] bg-[var(--sales-primary)]/10'
                    : 'text-[var(--sales-text-secondary)] hover:text-[var(--sales-text)] hover:bg-black/5 dark:hover:bg-white/5'
                }`
              }
            >
              <item.icon size={16} />
              {item.label}
            </NavLink>
          ))}
        </nav>

        <div className="hidden md:flex items-center gap-3">
          {loggedIn ? (
            <>
              <NavLink
                to="/user"
                className={({ isActive }) =>
                  `flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-all ${
                    isActive
                      ? 'text-[var(--sales-primary)] bg-[var(--sales-primary)]/10'
                      : 'text-[var(--sales-text-secondary)] hover:text-[var(--sales-text)]'
                  }`
                }
              >
                <User size={16} />
                用户中心
              </NavLink>
              <button
                onClick={onLogout}
                className="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-danger hover:bg-danger/10 transition-colors"
              >
                <LogOut size={16} />
                退出
              </button>
            </>
          ) : (
            <>
              <button
                onClick={() => navigate('/login')}
                className="px-4 py-2 rounded-lg text-sm font-medium text-[var(--sales-text-secondary)] hover:text-[var(--sales-text)] transition-colors"
              >
                登录
              </button>
              <button
                onClick={() => navigate('/login')}
                className="px-4 py-2 rounded-lg text-sm font-medium text-white bg-gradient-to-r from-[var(--sales-primary)] to-[var(--sales-accent)] shadow-lg shadow-[var(--sales-primary)]/30 hover:shadow-[var(--sales-primary)]/50 hover:-translate-y-0.5 transition-all"
              >
                注册
              </button>
            </>
          )}
        </div>

        <button
          className="md:hidden p-2 rounded-lg text-[var(--sales-text-secondary)] hover:bg-black/5 dark:hover:bg-white/5"
          onClick={() => setMobileOpen(!mobileOpen)}
        >
          {mobileOpen ? <X size={20} /> : <Menu size={20} />}
        </button>
      </div>

      {mobileOpen && (
        <div className="md:hidden border-t border-[var(--sales-border)] bg-[var(--sales-card)]">
          <nav className="px-4 py-3 space-y-1">
            {navItems.map((item) => (
              <NavLink
                key={item.key}
                to={item.key}
                onClick={() => setMobileOpen(false)}
                className={({ isActive }) =>
                  `flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium ${
                    isActive
                      ? 'text-[var(--sales-primary)] bg-[var(--sales-primary)]/10'
                      : 'text-[var(--sales-text-secondary)]'
                  }`
                }
              >
                <item.icon size={16} />
                {item.label}
              </NavLink>
            ))}
            {loggedIn ? (
              <>
                <NavLink
                  to="/user"
                  onClick={() => setMobileOpen(false)}
                  className="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-[var(--sales-text-secondary)]"
                >
                  <User size={16} />
                  用户中心
                </NavLink>
                <button
                  onClick={() => {
                    setMobileOpen(false);
                    onLogout?.();
                  }}
                  className="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-danger"
                >
                  <LogOut size={16} />
                  退出
                </button>
              </>
            ) : (
              <button
                onClick={() => {
                  setMobileOpen(false);
                  navigate('/login');
                }}
                className="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-[var(--sales-primary)]"
              >
                <User size={16} />
                登录 / 注册
              </button>
            )}
          </nav>
        </div>
      )}
    </header>
  );
}

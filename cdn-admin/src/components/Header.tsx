import { useState, useRef, useEffect } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { Menu, Bell, Search, LogOut, RefreshCcw, Check, Settings, Sun, Moon, Shield, ChevronDown } from 'lucide-react';
import type { Role } from '../types';
import { sProfile, bProfile, notifications as initialNotifications } from '../data/mock';
import SearchModal from './SearchModal';

interface HeaderProps {
  role: Role;
  collapsed: boolean;
  dark: boolean;
  onToggle: () => void;
  onMobileToggle: () => void;
  onThemeToggle: () => void;
  onSwitchRole: () => void;
  onLogout: () => void;
}

const typeColor: Record<string, string> = {
  system: 'bg-primary',
  order: 'bg-success',
  alert: 'bg-danger',
  finance: 'bg-warning',
};

export default function Header({
  role,
  dark,
  onToggle,
  onMobileToggle,
  onThemeToggle,
  onSwitchRole,
  onLogout,
}: HeaderProps) {
  const profile = role === 's' ? sProfile : bProfile;
  const [searchOpen, setSearchOpen] = useState(false);
  const [notifyOpen, setNotifyOpen] = useState(false);
  const [userOpen, setUserOpen] = useState(false);
  const [notifications, setNotifications] = useState(initialNotifications);
  const notifyRef = useRef<HTMLDivElement>(null);
  const userRef = useRef<HTMLDivElement>(null);
  const navigate = useNavigate();

  const unreadCount = notifications.filter((n) => !n.read).length;

  useEffect(() => {
    const handleKey = (e: KeyboardEvent) => {
      if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        setSearchOpen((v) => !v);
      }
      if (e.key === 'Escape') {
        setSearchOpen(false);
        setNotifyOpen(false);
        setUserOpen(false);
      }
    };
    window.addEventListener('keydown', handleKey);
    return () => window.removeEventListener('keydown', handleKey);
  }, []);

  useEffect(() => {
    const handleClick = (e: MouseEvent) => {
      if (notifyRef.current && !notifyRef.current.contains(e.target as Node)) {
        setNotifyOpen(false);
      }
      if (userRef.current && !userRef.current.contains(e.target as Node)) {
        setUserOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClick);
    return () => document.removeEventListener('mousedown', handleClick);
  }, []);

  const markRead = (id: string) => {
    setNotifications((prev) => prev.map((n) => (n.id === id ? { ...n, read: true } : n)));
  };

  const markAllRead = () => {
    setNotifications((prev) => prev.map((n) => ({ ...n, read: true })));
  };

  const handleNotifyClick = (n: (typeof notifications)[0]) => {
    markRead(n.id);
    setNotifyOpen(false);
    if (n.link && n.link.startsWith(`/${role}`)) {
      navigate(n.link);
    }
  };

  return (
    <>
      <header className="h-14 bg-card border-b border-border flex items-center justify-between px-4 shrink-0">
        <div className="flex items-center gap-3">
          <button
            onClick={onMobileToggle}
            className="md:hidden p-2 rounded-lg hover:bg-black/5 dark:hover:bg-white/10 text-text-secondary transition-colors"
          >
            <Menu size={18} />
          </button>
          <button
            onClick={onToggle}
            className="hidden md:block p-2 rounded-lg hover:bg-black/5 dark:hover:bg-white/10 text-text-secondary transition-colors"
          >
            <Menu size={18} />
          </button>
          <div className="flex items-center gap-2.5">
            <div className="w-7 h-7 rounded-lg bg-gradient-to-br from-primary to-primary-dark text-white flex items-center justify-center">
              <Shield size={16} />
            </div>
            <span className="font-semibold text-text">EdgeOne 控制台</span>
          </div>
          {role === 'b' && profile.shopName && (
            <span className="text-sm text-text-secondary ml-2 hidden sm:inline">| {profile.shopName}</span>
          )}
        </div>

        <div className="flex items-center gap-1 md:gap-2">
          <button
            onClick={() => setSearchOpen(true)}
            className="hidden md:flex items-center gap-2 h-9 px-3 rounded-full border border-border bg-hover-bg text-xs text-text-secondary hover:border-primary/40 hover:text-primary transition-colors"
          >
            <Search size={14} /> 全站搜索 <span className="text-[10px] opacity-60">⌘K</span>
          </button>

          {role === 'b' && (
            <div className="text-sm hidden sm:block px-2">
              余额：<span className="text-danger font-medium">¥{profile.balance.toLocaleString('zh-CN')}</span>
            </div>
          )}

          <div className="relative" ref={notifyRef}>
            <button
              onClick={() => setNotifyOpen((v) => !v)}
              className="relative p-2 rounded-lg hover:bg-black/5 dark:hover:bg-white/10 text-text-secondary transition-colors"
            >
              <Bell size={18} />
              {unreadCount > 0 && (
                <span className="absolute top-1.5 right-1.5 w-2 h-2 bg-danger rounded-full ring-2 ring-card" />
              )}
            </button>
            {notifyOpen && (
              <div className="absolute right-0 top-full mt-2 w-80 bg-card border border-border rounded-xl shadow-lg z-40 overflow-hidden">
                <div className="flex items-center justify-between px-4 h-11 border-b border-border">
                  <span className="text-sm font-medium">消息通知</span>
                  <button onClick={markAllRead} className="text-xs text-primary hover:underline">
                    全部已读
                  </button>
                </div>
                <div className="max-h-[320px] overflow-y-auto">
                  {notifications.length === 0 ? (
                    <div className="px-4 py-8 text-center text-sm text-text-secondary">暂无通知</div>
                  ) : (
                    notifications.map((n) => (
                      <div
                        key={n.id}
                        onClick={() => handleNotifyClick(n)}
                        className={`px-4 py-3 border-b border-border last:border-0 cursor-pointer hover:bg-hover-bg transition-colors ${n.read ? 'opacity-70' : ''}`}
                      >
                        <div className="flex items-start gap-2">
                          <span className={`w-2 h-2 rounded-full mt-1.5 shrink-0 ${typeColor[n.type]}`}></span>
                          <div className="flex-1 min-w-0">
                            <div className="text-sm font-medium truncate">{n.title}</div>
                            <div className="text-xs text-text-secondary line-clamp-2 mt-0.5">{n.content}</div>
                            <div className="text-xs text-text-secondary mt-1">{n.createdAt}</div>
                          </div>
                          {!n.read && (
                            <button
                              onClick={(e) => {
                                e.stopPropagation();
                                markRead(n.id);
                              }}
                              className="p-1 rounded hover:bg-black/5 dark:hover:bg-white/10 text-success"
                              title="标记已读"
                            >
                              <Check size={12} />
                            </button>
                          )}
                        </div>
                      </div>
                    ))
                  )}
                </div>
                <div className="px-4 py-2 border-t border-border text-center">
                  <Link
                    to={`/${role}/notifications`}
                    onClick={() => setNotifyOpen(false)}
                    className="text-xs text-primary hover:underline"
                  >
                    查看全部通知
                  </Link>
                </div>
              </div>
            )}
          </div>

          <button
            onClick={onThemeToggle}
            className="p-2 rounded-lg hover:bg-black/5 dark:hover:bg-white/10 text-text-secondary transition-colors"
            title={dark ? '切换亮色' : '切换暗色'}
          >
            {dark ? <Sun size={17} /> : <Moon size={17} />}
          </button>

          <button
            onClick={onSwitchRole}
            className="hidden sm:flex p-2 rounded-lg hover:bg-black/5 dark:hover:bg-white/10 text-text-secondary transition-colors"
            title="切换角色"
          >
            <RefreshCcw size={17} />
          </button>

          <div className="relative" ref={userRef}>
            <button
              onClick={() => setUserOpen((v) => !v)}
              className="flex items-center gap-2 pl-2 md:pl-3 ml-1 border-l border-border hover:opacity-90 transition-opacity"
            >
              <div className="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-primary-dark text-white flex items-center justify-center text-xs font-medium">
                {profile.avatar}
              </div>
              <span className="text-sm hidden lg:inline text-text">{profile.name}</span>
              <ChevronDown size={14} className="text-text-secondary hidden lg:inline" />
            </button>

            {userOpen && (
              <div className="absolute right-0 top-full mt-2 w-48 bg-card border border-border rounded-xl shadow-lg z-40 overflow-hidden">
                <div className="px-4 py-3 border-b border-border">
                  <div className="text-sm font-medium text-text">{profile.name}</div>
                  <div className="text-xs text-text-secondary">{role === 's' ? 'S 端总站长' : 'B 端商户'}</div>
                </div>
                <Link
                  to={`/${role}/profile`}
                  onClick={() => setUserOpen(false)}
                  className="flex items-center gap-2 px-4 py-2.5 text-sm text-text-secondary hover:bg-hover-bg hover:text-text transition-colors"
                >
                  <Settings size={15} /> 个人设置
                </Link>
                <Link
                  to={`/${role}/settings`}
                  onClick={() => setUserOpen(false)}
                  className="flex items-center gap-2 px-4 py-2.5 text-sm text-text-secondary hover:bg-hover-bg hover:text-text transition-colors"
                >
                  <Settings size={15} /> 系统设置
                </Link>
                <button
                  onClick={() => {
                    setUserOpen(false);
                    onLogout();
                  }}
                  className="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-danger hover:bg-danger/5 transition-colors"
                >
                  <LogOut size={15} /> 退出登录
                </button>
              </div>
            )}
          </div>
        </div>
      </header>
      <SearchModal open={searchOpen} role={role} onClose={() => setSearchOpen(false)} />
    </>
  );
}

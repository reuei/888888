import { useState, useRef, useEffect } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { Menu, Bell, Search, LogOut, RefreshCcw, Check, Settings } from 'lucide-react';
import type { Role } from '../types';
import { sProfile, bProfile, notifications as initialNotifications } from '../data/mock';
import SearchModal from './SearchModal';

interface HeaderProps {
  role: Role;
  collapsed: boolean;
  onToggle: () => void;
  onSwitchRole: () => void;
  onLogout: () => void;
}

const typeColor: Record<string, string> = {
  system: 'bg-primary',
  order: 'bg-success',
  alert: 'bg-danger',
  finance: 'bg-warning',
};

export default function Header({ role, onToggle, onSwitchRole, onLogout }: HeaderProps) {
  const profile = role === 's' ? sProfile : bProfile;
  const [searchOpen, setSearchOpen] = useState(false);
  const [notifyOpen, setNotifyOpen] = useState(false);
  const [notifications, setNotifications] = useState(initialNotifications);
  const notifyRef = useRef<HTMLDivElement>(null);
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

  const handleNotifyClick = (n: typeof notifications[0]) => {
    markRead(n.id);
    setNotifyOpen(false);
    if (n.link && n.link.startsWith(`/${role}`)) {
      navigate(n.link);
    }
  };

  return (
    <>
      <header className="h-12 bg-card border-b border-border flex items-center justify-between px-4 shrink-0">
        <div className="flex items-center gap-3">
          <button onClick={onToggle} className="p-1.5 rounded hover:bg-gray-100 text-text-secondary">
            <Menu size={18} />
          </button>
          <span className="font-semibold text-primary">CDN 防护加速平台</span>
          {role === 'b' && profile.shopName && (
            <span className="text-sm text-text-secondary ml-2">| {profile.shopName}</span>
          )}
        </div>
        <div className="flex items-center gap-4">
          <button
            onClick={() => setSearchOpen(true)}
            className="hidden md:flex items-center gap-2 h-8 px-3 rounded border border-border text-xs text-text-secondary hover:border-primary hover:text-primary transition-colors"
          >
            <Search size={14} /> 全站搜索 <span className="text-[10px] opacity-60">⌘K</span>
          </button>
          {role === 'b' && (
            <div className="text-sm">
              余额：<span className="text-danger font-medium">¥{profile.balance.toLocaleString('zh-CN')}</span>
            </div>
          )}
          <div className="relative" ref={notifyRef}>
            <button
              onClick={() => setNotifyOpen((v) => !v)}
              className="relative p-1.5 rounded hover:bg-gray-100 text-text-secondary"
            >
              <Bell size={18} />
              {unreadCount > 0 && (
                <span className="absolute top-1 right-1 w-2 h-2 bg-danger rounded-full"></span>
              )}
            </button>
            {notifyOpen && (
              <div className="absolute right-0 top-full mt-2 w-80 bg-card border border-border rounded shadow-lg z-40">
                <div className="flex items-center justify-between px-4 h-10 border-b border-border">
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
                        className={`px-4 py-3 border-b border-border last:border-0 cursor-pointer hover:bg-gray-50 ${n.read ? 'opacity-70' : ''}`}
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
                              className="p-1 rounded hover:bg-gray-100 text-success"
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
          <button onClick={onSwitchRole} className="p-1.5 rounded hover:bg-gray-100 text-text-secondary" title="切换角色">
            <RefreshCcw size={16} />
          </button>
          <div className="flex items-center gap-2 pl-3 border-l border-border">
            <div className="w-7 h-7 rounded-full bg-primary text-white flex items-center justify-center text-xs font-medium">
              {profile.avatar}
            </div>
            <span className="text-sm hidden sm:inline">{profile.name}</span>
            <Link to={`/${role}/settings`} className="p-1.5 rounded hover:bg-gray-100 text-text-secondary" title="设置">
              <Settings size={16} />
            </Link>
            <button onClick={onLogout} className="p-1.5 rounded hover:bg-gray-100 text-text-secondary" title="退出">
              <LogOut size={16} />
            </button>
          </div>
        </div>
      </header>
      <SearchModal open={searchOpen} role={role} onClose={() => setSearchOpen(false)} />
    </>
  );
}

import { Menu, Bell, Search, LogOut, RefreshCcw } from 'lucide-react';
import type { Role } from '../types';
import { sProfile, bProfile } from '../data/mock';

interface HeaderProps {
  role: Role;
  collapsed: boolean;
  onToggle: () => void;
  onSwitchRole: () => void;
  onLogout: () => void;
}

export default function Header({ role, onToggle, onSwitchRole, onLogout }: HeaderProps) {
  const profile = role === 's' ? sProfile : bProfile;

  return (
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
        <div className="relative hidden md:block">
          <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
          <input
            type="text"
            placeholder="全站搜索..."
            className="input pl-8 h-8 w-64 text-xs"
          />
        </div>
        {role === 'b' && (
          <div className="text-sm">
            余额：<span className="text-danger font-medium">¥{profile.balance.toLocaleString('zh-CN')}</span>
          </div>
        )}
        <button className="relative p-1.5 rounded hover:bg-gray-100 text-text-secondary">
          <Bell size={18} />
          <span className="absolute top-1 right-1 w-2 h-2 bg-danger rounded-full"></span>
        </button>
        <button onClick={onSwitchRole} className="p-1.5 rounded hover:bg-gray-100 text-text-secondary" title="切换角色">
          <RefreshCcw size={16} />
        </button>
        <div className="flex items-center gap-2 pl-3 border-l border-border">
          <div className="w-7 h-7 rounded-full bg-primary text-white flex items-center justify-center text-xs font-medium">
            {profile.avatar}
          </div>
          <span className="text-sm hidden sm:inline">{profile.name}</span>
          <button onClick={onLogout} className="p-1.5 rounded hover:bg-gray-100 text-text-secondary" title="退出">
            <LogOut size={16} />
          </button>
        </div>
      </div>
    </header>
  );
}

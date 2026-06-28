import { useState, useEffect } from 'react';
import type { Role } from '../types';
import Header from './Header';
import Sidebar from './Sidebar';

interface LayoutProps {
  role: Role;
  children: React.ReactNode;
  onSwitchRole: () => void;
  onLogout: () => void;
}

export default function Layout({ role, children, onSwitchRole, onLogout }: LayoutProps) {
  const [collapsed, setCollapsed] = useState(false);
  const [mobileOpen, setMobileOpen] = useState(false);
  const [dark, setDark] = useState(() => {
    if (typeof window === 'undefined') return false;
    return localStorage.getItem('theme') === 'dark' ||
      (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
  });

  useEffect(() => {
    if (dark) {
      document.documentElement.classList.add('dark');
      localStorage.setItem('theme', 'dark');
    } else {
      document.documentElement.classList.remove('dark');
      localStorage.setItem('theme', 'light');
    }
  }, [dark]);

  return (
    <div className="flex h-screen bg-bg">
      {/* Desktop sidebar */}
      <div className="hidden md:block shrink-0">
        <Sidebar role={role} collapsed={collapsed} />
      </div>

      {/* Mobile sidebar overlay */}
      {mobileOpen && (
        <>
          <div
            className="fixed inset-0 bg-black/40 z-40 md:hidden"
            onClick={() => setMobileOpen(false)}
          />
          <div className="fixed left-0 top-0 h-full z-50 md:hidden">
            <Sidebar role={role} collapsed={false} onNavigate={() => setMobileOpen(false)} />
          </div>
        </>
      )}

      <div className="flex flex-col flex-1 min-w-0">
        <Header
          role={role}
          collapsed={collapsed}
          dark={dark}
          onToggle={() => setCollapsed(!collapsed)}
          onMobileToggle={() => setMobileOpen(true)}
          onThemeToggle={() => setDark(!dark)}
          onSwitchRole={onSwitchRole}
          onLogout={onLogout}
        />
        <main className="flex-1 overflow-auto p-4 md:p-6">
          {children}
          <footer className="mt-8 text-xs text-text-secondary text-center py-2 border-t border-border">
            CDN 防护加速平台 v1.0.0 · © 2026 企业级CDN · 帮助文档
          </footer>
        </main>
      </div>
    </div>
  );
}

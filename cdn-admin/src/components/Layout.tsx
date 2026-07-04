import { useEffect } from 'react';
import type { Role } from '../types';
import Sidebar from './Sidebar';
import Header from './Header';
import { ToastProvider } from './Toast';
import { useLocalStorage } from '../hooks/useLocalStorage';

interface LayoutProps {
  role: Role;
  children: React.ReactNode;
  onSwitchRole: () => void;
  onLogout: () => void;
}

export default function Layout({ role, children, onSwitchRole, onLogout }: LayoutProps) {
  const [collapsed, setCollapsed] = useLocalStorage('sidebar-collapsed', false);
  const [mobileOpen, setMobileOpen] = useLocalStorage('sidebar-mobile-open', false);
  const [dark, setDark] = useLocalStorage('theme', () => {
    if (typeof window === 'undefined') return false;
    return window.matchMedia('(prefers-color-scheme: dark)').matches;
  });

  useEffect(() => {
    if (dark) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  }, [dark]);

  return (
    <ToastProvider>
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

        <div className="flex flex-col flex-1 min-w-0 shadow-[-1px_0_0_0_var(--color-border)]">
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
            <div className="min-h-[calc(100%-3rem)]">
              {children}
            </div>
            <footer className="mt-8 text-xs text-text-secondary text-center py-4 border-t border-border bg-card/50 rounded-lg">
              EdgeOne 控制台 v1.0.0 · © 2026 企业级 CDN · 帮助文档
            </footer>
          </main>
        </div>
      </div>
    </ToastProvider>
  );
}
